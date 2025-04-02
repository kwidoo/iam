````markdown
# Contact Verification

## Verifiers

Verifiers handle OTP generation, throttling, token storage and validation.

```php
interface VerifierInterface {
    public function send(string $value): void;
    public function verify(string $value, string $token): bool;
}
```
````

Implementations:

-   `EmailVerifier`
-   `PhoneVerifier`

---

## Rate Limiting

Each verifier implements `RateLimiterInterface` to control sending frequency.

---

## Token Metadata

Verification tokens include:

-   IP Address
-   User Agent
-   Optional: Geo Location

Useful for security auditing and abuse prevention.

---

## Queued Notifications

All verifier notifications are queued via Laravel’s `ShouldQueue`.

```

```
