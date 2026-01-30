# Database Authentication Setup

## Overview

The CMS has been updated to store user credentials in an SQLite database instead of hardcoded values in the config file. This provides better security and allows administrators to change their login credentials through the admin interface.

## Changes Made

### 1. Database Structure
- **Users Table**: Created in `database/blog.db`
  - `id`: Unique user identifier
  - `username`: Login username
  - `password`: Hashed password
  - `email`: User email (optional)
  - `created_at`: Account creation timestamp
  - `updated_at`: Last update timestamp

### 2. Configuration Updates
- **Removed**: Hardcoded `ADMIN_USERNAME` and `ADMIN_PASSWORD` from `config.php`
- **Added**: `get_db()` function for database connections

### 3. Authentication Functions
Added to `functions.php`:
- `authenticate_user($username, $password)` - Verify login credentials
- `get_user($user_id)` - Retrieve user information
- `update_user_password($user_id, $new_password)` - Change password
- `update_user($user_id, $username, $email)` - Update profile

### 4. Login System
- Updated `admin/login.php` to authenticate against database
- Added session variables: `user_id`, `username`

### 5. Settings Page
- New page: `admin/settings.php`
- Features:
  - View account information
  - Update username and email
  - Change password with current password verification
  - Password strength requirements (minimum 6 characters)

### 6. Admin Interface
- Added "Settings" link to admin navigation
- Display current username in header

## Initial Setup

The default admin account has been created:
- **Username**: `admin`
- **Password**: `changeme123`

**⚠️ IMPORTANT**: Please change this password immediately after logging in!

## Changing Your Password

1. Log in to the admin panel
2. Click "Settings" in the navigation menu
3. Scroll to "Change Password" section
4. Enter your current password
5. Enter and confirm your new password
6. Click "Change Password"

## Managing Your Account

From the Settings page, you can:
- Update your username (used for login)
- Add/update your email address
- View account creation date
- Change your password

## Security Best Practices

✅ Use a strong password with:
- At least 6 characters (longer is better)
- Mix of letters, numbers, and symbols
- Avoid common words or patterns

✅ Keep your credentials secure:
- Don't share your password
- Log out when finished
- Change password periodically

## Database Location

`/database/blog.db` - SQLite database containing:
- User accounts
- Blog posts
- Blog comments
- Blog categories

## Troubleshooting

### Can't log in after update
If you're locked out:
1. Run `php database/init-users.php` to reset admin account
2. Use default credentials: `admin` / `changeme123`
3. Change password immediately

### Forgot password
Since this is a simple CMS, password recovery requires database access:
1. Access the server filesystem
2. Run the init script to create a new admin account
3. Or manually update the password hash in the database

## Files Modified

- `config.php` - Removed hardcoded credentials, added database helper
- `functions.php` - Added authentication functions
- `admin/login.php` - Updated to use database authentication
- `admin/settings.php` - New settings page (created)
- `admin/includes/admin-header.php` - Added Settings link and username display
- `database/init-users.php` - Database initialization script (created)
- `blog-functions.php` - Removed duplicate get_db() function

## Migration Notes

The hardcoded credentials have been migrated to the database. The original config values are no longer used.
