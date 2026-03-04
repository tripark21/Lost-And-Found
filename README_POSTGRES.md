# PostgreSQL Migration Guide - Lost & Found System

This guide will help you migrate from Firebase/MySQL to PostgreSQL with file upload support.

## Why PostgreSQL?

✅ **Better for File Management** - PostgreSQL handles large binary data efficiently
✅ **Improved Performance** - BLOB support and indexing
✅ **Scalability** - Better suited for growing applications
✅ **ACID Compliance** - Full transactions support
✅ **Cost Effective** - Free and open-source
✅ **Video/Photo Support** - Native support for storing media references

## Prerequisites

- PostgreSQL 10.0 or higher installed
- PHP 7.4 or higher with PostgreSQL extension (php-pgsql)
- XAMPP with Apache and PHP configured

## Installation Steps

### Step 1: Install PostgreSQL

**For Windows:**
1. Download PostgreSQL from: https://www.postgresql.org/download/windows/
2. Run the installer
3. Remember the password you set for the `postgres` user (important!)
4. Keep default port: 5432

**Verify Installation:**
```bash
psql --version
```

### Step 2: Configure PHP PostgreSQL Extension

1. Open `php.ini` (usually in `C:\xampp\php\`)
2. Find and uncomment the line: `;extension=pgsql`
3. Remove the semicolon to enable it: `extension=pgsql`
4. Also uncomment: `;extension=pdo_pgsql`
5. Save and restart Apache

### Step 3: Update PostgreSQL Configuration

Edit `config/postgres_config.php`:

```php
define('DB_HOST', 'localhost');      // PostgreSQL host
define('DB_PORT', 5432);             // PostgreSQL port
define('DB_USER', 'postgres');       // Your PostgreSQL user
define('DB_PASS', 'your_password');  // The password you set during installation
define('DB_NAME', 'lost_found_db');  // Database name
```

### Step 4: Create the Database and Tables

1. Navigate to: `http://localhost/LAF/database/db_setup_postgres.php`
2. You should see success messages:
   - ✓ Database created successfully
   - ✓ Items table created successfully
   - ✓ Indexes created successfully

### Step 5: Set Upload Permissions

Create the uploads directory (already done) and ensure it's writable:

**Windows (via Command Prompt as Administrator):**
```cmd
icacls "C:\xampp\apache\htdocs\LAF\uploads" /grant Everyone:F /T
```

**Linux/macOS:**
```bash
chmod 755 /var/www/html/LAF/uploads
chmod -R 777 /var/www/html/LAF/uploads/*
```

## File Structure

```
LAF/
├── index_postgres.php              # Home page (PostgreSQL version)
├── report_lost_postgres.php        # Report lost items with uploads
├── report_found_postgres.php       # Report found items with uploads
├── config/
│   └── postgres_config.php         # PostgreSQL configuration
├── database/
│   └── db_setup_postgres.php       # Database initialization
├── handlers/
│   └── process_report_postgres.php # Form submission handler
├── pages/
│   ├── lost_items_postgres.php     # View lost items
│   ├── found_items_postgres.php    # View found items
│   └── view_item_postgres.php      # View item details with media
├── uploads/                        # Media files storage
├── css/
│   └── style.css                   # Stylesheet
└── README_POSTGRES.md              # This file
```

## Using the PostgreSQL Version

### Access the Application

Navigate to: `http://localhost/LAF/index_postgres.php`

### Report an Item with Media

1. Click "Report Lost" or "Report Found"
2. Fill in all required fields
3. Optionally upload a photo or video (max 50MB)
   - Supported formats: JPG, PNG, GIF (images)
   - Supported formats: MP4, AVI, MOV, MKV, WebM (videos)
4. Submit the form
5. Media files are automatically saved in the `uploads/` directory

### View Items with Media

1. Browse lost or found items
2. Items with media show a 📎 indicator
3. Click "View Details" to see:
   - Full description
   - Photos or embedded video player
   - Contact information

## Database Schema

### items table

| Column | Type | Description |
|--------|------|-------------|
| id | SERIAL | Primary key (auto-increment) |
| item_type | VARCHAR(10) | 'lost' or 'found' |
| item_name | VARCHAR(100) | Name of the item |
| description | TEXT | Detailed description |
| category | VARCHAR(50) | Item category |
| date_reported | DATE | Date item was lost/found |
| location | VARCHAR(150) | Location where lost/found |
| contact_name | VARCHAR(100) | Reporter's name |
| contact_email | VARCHAR(100) | Reporter's email |
| contact_phone | VARCHAR(20) | Reporter's phone (optional) |
| media_filename | VARCHAR(255) | Uploaded file name |
| media_type | VARCHAR(10) | 'image' or 'video' |
| status | VARCHAR(20) | 'unresolved', 'claimed', or 'returned' |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Record update time |

**Indexes:**
- idx_item_type - Fast filtering by item type
- idx_status - Fast filtering by status
- idx_category - Fast filtering by category
- idx_item_name - Fast searching by name
- idx_date_reported - Fast sorting by date

## File Upload Features

### Supported File Types

**Images:**
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)

**Videos:**
- MP4 (.mp4)
- AVI (.avi)
- MOV (.mov)
- MKV (.mkv)
- WebM (.webm)

### Upload Limits

- Maximum file size: 50MB
- Files are automatically validated
- Unique filenames are generated to avoid conflicts
- Files are stored securely in the uploads directory

### Security Features

✅ MIME type validation
✅ File extension validation
✅ File size limitation
✅ Unique filename generation
✅ Error handling and cleanup on failed uploads
✅ SQL injection prevention with prepared statements
✅ XSS prevention with htmlspecialchars()

## Managing Uploaded Files

### Delete Uploaded Files

To delete files manually:
```bash
rm C:\xampp\apache\htdocs\LAF\uploads\item_*.jpg
```

### Clean Old Files (Optional)

Add this to database/db_maintenance.php:
```php
// Delete files older than 30 days if item is removed
$files = glob('../uploads/*');
foreach ($files as $file) {
    if (time() - filemtime($file) > 30 * 24 * 60 * 60) {
        unlink($file);
    }
}
```

## Troubleshooting

### "Connect failed: SQLSTATE[08006]"
- PostgreSQL is not running
- **Solution:** Start PostgreSQL service
```bash
# Windows: Start services
# macOS: brew services start postgresql
# Linux: sudo systemctl start postgresql
```

### "Extension php-pgsql is not installed"
- PHP doesn't have PostgreSQL extension enabled
- **Solution:** Enable php-pgsql in php.ini and restart Apache

### "Permission denied" when uploading
- Upload directory is not writable
- **Solution:** Check folder permissions (see Step 5)

### File upload fails silently
- Check file size limit in php.ini:
```ini
upload_max_filesize = 50M
post_max_size = 50M
```

### Videos don't play
- Browser doesn't support the video format
- **Solution:** Convert to MP4 or WebM format

## Database Backup

### Backup PostgreSQL Database

```bash
pg_dump -U postgres lost_found_db > backup_lost_found.sql
```

### Restore Database

```bash
psql -U postgres -d lost_found_db < backup_lost_found.sql
```

### Backup Uploads

```bash
# Windows
xcopy "C:\xampp\apache\htdocs\LAF\uploads" "C:\backups\uploads" /E /I

# Linux/macOS
cp -r /var/www/html/LAF/uploads /backups/uploads
```

## Performance Tips

1. **Regular Cleanup:** Remove old items and unused files
2. **Index Optimization:** PostgreSQL automatically optimizes indexes
3. **Connection Pooling:** For high traffic, consider using pgBouncer
4. **Compression:** Enable gzip compression for large files
5. **CDN:** For production, use a CDN to serve media files

## Migration from MySQL/Firebase

If you have existing data:

```sql
-- Insert from old MySQL database
INSERT INTO items (item_type, item_name, description, category, date_reported, location, contact_name, contact_email, contact_phone, status)
SELECT item_type, item_name, description, category, date_reported, location, contact_name, contact_email, contact_phone, status
FROM old_database.items;
```

## Support

For more information about PostgreSQL:
- Official Documentation: https://www.postgresql.org/docs/
- PHP PDO: https://www.php.net/manual/en/pdo.pgsql.php

## Next Steps

1. ✅ Set up PostgreSQL
2. ✅ Configure PHP extension
3. ✅ Update config/postgres_config.php
4. ✅ Run database setup
5. ✅ Test with sample items
6. ✅ Start reporting lost/found items with media!

## Features Summary

| Feature | Status |
|---------|--------|
| Browse lost items | ✅ |
| Browse found items | ✅ |
| Search & filter | ✅ |
| Report lost items | ✅ |
| Report found items | ✅ |
| **Upload photos** | ✅ NEW |
| **Upload videos** | ✅ NEW |
| **Display media** | ✅ NEW |
| Contact information | ✅ |
| Responsive design | ✅ |
| Statistics dashboard | ✅ |

---

**Last Updated:** March 2026
**System:** PostgreSQL Lost & Found with Media Upload Support
