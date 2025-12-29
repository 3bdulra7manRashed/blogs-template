# Admin User Setup Guide

## Overview

This Laravel application has been configured to disable public registration and use an admin-only user creation system. The initial admin account is created via a seeder using environment variables.

## Creating the Initial Admin User

### Step 1: Set Environment Variables

Add the following variables to your `.env` file:

```env
ADMIN_NAME="Abdulrahman"
ADMIN_EMAIL="Abdulrahman@blog.com"
ADMIN_PASSWORD="myblog25"
```

**Important:** Use a strong password and keep these credentials secure.

### Step 2: Run the Migration

```bash
php artisan migrate
```

This will add the `is_admin` column to the users table.

### Step 3: Run the Admin User Seeder

```bash
php artisan db:seed --class=AdminUserSeeder
```

This will create the initial admin user with the credentials specified in your `.env` file.

### Step 4: Clear Caches (Optional but Recommended)

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## Using the Admin Dashboard

1. **Log in** with your admin credentials at `/login`
2. **Navigate** to `/admin/users` to view all users
3. **Click** "إضافة مستخدم جديد" (Add New User) to create additional users
4. **Fill in** the user form:
   - Name (required)
   - Email (required, must be unique)
   - Password (optional - if left empty, a secure 12-character random password will be generated)
   - Admin checkbox (optional - to make the user an admin)

5. **Note the password** when creating users without a password - it will be shown once in the success message.

## Registration Disabled

Public registration has been completely disabled:
- Visiting `/register` will return a 404 error
- POST requests to `/register` will also return 404

## Middleware Protection

Admin routes are protected by:
- `auth` middleware (user must be logged in)
- `admin` middleware (user must have `is_admin = true`)

The `EnsureUserIsAdmin` middleware checks the `is_admin` boolean column. Users can also have admin privileges through Spatie Permission roles, and the `isAdmin()` method checks both.

## Running Tests

To verify the implementation:

```bash
php artisan test
```

Or run specific test files:

```bash
php artisan test tests/Feature/AdminUserCreationTest.php
php artisan test tests/Feature/Auth/RegistrationTest.php
```

## Alternative: Using Breeze or Fortify

### For Laravel Breeze:

If using Breeze, registration routes are typically in `routes/auth.php` (which we've already disabled). If you have additional registration routes, you can disable them similarly.

### For Laravel Fortify:

To disable registration in Fortify, edit `config/fortify.php`:

```php
'features' => [
    Features::registration(), // Remove or comment this line
    Features::resetPasswords(),
    Features::emailVerification(),
    // ... other features
],
```

Or in your `FortifyServiceProvider`, you can conditionally disable registration.

## Notes

- The admin user created by the seeder will also be assigned the 'admin' role via Spatie Permission
- Users created through the admin panel can optionally be made admins by checking the "Make this user an admin" checkbox
- Passwords are automatically hashed before storage
- New users are automatically marked as email verified (`email_verified_at` is set to `now()`)

