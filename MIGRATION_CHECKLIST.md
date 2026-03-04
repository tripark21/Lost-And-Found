# PostgreSQL Migration Checklist

Use this checklist to set up PostgreSQL for your Lost & Found System.

## Pre-Migration (Before Starting)

- [ ] **Backup existing data** (if using MySQL currently)
  - Export MySQL database: `mysqldump -u root lost_found_db > backup_mysql.sql`
  - Save all uploaded files from previous system

- [ ] **Note current system status**
  - Current items in database: ___________
  - Current uploaded files: ___________
  - System URL: http://localhost/LAF/

## PostgreSQL Installation

- [ ] **Download PostgreSQL 10+**
  - From: https://www.postgresql.org/download/windows/
  - Version: ___________
  - Download location: ___________

- [ ] **Install PostgreSQL**
  - Default settings ✓
  - Set `postgres` user password: ________________
  - Port number (default 5432): ________________
  - Start service after installation ✓

- [ ] **Verify installation**
  - Command: `psql --version`
  - Result: ___________

## PHP Configuration

- [ ] **Enable php-pgsql extension**
  - Edit file: `C:\xampp\php\php.ini`
  - Uncomment: `;extension=pgsql` → `extension=pgsql`
  - Uncomment: `;extension=pdo_pgsql` → `extension=pdo_pgsql`
  - Save file

- [ ] **Increase file upload limits** (in php.ini)
  ```ini
  upload_max_filesize = 50M
  post_max_size = 50M
  max_execution_time = 300
  ```
  - [ ] Confirmed settings updated

- [ ] **Restart Apache**
  - Stop Apache in XAMPP Control Panel
  - Wait 5 seconds
  - Start Apache again
  - Check status ✓

## Configuration Files

- [ ] **Update postgres_config.php**
  - File: `config/postgres_config.php`
  - DB_HOST: `localhost`
  - DB_PORT: `5432`
  - DB_USER: `postgres`
  - DB_PASS: ________________ (your PostgreSQL password)
  - DB_NAME: `lost_found_db`
  - Save file

- [ ] **Verify file permissions**
  - config/postgres_config.php is readable: ✓
  - uploads/ directory is writable: ✓

## Database Setup

- [ ] **Create database and tables**
  - Navigate to: `http://localhost/LAF/database/db_setup_postgres.php`
  - Should see: ✓ Database created
  - Should see: ✓ Items table created
  - Should see: ✓ Indexes created
  - Take screenshot: ___ (optional)

- [ ] **Verify indexes**
  - Database query tool or command:
  ```sql
  \di lost_found_db
  ```
  - Indexes visible: ✓

- [ ] **Test database connection**
  - Create simple test file and run:
  ```php
  <?php
  include 'config/postgres_config.php';
  echo "Connected to PostgreSQL!";
  ?>
  ```
  - Result: Connected ✓

## File System Setup

- [ ] **Create uploads directory**
  - Path: `C:\xampp\apache\htdocs\LAF\uploads\`
  - Created: ✓

- [ ] **Set folder permissions**
  - Command (as Admin):
  ```cmd
  icacls "C:\xampp\apache\htdocs\LAF\uploads" /grant Everyone:F /T
  ```
  - Permissions set: ✓

- [ ] **Verify permissions**
  - Test file upload succeeds: ✓
  - Files appear in uploads/ folder: ✓

## System Testing

- [ ] **Test home page**
  - URL: `http://localhost/LAF/index_postgres.php`
  - Displays without errors: ✓
  - Statistics show correctly: ✓

- [ ] **Test browsing**
  - Lost Items page loads: ✓ (`pages/lost_items_postgres.php`)
  - Found Items page loads: ✓ (`pages/found_items_postgres.php`)
  - Search functionality works: ✓

- [ ] **Test reporting**
  - Report Lost form loads: ✓ (`report_lost_postgres.php`)
  - Report Found form loads: ✓ (`report_found_postgres.php`)

- [ ] **Test file uploads**
  - [ ] Upload image (JPG) - Success ✓
  - [ ] Upload image (PNG) - Success ✓
  - [ ] Upload video (MP4) - Success ✓
  - [ ] File size validation - Works ✓
  - [ ] File type validation - Works ✓
  - [ ] File appears in uploads/ folder ✓

- [ ] **Test item viewing**
  - View item with image: ✓
  - View item with video: ✓
  - Image displays correctly: ✓
  - Video plays correctly: ✓

- [ ] **Test search**
  - Search by name: ✓
  - Search by description: ✓
  - Filter by category: ✓

## Performance Tests

- [ ] **Page load times**
  - Home page: < 2 seconds ✓
  - Items list: < 2 seconds ✓
  - Item details: < 2 seconds ✓

- [ ] **File upload speed**
  - 5MB image upload: _____ seconds
  - 20MB video upload: _____ seconds

## Data Migration (If Existing Data)

- [ ] **Export old data**
  - MySQL export: ___________
  - Firestore export: ___________

- [ ] **Import to PostgreSQL**
  - Command: `psql -U postgres lost_found_db < backup_mysql.sql`
  - Result: ___________

- [ ] **Verify imported data**
  - Item count matches: ✓
  - Data integrity check: ✓

- [ ] **Copy uploaded files**
  - Source: ___________
  - Destination: `C:\xampp\apache\htdocs\LAF\uploads\`
  - Files copied: ___________

## Documentation

- [ ] **Read setup guide**
  - File: `README_POSTGRES.md`
  - Understood: ✓

- [ ] **Review quick start**
  - File: `POSTGRES_QUICK_START.md`
  - Key takeaways: 
    - _____________________
    - _____________________
    - _____________________

- [ ] **Created emergency backup**
  - Location: ___________
  - Date: ___________
  - Verified: ✓

## Security Checks

- [ ] **Passwords secured**
  - PostgreSQL password not in public files: ✓
  - Credentials only in config files: ✓
  - Config files not in version control: ✓

- [ ] **File uploads validated**
  - MIME type checking enabled: ✓
  - File size limits enforced: ✓
  - Unique filenames generated: ✓
  - Malicious uploads prevented: ✓

- [ ] **SQL injection prevention**
  - Prepared statements used: ✓
  - Parameterized queries: ✓

- [ ] **XSS prevention**
  - htmlspecialchars() used: ✓
  - User input escaped: ✓

## Final Verification

- [ ] **All URLs working**
  - [x] http://localhost/LAF/index_postgres.php
  - [x] http://localhost/LAF/report_lost_postgres.php
  - [x] http://localhost/LAF/report_found_postgres.php
  - [x] http://localhost/LAF/pages/lost_items_postgres.php
  - [x] http://localhost/LAF/pages/found_items_postgres.php

- [ ] **Database responds**
  - Query test: ✓

- [ ] **File upload works**
  - Tested with image: ✓
  - Tested with video: ✓

- [ ] **Error handling tested**
  - Invalid form submission: ✓
  - Large file upload: ✓
  - Wrong file type: ✓

## Deployment Ready

- [ ] **All checks passed**
- [ ] **Backup taken**
- [ ] **Team notified**
- [ ] **Documentation ready**
- [ ] **Support contacts available**

## Post-Migration

- [ ] **Monitor system**
  - Check error logs: ___ times/day
  - Check disk space: ___ times/week
  - Verify backups: ___ times/month

- [ ] **Maintenance schedule**
  - Clean up old uploads: ___ per month
  - Database optimization: ___ per month
  - Security updates check: ___ per week

---

## Notes Section

Use this space to document any issues or special configuration:

```
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
```

## Contact Information

Support contacts in case of issues:
- Database Admin: ___________
- System Admin: ___________
- Backup contacts: ___________

## Sign-Off

- [ ] **Migration completed by:** ___________
- [ ] **Date:** ___________
- [ ] **Verified by:** ___________
- [ ] **Approval date:** ___________

---

**Migration Status: IN PROGRESS ☐ | COMPLETE ✓ | FAILED ☐**

**Last Updated:** March 2026
