````markdown
# Testing Strategy

## Mocked Dependencies

Only the following are mocked in registration/auth tests:

-   `VerifierInterface` (e.g. `PhoneVerifier`, `EmailVerifier`)

---

## Real Logic

All other services (contact, profile, organization) use actual logic in tests.

## Lifecycle Tests

Use the `Lifecycle` wrapper to test full transactional flow.

```php
$this->lifecycle->run(fn() => $this->registrationService->register($dto));
```

Use assertions to confirm:

-   Events were fired
-   Transactions rolled back if needed
-   Correct models were created
````
