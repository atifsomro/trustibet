<?php

namespace Tests\Feature;

use App\Actions\Lottery\BuyLotteryTicketsAction;
use App\Actions\Lottery\DrawLotteryAction;
use App\Enums\LotteryStatus;
use App\Models\Lottery;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\LotteryNewRoundNotification;
use App\Notifications\LotteryResultNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LotteryCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_lottery_still_allows_ticket_purchase(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'is_active' => false,
            'duration_seconds' => 3600,
        ]);

        $tickets = app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 2);

        $this->assertCount(2, $tickets);
        $this->assertSame(2, $lottery->fresh()->totalCurrentRoundTickets());
    }

    public function test_purchase_is_blocked_after_ends_at(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'duration_seconds' => 60,
        ]);
        $lottery->starts_at = now()->subMinutes(5);
        $lottery->recalculateEndsAt();
        $lottery->save();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);
    }

    public function test_per_lottery_ticket_limit_is_enforced(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'max_tickets_per_user' => 1,
            'duration_seconds' => 3600,
        ]);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery->fresh(), 1);
    }

    public function test_inactive_draw_selects_internal_winners_but_pays_nobody(): void
    {
        Notification::fake();

        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'is_active' => false,
            'duration_seconds' => 60,
        ]);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);

        $lottery->ends_at = now();
        $lottery->sales_end_at = $lottery->ends_at;
        $lottery->draw_at = $lottery->ends_at;
        $lottery->status = LotteryStatus::ENDED;
        $lottery->save();

        $draw = app(DrawLotteryAction::class)->execute($lottery->fresh());

        $this->assertFalse($draw->winners_announced);
        $this->assertSame(0, $draw->total_winners);
        $this->assertGreaterThan(0, $draw->winners()->count());
        $this->assertSame('suppressed', $draw->winners()->first()->payout_status);
        $this->assertSame('lost', LotteryTicket::query()->first()->status);
        $this->assertEquals(99.0, (float) $user->wallet()->first()->withdrawable_balance);

        Notification::assertSentTo($user, LotteryResultNotification::class, function (LotteryResultNotification $notification) {
            return $notification->result === 'lost';
        });
        Notification::assertSentTo($user, LotteryNewRoundNotification::class);
    }

    public function test_active_draw_pays_winner_and_notifies(): void
    {
        Notification::fake();

        $winner = $this->makeUserWithBalance(100);
        $loser = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'is_active' => true,
            'duration_seconds' => 60,
        ]);

        app(BuyLotteryTicketsAction::class)->execute($winner, $lottery, 1);
        app(BuyLotteryTicketsAction::class)->execute($loser, $lottery, 1);

        $lottery->ends_at = now();
        $lottery->sales_end_at = $lottery->ends_at;
        $lottery->draw_at = $lottery->ends_at;
        $lottery->status = LotteryStatus::ENDED;
        $lottery->save();

        $draw = app(DrawLotteryAction::class)->execute($lottery->fresh());

        $this->assertTrue($draw->winners_announced);
        $this->assertGreaterThan(0, $draw->total_winners);

        Notification::assertSentTo($winner, LotteryResultNotification::class);
        Notification::assertSentTo($loser, LotteryResultNotification::class);
        Notification::assertSentTo($winner, LotteryNewRoundNotification::class);
        Notification::assertSentTo($loser, LotteryNewRoundNotification::class);
    }

    public function test_draw_due_command_draws_ended_lotteries(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'duration_seconds' => 60,
        ]);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);

        $lottery->ends_at = now();
        $lottery->sales_end_at = $lottery->ends_at;
        $lottery->draw_at = $lottery->ends_at;
        $lottery->status = LotteryStatus::ENDED;
        $lottery->save();

        $this->artisan('lottery:draw-due')->assertSuccessful();

        $lottery = $lottery->fresh();

        $this->assertTrue($lottery->isSelling());
        $this->assertTrue($lottery->ends_at->isFuture());
        $this->assertDatabaseHas('lottery_draws', [
            'lottery_id' => $lottery->id,
            'status' => 'completed',
        ]);
        $this->assertGreaterThan(0, $lottery->winners()->count());
    }

    public function test_frontend_draw_due_endpoint_draws_when_countdown_has_ended(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'duration_seconds' => 60,
        ]);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);

        $lottery->ends_at = now();
        $lottery->sales_end_at = $lottery->ends_at;
        $lottery->draw_at = $lottery->ends_at;
        $lottery->status = LotteryStatus::ENDED;
        $lottery->save();

        $response = $this->actingAs($user)->postJson(route('lotteries.draw-due', $lottery));

        $response->assertOk()
            ->assertJson(['drawn' => true]);

        $lottery = $lottery->fresh();

        $this->assertTrue($lottery->isSelling());
        $this->assertTrue($lottery->ends_at->isFuture());
    }

    public function test_draw_restarts_countdown_and_allows_a_new_round_purchase(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'max_tickets_per_user' => 1,
            'duration_seconds' => 60,
        ]);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);

        $lottery->ends_at = now();
        $lottery->sales_end_at = $lottery->ends_at;
        $lottery->draw_at = $lottery->ends_at;
        $lottery->status = LotteryStatus::ENDED;
        $lottery->save();

        app(DrawLotteryAction::class)->execute($lottery->fresh());

        $lottery = $lottery->fresh();

        $this->assertTrue($lottery->isSelling());
        $this->assertSame(0, $lottery->ticketsForCurrentRoundUser($user->id));

        $tickets = app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);

        $this->assertCount(1, $tickets);
        $this->assertSame(1, $lottery->fresh()->ticketsForCurrentRoundUser($user->id));
    }

    public function test_listing_shows_one_result_card_per_lottery_not_per_round(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery(['title' => 'Grouped Results Lottery']);

        $this->makeCompletedDraw($lottery, now()->subHours(2));
        $this->makeCompletedDraw($lottery, now()->subHour());

        $response = $this->actingAs($user)->get(route('lotteries.index'));

        $response->assertOk();
        $response->assertSee('Grouped Results Lottery');
        $response->assertSee('2 rounds completed');
        $response->assertSee(route('lotteries.results', $lottery), false);
        $this->assertSame(1, substr_count($response->getContent(), 'View Results'));
    }

    public function test_lottery_results_page_lists_every_completed_round(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery(['title' => 'Results Page Lottery']);

        $this->makeCompletedDraw($lottery, now()->subHours(2));
        $this->makeCompletedDraw($lottery, now()->subHour());

        $response = $this->actingAs($user)->get(route('lotteries.results', $lottery));

        $response->assertOk();
        $response->assertSee('Results Page Lottery');
        $response->assertSee('Round 1');
        $response->assertSee('Round 2');
        $response->assertSee('2 completed rounds');
    }

    public function test_lottery_pages_show_used_and_remaining_balance(): void
    {
        $user = $this->makeUserWithBalance(100);
        $lottery = $this->makeLottery([
            'title' => 'Balance Lottery',
            'ticket_price' => 15,
        ]);

        app(BuyLotteryTicketsAction::class)->execute($user, $lottery, 1);

        $index = $this->actingAs($user->fresh())->get(route('lotteries.index'));
        $index->assertOk();
        $index->assertSee('Used');
        $index->assertSee('Remaining');
        $index->assertSee('15.00');
        $index->assertSee('85.00');

        $show = $this->actingAs($user->fresh())->get(route('lotteries.show', $lottery));
        $show->assertOk();
        $show->assertSee('Used');
        $show->assertSee('Remaining');
        $show->assertSee('15.00');
        $show->assertSee('85.00');
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function makeLottery(array $overrides = []): Lottery
    {
        $lottery = new Lottery(array_merge([
            'title' => 'Test Draw',
            'ticket_price' => 1,
            'currency' => 'USD',
            'duration_seconds' => 3600,
            'first_prize' => 10,
            'second_prize' => 5,
            'third_prize' => 3,
            'fourth_prize' => 2,
            'fifth_prize' => 1,
            'status' => LotteryStatus::SELLING,
            'is_active' => true,
            'sales_end_at' => now()->addHour(),
        ], $overrides));

        $lottery->applyCountdown();
        $lottery->save();

        return $lottery->fresh();
    }

    protected function makeUserWithBalance(float $balance): User
    {
        $user = User::factory()->create();

        Wallet::query()->create([
            'user_id' => $user->id,
            'withdrawable_balance' => $balance,
            'bonus_balance' => 0,
            'locked_balance' => 0,
            'currency' => 'USD',
        ]);

        return $user->fresh();
    }

    protected function makeCompletedDraw(Lottery $lottery, $completedAt): LotteryDraw
    {
        return LotteryDraw::query()->create([
            'lottery_id' => $lottery->id,
            'status' => 'completed',
            'winners_announced' => true,
            'total_tickets' => 1,
            'total_winners' => 0,
            'completed_at' => $completedAt,
        ]);
    }
}
