# Kwidoo IAM — Laravel Microservice for Identity and Access Management

**Status**: MVP (70% complete)
**Tech stack**: Laravel, Passport (OAuth2), Spatie Event Sourcing, SOLID, DRY, custom Lifecycle framework
**Purpose**: Acts as a centralized IAM microservice for authenticating and authorizing users across multiple Laravel-based microservices.

---

## ✨ Features

-   ✅ OAuth2 authentication via Laravel Passport
-   ✅ Event Sourcing with Spatie package
-   ✅ SOLID, DRY, modular architecture with custom `Lifecycle` and service resolution
-   ✅ Multi-organization logic with flexible user-org relationships
-   ✅ Role and Policy support (WIP)
-   ✅ Dynamic menu builder per user/service (microservice-compliant)
-   ✅ Reusable shared logic in separate `kwidoo/mere` package

---

## 🧱 Architecture Overview

### 🧠 Core Concepts

This IAM system is built as a **modular, event-sourced, lifecycle-driven microservice**, emphasizing clear separation of concerns and long-term maintainability.

-   **Factory/Resolver Pattern**: Used to dynamically select service implementations based on input or configuration (DI-friendly workaround for PHP).
-   **Lifecycle Executor**: Encapsulates authorization, hooks (before/after), aggregate handling, and service orchestration.
-   **Eventable Hooks**: Pre-/post-operation hooks allow extensibility (currently implemented but may be refactored).
-   **Aggregate Roots**: Domain logic is captured via Spatie's Event Sourcing aggregate system.

---

## 🔁 Registration Flow (Example)

1. **Authorization Check** — Can the current user register?
2. **Lifecycle Orchestration**:
    - Create User
    - Create Profile
    - Create Contact (email/phone)
    - Create or assign Organization
3. **Dynamic Organization Rules**:
    - Single default organization
    - One organization per user (with or without ability to create more)
    - Many-to-many user/organization support

---

## 🔒 Roles & Permissions (WIP)

-   Role-based access with Spatie Laravel Permission
-   Dynamic, organization-aware policy checks
-   Planned support for scoped permission generation (`org-{id}-{action}`)

---

## 🧩 Related Components

### 📦 [`kwidoo/mere`](https://github.com/your-org/mere)

Reusable internal Laravel package that provides:

-   Lifecycle service infrastructure
-   Shared logic and traits across microservices
-   Event projector bridge and hook handling

### 📦 [`kwidoo/sms-verification`](https://github.com/your-org/sms-verification)

-   SMS verification system with support for 9 providers:
    -   Twilio, Nexmo (Vonage), MessageBird, etc.
-   Config-based driver switching
-   OTP token generation and validation
-   Can be reused in any Laravel app

### 📦 [`kwidoo/passport-multiauth`](https://github.com/your-org/passport-multiauth)

-   Passport extension that supports:
    -   Login via password
    -   Login via OTP (SMS or Email)
-   Drop-in replacement for Laravel Passport’s password grant
-   Can be used independently of the IAM service

---

## ⚙️ Tech Highlights

| Component   | Stack                         |
| ----------- | ----------------------------- |
| Framework   | Laravel                       |
| Auth        | Laravel Passport (OAuth2)     |
| Events      | Spatie Laravel Event Sourcing |
| DI Strategy | Factory + Resolver pattern    |
| Principles  | SOLID, DRY                    |
| Deployment  | Microservice-ready            |

---

## 🚧 Roadmap

-   [ ] Complete UI interface (admin console)
-   [ ] Finalize role/policy support
-   [ ] Write full test suite (unit + feature)
-   [ ] Dockerize for microservice deployment
-   [ ] Add OpenAPI (Swagger) documentation

---

## 🧠 Philosophy

This project is not just about authentication, but about creating a **modular identity service** with clear extensibility points and adherence to SOLID principles.

It is designed to be **the backbone IAM module** for distributed systems.

---

## 🧑‍💻 Author

Built by a Laravel/Vue architect with over a decade of experience, focused on scalable, long-living systems.

---

## 📬 Contact / Early Access

Want to use this IAM module in your system? Interested in OAuth2/event-sourced identity architecture?

📩 Reach out via LinkedIn or email to discuss implementation or consulting opportunities.
