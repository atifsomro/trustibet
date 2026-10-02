# Color Trading Fairness — Client Brief

## Can players “hack” the next color with pencil and paper?

**No.** Writing past colors and guessing “what is due next” cannot force a win.

### Why pattern notes fail

1. **Each round is independent.** The next color does not depend on the last result, streaks, or frequency charts.
2. **The color is chosen only after betting closes.** While players are betting, the winning color is not decided from their notes or from live bets.
3. **Outcomes use cryptographic randomness.** At settlement, a committed server seed produces a fair roll that maps to a color. Past history is not an input.
4. **The house edge is already built into odds/payouts.** Tracking colors may feel clever in the short run, but over many rounds expected value stays with the house when multipliers and color counts are set correctly.

What players call a “system” is usually the **gambler’s fallacy** (“green hasn’t appeared, so it must come next”) or **stake doubling** after losses. Those do not predict the next color.

### What we put in place

| Control | Purpose |
|--------|---------|
| Short recent history (default 3) | Less fuel for hot/cold tracking |
| On-screen independence message | Sets expectations that rounds do not chain |
| Max stake per round / per day | Limits martingale-style bankroll abuse |
| Commit–reveal fairness | Hash shown while betting; seed revealed after settle so the result can be verified |

### Message you can share with stakeholders

> Betters writing colors on paper cannot force wins. Each result is generated server-side from a pre-committed random seed after betting closes, and past rounds do not influence the next. History is limited and stake caps discourage “systems” and doubling strategies, while the game stays fair and random.

### How to verify a settled round

1. During betting, note the **Fairness Commit** (`server_seed_hash` = SHA-256 of the secret seed).
2. After settlement, the **seed** is revealed.
3. Confirm `SHA-256(seed)` matches the commit shown earlier.
4. The roll is derived as: `HMAC-SHA256(seed, "{round_id}:{round_number}")`, take the first 8 hex digits as an integer, then `(value % total_weight) + 1`, and map that roll onto the weighted colors.
