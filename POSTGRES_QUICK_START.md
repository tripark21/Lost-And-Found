# PostgreSQL Quick Start - 5 Minute Setup

## Quick Setup Instructions

### 1. Install PostgreSQL (if not already installed)
- Download: https://www.postgresql.org/download/windows/
- Default settings are fine
- Remember the password you set for `postgres` user

### 2. Enable PHP PostgreSQL Extension
- Open: `C:\xampp\php\php.ini`
- Find and uncomment these lines:
  - `;extension=pgsql` → remove semicolon
  - `;extension=pdo_pgsql` → remove semicolon
- Save file
- Restart Apache in XAMPP Control Panel

### 3. Update Database Configuration
Edit: `config/postgres_config.php`

Change this line:
```php
define('DB_PASS', 'password'); // Change this to your PostgreSQL password
```

### 4. Create Database
Navigate to: `http://localhost/LAF/database/db_setup_postgres.php`

You should see:
```
✓ Database 'lost_found_db' already exists.
✓ Items table created successfully or already exists.
✓ Indexes created successfully.
✓ Database setup complete!
```

### 5. Start Using the System!
- Home: `http://localhost/LAF/index_postgres.php`
- Report Lost: `http://localhost/LAF/report_lost_postgres.php`
- Report Found: `http://localhost/LAF/report_found_postgres.php`

## New Features with PostgreSQL

✨ **Upload Photos & Videos**
- Max size: 50MB
- Formats: JPG, PNG, GIF (images), MP4, AVI, MOV, MKV, WebM (videos)

✨ **View Media**
- Photos display as thumbnails
- Videos embedded with player controls

✨ **Better Performance**
- Optimized queries with indexes
- ACID compliant transactions

## File Structure Reference

All PostgreSQL versions have `_postgres` suffix:
- Lost Items: `pages/lost_items_postgres.php`
- Found Items: `pages/found_items_postgres.php`
- View Item: `pages/view_item_postgres.php`
- Report Lost: `report_lost_postgres.php`
- Report Found: `report_found_postgres.php`
- Handler: `handlers/process_report_postgres.php`
- Config: `config/postgres_config.php`
- Database Setup: `database/db_setup_postgres.php`

## Troubleshooting

### PostgreSQL won't start
```bash
# Windows Services - Start PostgreSQL
Services → PostgreSQL → Right-click → Start
```

### File upload fails
1. Check folder permissions: `C:\xampp\apache\htdocs\LAF\uploads`
2. Ensure C:\xampp\apache\htdocs\LAF\uploads folder exists
3. Check php.ini settings:
   - upload_max_filesize = 50M
   - post_max_size = 50M

### "Connection refused" error
- PostgreSQL not running
- Wrong password in postgres_config.php
- Wrong port (should be 5432)

## Next Steps

Read full documentation: `README_POSTGRES.md`

Enjoy your upgraded Lost & Found System! 🎉
