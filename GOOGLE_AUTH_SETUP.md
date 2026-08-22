# Google Social Login — Setup Instructions

I don't have your `config/services.php` or your `users` migration, so those two need
a small manual step from you. Everything else (controller, model, routes, views,
new migration) is done and ready to drop in.

## 1. Install packages

```bash
composer require laravel/socialite
composer require doctrine/dbal   # needed because the new migration uses ->change()
```

## 2. Add Google credentials to `config/services.php`

Open `config/services.php` and add this array entry (alongside `mailgun`, `postmark`, etc — don't replace the whole file, just add this key):

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

## 3. Add to your `.env`

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
```

For production (Hostinger/cPanel), use your real domain, e.g.
`https://yourdomain.com/auth/google/callback`.

## 4. Get credentials from Google Cloud Console

1. Go to https://console.cloud.google.com/apis/credentials
2. Create (or select) a project.
3. Configure the **OAuth consent screen** (External, add your app name/logo, your
   email as support contact).
4. Create **Credentials → OAuth client ID → Web application**.
5. Under **Authorized redirect URIs**, add exactly the same URL you put in
   `GOOGLE_REDIRECT_URI` (e.g. `http://localhost/auth/google/callback` for local
   WAMP dev, and your live URL for production — add both as separate entries).
6. Copy the generated **Client ID** and **Client Secret** into `.env`.

## 5. Files in this delivery

| File | What to do with it |
|---|---|
| `2026_08_20_000000_add_google_auth_fields_to_users_table.php` | Copy into `database/migrations/`, then run `php artisan migrate` |
| `User.php` | Replace `app/Models/User.php` |
| `AuthController.php` | Replace `app/Http/Controllers/AuthController.php` |
| `web.php` | Replace `routes/web.php` |
| `login.blade.php` | Replace `resources/views/auth/login.blade.php` |
| `register.blade.php` | Replace `resources/views/auth/register.blade.php` |

The migration adds `google_id`, `provider`, `avatar` to `users`, and makes
`password`, `phone`, and `country_id` nullable (Google sign-ups won't supply
these at creation time).

## 6. What the flow does

- **`GET /auth/google`** (`redirectToGoogle`) — sends the user to Google's consent screen.
- **`GET /auth/google/callback`** (`handleGoogleCallback`):
  - Fetches the Google profile (`stateless()` is used so it works fine even if
    your session cookie domain/SameSite settings are strict).
  - If a user already exists with that `google_id` **or** that email, it links
    the Google ID to that account (so someone who registered manually with the
    same email can still use "Sign in with Google" afterward) and marks the
    email verified (Google already verified it).
  - Otherwise it creates a brand-new user: name/email from Google, a unique
    generated `username`, a random unusable password, `provider = 'google'`,
    and `email_verified_at` set immediately (no verification email needed).
  - Logs the user in and redirects to `home`.
- Errors (expired OAuth state, Google API failure, no email scope granted) are
  caught and redirect back to the login page with a flash `error` message,
  same pattern your app already uses elsewhere.

## 7. Small bugs I fixed along the way

- Your `login.blade.php` form was missing `@csrf`. Laravel's `VerifyCsrfToken`
  middleware would reject every login POST with a 419 error — I added it back.
- The commented-out "Or login with / Or register with" Google button markup
  in both blades is now uncommented and wired to `route('auth.google')`,
  reusing the existing `images/google/google.svg` asset reference you already
  had in the markup.

## 8. Testing

1. Run `php artisan migrate`.
2. Visit `/register` or `/login`, click the Google icon.
3. You should land on Google's consent screen, approve, and be redirected
   back into the app logged in as `home`.
4. Check the `users` table — a new row should have `google_id`, `provider =
   google`, and `password` filled with a bcrypt hash the user will never use.
