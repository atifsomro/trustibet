# Admin Games Guide (for operators)

**Short version you can paste to WhatsApp / email:**

> Games are managed in Admin → Management → Games. You can have only **one game of each type**. After a game is created, the type cannot change. Use **Edit** for day-to-day changes. The top of the Edit page is game settings (**Update Game**). Prices and odds are below under **Packages (fees)** — each package and prize has its **own Save button**. Creating a game only saves settings; packages are added after you are redirected to Edit. If you see “This game already exists,” open Edit instead of Create.

---

## 1. Big picture

Think of admin games as **two steps**:

1. **Game settings** — title, image, timers, colors, active/featured  
2. **Packages & prizes** — what players pay (fee) and what they can win (odds)

```
Login → Management → Games
         ├─ Edit existing game (usual)
         │     → Update Game (settings)
         │     → Packages / Prizes (prices & odds)
         └─ Add Game (only if that type is missing)
               → Save Game
               → then add Packages / Prizes on Edit
```

### Rules that confuse people most

| Rule | Plain meaning |
|------|----------------|
| One game per type | You cannot create a second Scratch Card, Dice, Lucky Wheel, Color Trading, or 1$ game |
| Type is locked | After create, Game Type cannot be changed |
| Create ≠ packages | Create only saves settings. Packages appear on the **Edit** page |
| Separate Save buttons | **Update Game** does **not** save package/prize rows. Save each package/prize with its own button |

---

## 2. How to open Games

1. Log in to the **Admin** panel  
2. Left sidebar: **Management → Games**  
3. You will see the list at `/admin/games`

From there:

- **Add Game** — create a missing type  
- **Edit** on a row — change settings, packages, and prizes  

---

## 3. Edit an existing game (most common)

Use this when Color Trading, Wheel, etc. already exist (typical after first setup).

1. Open **Games**  
2. Click **Edit** on the game you want  
3. Change **Configuration** fields for that game type (see [cheat sheet](#6-per-type-cheat-sheet))  
4. Change **Title**, **Badge**, **Description**, **Card image**, **Rating**, **Sort Order**, **Active**, **Featured** if needed  
5. Click **Update Game** at the bottom of the first card  
6. Scroll down to **Packages (fees)** to change prices or odds (see [section 5](#5-packages-and-prizes))

You should see a green success message after Update Game.

---

## 4. Create a new game (only if that type is missing)

1. Click **Add Game**  
2. Choose **Game Type** first — the right Configuration fields then appear  
3. Fill Configuration + **Title** (Slug can be left empty; it is filled automatically)  
4. Set Status (**Active**) and **Featured** as needed  
5. Click **Save Game**  
6. You are taken to the **Edit** page — now add **Packages** and **Prizes** there  

### If create is blocked

If you see **“This game already exists”** or Save stays disabled:

- That type is already in the system  
- Go back to the Games list and click **Edit** on that game instead  

---

## 5. Packages and prizes

On the Edit page, the second card is **Packages (fees)**.

### What the words mean

| Word in admin | Meaning |
|---------------|---------|
| **Package** | A bet option / chip / entry players choose (e.g. Chip $10, Entry $1) |
| **Fee** | Amount taken from the player’s wallet |
| **Multiplier** | Payout helper (e.g. Color Trading often uses 2×) |
| **Chances** | Used by some games for how “plays” or chances work |
| **Prize Label** | Name shown for a win outcome |
| **Amount** | Cash prize amount (0 can mean no cash / display-only) |
| **Weight** | Odds — **higher weight = more likely** to be selected |
| **Color** | Color Trading color name (e.g. `green`) or wheel color |
| **Segment** | Wheel slice number (Lucky Wheel) |
| **Active** | Uncheck to hide without deleting |

### Add a package

1. At the top of **Packages (fees)**, fill **Package name**, **Fee**, and optional Multiplier / Chances / Sort  
2. Click **Add Package**  

### Edit a package

1. Change Name, Fee, Multiplier, Chances, Sort, Active  
2. Click that row’s **Save** (not “Update Game”)  

### Delete a package

Click **Delete Package** and confirm. This also removes its prizes.

### Add / edit prizes (odds)

Under each package you will see **Prizes / Odds for …**

1. Fill **Label**, **Amount**, **Weight** (and **Color** / **Segment** when shown)  
2. Click **Add**  
3. To change an existing prize, edit the row and click **Save**  

**Tip for odds:** If one prize has Weight `40` and another has Weight `10`, the first is about four times more likely.

---

## 6. Per-type cheat sheet

### Scratch Card

- No extra Configuration block  
- Control everything with **packages** and **prize weights**  

### Dice

- **Dice Faces** — usually `6`  

### Lucky Wheel

- **Free Spins / Day** — how many free spins a player gets per day (`0` = none)  
- Prizes can use **Color** and **Segment** for how the wheel looks  

### Color Trading

| Field | What it does |
|-------|----------------|
| **Round Seconds** | Length of one round |
| **Lock Seconds** | Last seconds when new bets are blocked |
| **History Limit** | How many recent colors show on the player page (1–5) |
| **Max Bet / Round** | Max a player can stake in one round (`0` = unlimited) |
| **Max Bet / Day** | Max a player can stake per day (`0` = unlimited) |
| **Colors** | Comma-separated list, e.g. `green, red, blue, yellow, orange, purple, pink, cyan, white, black` |

**Important:** Prize **Color** values should match the colors list (same names, lowercase is best).

### 1$ game (Limited Draw)

Shown in the type dropdown as **1$ game**.

| Field | What it does |
|-------|----------------|
| **Home Headline** | Marketing line on the participate page |
| **Prize Name** | e.g. Honda CG125 |
| **Prize Value** | Value shown / wallet amount if cash |
| **Currency Label** | Usually `$` |
| **Prize photo** | Picture of the prize |
| **Draw At** | When the draw happens |
| **Max Entries** | Total entries allowed (`0` = unlimited) |
| **Winners** | How many winners |
| **Max / User** | How many entries one user may buy |

The **package Fee** is the entry price players pay.

---

## 7. Common mistakes checklist

- [ ] Looking for packages on the **Create** page — they are only on **Edit**  
- [ ] Changing a package fee, then clicking **Update Game** — use the package **Save** button  
- [ ] Trying to create a second Color Trading / Wheel / Dice / Scratch / 1$ game  
- [ ] Changing Color Trading **Colors** but forgetting to update prize **Color** fields  
- [ ] Setting **Max Bet** to `0` to “block” betting — `0` means **unlimited**  
- [ ] Unchecking **Active** on the game and wondering why it disappeared from the site  

---

## 8. Quick recipes

### Change Color Trading round time

1. Games → Edit **Color Trading**  
2. Set **Round Seconds** / **Lock Seconds**  
3. **Update Game**  

### Add a new chip (e.g. $25)

1. Edit Color Trading  
2. Under Packages: name `Chip $25`, fee `25`, multiplier `2`  
3. **Add Package**  
4. Add color prizes with matching **Color** names and equal **Weight** if you want equal odds  
5. Add a win prize row (amount = fee × multiplier) if your setup uses fixed win amounts  

### Turn a game off temporarily

1. Edit the game  
2. Uncheck **Active**  
3. **Update Game**  

### Hide a package without deleting

1. Uncheck **Active** on that package  
2. Click package **Save**  

---

## Need help?

If something will not save, note:

1. Which game (title / type)  
2. Whether you clicked **Update Game** or a package/prize **Save**  
3. The exact error message in red  

That makes support much faster.
