<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function walletFor(User $user, float $withdrawable = 100.0): Wallet
    {
        return Wallet::query()->create([
            'user_id' => $user->id,
            'withdrawable_balance' => $withdrawable,
            'bonus_balance' => 0,
            'locked_balance' => 0,
            'roi_balance' => 0,
            'currency' => 'USD',
            'version' => 1,
        ]);
    }

    public function test_withdrawal_create_page_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->walletFor($user);

        $this->actingAs($user)
            ->get(route('wallet.withdrawals.create'))
            ->assertOk()
            ->assertSee('Withdrawal Amount')
            ->assertSee('Payment Method')
            ->assertSee('Account Title')
            ->assertSee('Account Number');
    }

    public function test_bank_transfer_withdrawal_is_stored_with_structured_account_details(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletFor($user, 100.0);

        $response = $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '25',
            'payment_method' => 'bank_transfer',
            'account_title' => 'John Doe',
            'account_number' => 'PK00HABB0000000000000000',
            'bank_name' => 'Habib Bank Limited',
            'remarks' => '',
        ]);

        $response->assertRedirect(route('wallet.withdrawals'));
        $response->assertSessionHas('success');
        $response->assertSessionDoesntHaveErrors();

        $this->assertDatabaseCount('withdrawal_requests', 1);

        /** @var WithdrawalRequest $withdrawal */
        $withdrawal = WithdrawalRequest::query()->first();

        $this->assertSame('bank_transfer', $withdrawal->payment_method);
        $this->assertIsArray($withdrawal->account_details);
        $this->assertSame('John Doe', $withdrawal->account_details['account_title']);
        $this->assertSame('PK00HABB0000000000000000', $withdrawal->account_details['account_number']);
        $this->assertSame('Habib Bank Limited', $withdrawal->account_details['bank_name']);
        $this->assertArrayNotHasKey('crypto_source', $withdrawal->account_details);

        // Funds should have moved from withdrawable -> locked (existing
        // RequestWithdrawalAction behavior, untouched by this change).
        $wallet->refresh();
        $this->assertEqualsWithDelta(75.0, $wallet->withdrawable_balance, 0.001);
        $this->assertEqualsWithDelta(25.0, $wallet->locked_balance, 0.001);
    }

    public function test_crypto_withdrawal_is_stored_with_crypto_source(): void
    {
        $user = User::factory()->create();
        $this->walletFor($user, 100.0);

        $response = $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '10',
            'payment_method' => 'crypto',
            'account_title' => 'Jane Doe',
            'account_number' => 'TXk9d2f...walletaddress',
            'crypto_source' => 'Binance',
        ]);

        $response->assertRedirect(route('wallet.withdrawals'));
        $response->assertSessionDoesntHaveErrors();

        /** @var WithdrawalRequest $withdrawal */
        $withdrawal = WithdrawalRequest::query()->first();

        $this->assertSame('crypto', $withdrawal->payment_method);
        $this->assertSame('Binance', $withdrawal->account_details['crypto_source']);
        $this->assertArrayNotHasKey('bank_name', $withdrawal->account_details);
    }

    public function test_bank_transfer_requires_bank_name(): void
    {
        $user = User::factory()->create();
        $this->walletFor($user, 100.0);

        $response = $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '10',
            'payment_method' => 'bank_transfer',
            'account_title' => 'John Doe',
            'account_number' => '1234567890',
            // bank_name intentionally omitted
        ]);

        $response->assertSessionHasErrors(['bank_name']);
        $this->assertDatabaseCount('withdrawal_requests', 0);
    }

    public function test_crypto_requires_crypto_source(): void
    {
        $user = User::factory()->create();
        $this->walletFor($user, 100.0);

        $response = $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '10',
            'payment_method' => 'crypto',
            'account_title' => 'John Doe',
            'account_number' => '0xABC123',
            // crypto_source intentionally omitted
        ]);

        $response->assertSessionHasErrors(['crypto_source']);
        $this->assertDatabaseCount('withdrawal_requests', 0);
    }

    public function test_jazzcash_does_not_require_bank_name_or_crypto_source(): void
    {
        $user = User::factory()->create();
        $this->walletFor($user, 100.0);

        $response = $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '10',
            'payment_method' => 'jazzcash',
            'account_title' => 'John Doe',
            'account_number' => '03001234567',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseCount('withdrawal_requests', 1);
    }

    public function test_account_title_and_number_are_required(): void
    {
        $user = User::factory()->create();
        $this->walletFor($user, 100.0);

        $response = $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '10',
            'payment_method' => 'jazzcash',
        ]);

        $response->assertSessionHasErrors(['account_title', 'account_number']);
    }

    public function test_withdrawal_over_balance_is_rejected(): void
    {
        $user = User::factory()->create();
        $this->walletFor($user, 5.0);

        $response = $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '50',
            'payment_method' => 'jazzcash',
            'account_title' => 'John Doe',
            'account_number' => '03001234567',
        ]);

        $response->assertSessionHasErrors(['amount']);
        $this->assertDatabaseCount('withdrawal_requests', 0);
    }

    public function test_admin_can_view_withdrawal_with_structured_account_details(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletFor($user, 100.0);

        $admin = Admin::query()->create([
            'name' => 'Test Admin',
            'email' => 'admin-test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)->post(route('wallet.withdrawals.store'), [
            'amount' => '20',
            'payment_method' => 'bank_transfer',
            'account_title' => 'John Doe',
            'account_number' => 'PK00HABB0000000000000000',
            'bank_name' => 'Habib Bank Limited',
        ])->assertRedirect(route('wallet.withdrawals'));

        $withdrawal = WithdrawalRequest::query()->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.withdrawals.show', $withdrawal))
            ->assertOk()
            ->assertSee('Account Title')
            ->assertSee('John Doe')
            ->assertSee('Bank Name')
            ->assertSee('Habib Bank Limited');
    }
}
