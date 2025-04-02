```markdown
# Overview – Multi-Tenant IAM System

This project provides a robust, modular identity and access management (IAM) system built in Laravel. It supports:

-   Multi-tenancy (organization-based)
-   Flexible authentication (OTP, password, email, phone)
-   Configurable registration workflows
-   Dynamic permissions per organization
-   Modular service architecture with strategy and lifecycle patterns

---

## Packages

| Package       | Purpose                                                                          |
| ------------- | -------------------------------------------------------------------------------- |
| `kwidoo/mere` | Shared service layer, strategies, lifecycle, verifiers, and utilities            |
| Main App      | IAM-specific logic (users, organizations, registration/authentication workflows) |

---

## Architecture

-   🧠 **Service Layer**: Business logic separated by domain (UserService, OrganizationService, etc.)
-   🔌 **Strategies**: Pluggable auth/register behavior (email/password, OTP)
-   🧪 **Lifecycle**: Transactional, event-driven service orchestration
-   🛡 **Spatie Permissions**: Role/permission management per organization
-   🌐 **Multi-Tenant Support**: Organization scoping via pivot tables and dynamic policies
```
