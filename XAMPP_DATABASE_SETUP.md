# XAMPP Database Setup Guide for Barangay Inventory System

This guide will help you set up the MySQL database for the Barangay Inventory System using XAMPP.

## Prerequisites

1. **XAMPP installed** on your Windows system
2. **Laravel project** already set up (this project)
3. **PHP** and **Composer** installed

## Step 1: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Start the following services:
   - **Apache** (for web server)
   - **MySQL** (for database)

## Step 2: Create the Database

### Option A: Using phpMyAdmin (Recommended)

1. Open your web browser and go to: `http://localhost/phpmyadmin`
2. Click on **"New"** in the left sidebar
3. Enter database name: `barangay_inventory`
4. Select collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

### Option B: Using the SQL Script

1. In phpMyAdmin, select the `barangay_inventory` database
2. Click on the **"SQL"** tab
3. Copy and paste the entire content from `database_setup.sql` file
4. Click **"Go"** to execute the script

## Step 3: Configure Laravel Environment

1. Copy the `.env` file content provided below and create a new `.env` file in your project root:

```env
APP_NAME="Barangay Inventory System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barangay_inventory
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

## Step 4: Generate Application Key

Open Command Prompt or PowerShell in your project directory and run:

```bash
php artisan key:generate
```

## Step 5: Run Migrations (Optional)

If you want to use Laravel migrations instead of the SQL script:

```bash
php artisan migrate
```

## Step 6: Seed Sample Data (Optional)

```bash
php artisan db:seed
```

## Step 7: Test the Database Connection

Run this command to test if Laravel can connect to the database:

```bash
php artisan tinker
```

Then in the tinker console, run:

```php
DB::connection()->getPdo();
```

If successful, you should see a PDO object without errors.

## Database Structure

The database includes the following tables:

### Core Tables
- **users** - System users and authentication
- **sessions** - User session management
- **password_reset_tokens** - Password reset functionality

### Inventory Tables
- **items** - Inventory items (equipment, supplies, etc.)
- **cars** - Vehicle inventory
- **borrowed_items** - Item borrowing records
- **borrowed_cars** - Vehicle borrowing records

### System Tables
- **cache** - Application cache
- **jobs** - Queue jobs
- **failed_jobs** - Failed queue jobs
- **migrations** - Laravel migration tracking

## Sample Data

The database setup includes sample data:
- 1 admin user (email: admin@barangay.com)
- 5 sample inventory items
- 4 sample vehicles
- All items and vehicles are initially available

## Troubleshooting

### Common Issues:

1. **Connection Refused Error**
   - Make sure MySQL service is running in XAMPP
   - Check if port 3306 is not blocked

2. **Access Denied Error**
   - Default XAMPP MySQL username: `root`
   - Default XAMPP MySQL password: (empty)
   - Make sure these match your `.env` file

3. **Database Not Found Error**
   - Make sure you created the `barangay_inventory` database
   - Check the database name in your `.env` file

4. **Permission Denied Error**
   - Make sure XAMPP is running as Administrator
   - Check file permissions in your project directory

### Testing Commands:

```bash
# Test database connection
php artisan migrate:status

# Check if tables exist
php artisan tinker
>>> Schema::hasTable('users')
>>> Schema::hasTable('items')
>>> Schema::hasTable('cars')
```

## Next Steps

After successful database setup:

1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

2. Open your browser and go to: `http://localhost:8000`

3. You should see the Barangay Inventory System login page

## Default Login Credentials

- **Email:** admin@barangay.com
- **Password:** password (you'll need to reset this)

To reset the admin password, run:
```bash
php artisan tinker
>>> $user = User::where('email', 'admin@barangay.com')->first();
>>> $user->password = Hash::make('your_new_password');
>>> $user->save();
```

## Support

If you encounter any issues, check:
1. XAMPP error logs in `xampp/mysql/data/`
2. Laravel logs in `storage/logs/laravel.log`
3. Make sure all required PHP extensions are installed
