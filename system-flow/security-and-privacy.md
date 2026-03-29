# Security & Privacy Policy Implementation

This document describes the security and privacy measures implemented in the Castcrew (OptiCrew) workforce management platform, explaining how each mechanism works within the system.

---

## 1. Authentication

### 1.1 Password-Based Authentication

The system uses **bcrypt hashing** (10 rounds) for all stored passwords. Passwords are never stored in plain text.

- **Login Flow**: Users can authenticate via email, username, or name. The `LoginRequest` class attempts matching in this order:
  1. Email (primary)
  2. Alternative email (employees may have Gmail stored here)
  3. Username
  4. Name (fallback)
- **Password Verification**: Uses `Hash::check()` which performs a constant-time bcrypt comparison, resistant to timing attacks.
- **Rate Limiting**: Login attempts are throttled to **5 attempts per key** (based on login input + IP address). After exceeding the limit, users receive a lockout message with cooldown time.

### 1.2 Google OAuth 2.0

The system supports Google Sign-In for multiple purposes:

| Purpose | Description |
|---------|-------------|
| `login` | Standard login for all roles |
| `recruitment` | Applicant job applications |
| `quotation` | Client quotation requests |
| `link_account` | Linking Google to existing account |
| `mobile_login` | Mobile app authentication (Sanctum token) |

**OAuth Flow**:
1. User clicks "Sign in with Google"
2. System stores the auth purpose and context data in the session
3. User is redirected to Google's consent screen
4. Google redirects back to `/auth/google/callback`
5. The callback handler routes to the appropriate handler based on `google_auth_purpose`

**Security Measures**:
- Dynamic callback URL detection based on host (production vs. local vs. ngrok)
- Role conflict detection: prevents using an admin/employee Google account for applicant flows
- Automatic Google ID linking to existing accounts
- Session data is cleared after processing

### 1.3 API Token Authentication (Mobile)

For mobile app access, the system uses **Laravel Sanctum** personal access tokens:
- Tokens are generated upon successful Google authentication via the mobile flow
- Stateful domains are configured for localhost and the production URL
- Tokens do not expire by default (configurable)

---

## 2. Authorization & Access Control

### 2.1 Role-Based Access Control (RBAC)

The system defines the following user roles, each with dedicated middleware:

| Role | Middleware | Access Scope |
|------|-----------|-------------|
| `admin` | `CheckAdmin` | Full system access |
| `company` (Manager) | `CheckManager` | Company management |
| `employee` | `CheckEmployee` | Task execution, attendance |
| `client` / `external_client` | `CheckClient` | Appointments, history |
| `applicant` | `CheckApplicant` | Job applications, dashboard |

Each middleware verifies the authenticated user's role and redirects unauthorized users to their appropriate dashboard.

### 2.2 Policy-Based Authorization

Laravel Policies provide granular resource-level access control:

**Task Policy**:
- `view`: Admins see all tasks; employees see only their assigned tasks
- `create`: Admin only
- `update`: Admins can update all; employees can update their assigned tasks
- `delete`, `restore`, `forceDelete`: Admin only

**Additional Policies**: OptimizationRunPolicy, EmployeePolicy

### 2.3 Route Protection

Routes are organized into middleware-protected groups:
- All authenticated routes require the `auth` middleware
- Role-specific route groups apply the appropriate role-check middleware
- The `terms.accepted` middleware ensures users have accepted the Terms & Conditions before accessing protected pages

---

## 3. Cross-Site Request Forgery (CSRF) Protection

All POST, PUT, PATCH, and DELETE requests are protected by Laravel's `VerifyCsrfToken` middleware:
- Every form includes a `@csrf` token
- AJAX requests include the token via the `X-CSRF-TOKEN` header, read from the `<meta name="csrf-token">` tag
- No URIs are excluded from CSRF verification

---

## 4. Cookie Security

### 4.1 Encrypted Cookies

All cookies are encrypted by default using Laravel's `EncryptCookies` middleware, with two exceptions:
- `finnoys_terms_accepted`: Tracks Terms & Conditions acceptance (set via JavaScript for cross-page access)
- `finnoys_policy_accepted`: Tracks Privacy Policy acceptance (set via JavaScript for cross-page access)

These cookies are excluded from encryption because they are set client-side via `document.cookie` and need to be readable by JavaScript across different pages (login, recruitment).

### 4.2 Session Cookies

- **SameSite**: `lax` (allows session cookies on top-level navigations, such as OAuth redirects)
- **Secure**: Configurable via `SESSION_SECURE_COOKIE` (should be `true` in production)
- **HTTP Only**: Enabled by default (prevents JavaScript access to session cookies)
- **Session Lifetime**: 120 minutes (configurable)

---

## 5. Data Protection

### 5.1 Soft Deletes

Critical data is never permanently deleted. The following tables use soft deletes (`deleted_at` timestamp):

- `users`
- `employees`
- `tasks`
- `optimization_runs`
- `clients`
- `contracted_clients`
- `locations`
- `job_applications`

Soft-deleted records are automatically excluded from queries but can be restored by administrators.

### 5.2 Sensitive Data Handling

- **Environment Variables**: All sensitive configuration (database credentials, API keys, OAuth secrets) is stored in `.env` files, excluded from version control via `.gitignore`
- **Password Storage**: Bcrypt with 10 rounds; Argon2id available as fallback
- **API Keys**: Google OAuth client secrets, Gemini/Claude API keys, and OCR API keys are stored as environment variables
- **File Uploads**: Resume files and profile pictures are stored in protected storage paths with controlled access via authenticated routes

### 5.3 Input Validation

- **Server-Side**: All form inputs are validated using Laravel's validation rules (FormRequest classes)
- **Client-Side**: Alpine.js provides real-time validation feedback (e.g., phone number format, password strength)
- **Sanitization**: Laravel's `TrimStrings` and `ConvertEmptyStringsToNull` middleware process all incoming requests

---

## 6. User Account Security

### 6.1 Account Banning

Administrators can deactivate user accounts by toggling the `is_active` field:
- Banned users attempting to log in are immediately logged out
- The session is invalidated and regenerated
- A "banned" flag is flashed to display the suspension notice
- Google OAuth login also checks the `is_active` status post-authentication

### 6.2 Terms & Conditions Acceptance

- New users must accept the Terms & Conditions before accessing the system
- The `EnsureTermsAccepted` middleware checks the `terms_accepted_at` field on every authenticated request
- Users who haven't accepted are redirected to the acceptance page
- Google OAuth automatically sets `terms_accepted_at` for new account creation
- Cookie-based tracking (`finnoys_terms_accepted`, `finnoys_policy_accepted`) provides pre-authentication consent on public pages

### 6.3 Password Change Security

When changing passwords:
- Current password verification is required (if the account has a password)
- New password must be at least 8 characters
- Password strength validation: requires uppercase, lowercase, numbers, and special characters
- On successful password change, the user is logged out and must re-authenticate

---

## 7. Activity Logging & Auditing

### 7.1 User Activity Logs

The `UserActivityLog` model tracks security-relevant events:
- **Login events**: Captures user ID, IP address, and device information
- **Google account linking**: Logged when a Google account is linked to an existing user
- **Profile updates**: Tracked for audit purposes

### 7.2 Optimization History

All scheduling optimization runs are preserved:
- Each `OptimizationRun` records parameters, results, and fitness scores
- `OptimizationGeneration` tracks per-generation metrics for analysis
- Team compositions and task assignments are stored for accountability

---

## 8. Location Privacy & Geofencing

### 8.1 Geofence Configuration

The system uses geofencing for employee clock-in/out verification:
- **Geofence Radius**: Configurable (default: 100 meters), stored in `company_settings`
- **Location Sources**: Client locations from `contracted_clients` and `locations` tables
- **Validation**: The API endpoint calculates the distance between the employee's reported position and the assigned task location

### 8.2 Location Data Handling

- Employee location is only captured at clock-in/out events (not continuously tracked)
- Location coordinates are validated against the assigned task's client location
- Geofence radius is adjustable per company settings (10-1000 meters)

---

## 9. Real-Time Login Validation

The login page provides real-time feedback on credential validity:
- **Email/Username Check**: Validates existence against the database (fast DB lookup)
- **Password Check**: Verifies against the stored bcrypt hash (server-side only)
- **Rate Protection**: Debounced requests (300ms for login, 400ms for password) prevent excessive server load
- **User ID Caching**: After initial login check, the user ID is cached client-side to skip redundant DB lookups during password verification

**Security Note**: Passwords are never sent to the client. All password verification occurs server-side via `Hash::check()`. The bcrypt comparison is intentionally slow (~100ms) to resist brute-force attacks.

---

## 10. Third-Party Integrations Security

| Integration | Security Measure |
|------------|-----------------|
| Google OAuth | OAuth 2.0 with state validation, server-side token exchange |
| Gemini / Claude AI | API keys stored in environment variables, never exposed to client |
| OCR Space | API key stored in environment, used server-side only |
| Mailtrap (Email) | SMTP credentials in environment variables |

---

## Summary

The Castcrew platform implements a defense-in-depth security architecture:
1. **Authentication**: Multi-method (password + Google OAuth + Sanctum tokens)
2. **Authorization**: Role-based middleware + policy-based resource control
3. **Data Protection**: Bcrypt hashing, encrypted cookies, soft deletes, input validation
4. **Session Security**: CSRF tokens, SameSite cookies, session regeneration
5. **Monitoring**: Activity logging, optimization history, geofence validation
6. **Privacy**: Minimal location collection, configurable geofencing, Terms & Privacy acceptance enforcement
