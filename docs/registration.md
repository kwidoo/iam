````markdown
# Registration

## RegistrationService

Main service responsible for user registration.

### Key Responsibilities

-   Contact creation and verification (email/phone)
-   Profile creation
-   Organization creation (if applicable)
-   User account creation and linking
-   Scoped permission setup

---

## Data Flow

1. Validate and resolve registration method
2. Verify OTP via `PhoneVerifier` / `EmailVerifier`
3. Create Contact and Profile
4. Register user with UUID
5. Associate user with organization (if passed or default)

---

## DTO: RegistrationData

```php
class RegistrationData {
  string $method;
  string $value;
  string $otp;
  ?string $password;
  ?string $organization;
}
```

---

## Strategy Pattern

Like login, registration is strategy-based.

-   `RegisterStrategyInterface`
-   Implementations: `OtpRegisterStrategy`, `PasswordRegisterStrategy`
````
