````markdown
# Authentication

## Supported Login Methods

Authentication is strategy-based. Supported login methods include:

-   Email + Password
-   Phone + OTP
-   Email + OTP

Each organization (tenant) can define allowed methods via `OrgAuthRules`.

---

## Auth Flow

1. User submits login credentials (`email/phone` + password/OTP).
2. A `LoginStrategy` resolves the method.
3. Verifier (e.g. `EmailVerifier`) confirms OTP or password.
4. A token is issued if successful.

---

## Strategy Resolution

```php
$factory = new LoginStrategyFactory();
$strategy = $factory->make($method);
```
````

Strategies implement the `LoginStrategyInterface`.

---

## Domain Rules

Auth rules are defined per organization (or domain group). Example:

```php
[
  'email_otp' => true,
  'phone_otp' => true,
  'password_required' => false,
]
```
