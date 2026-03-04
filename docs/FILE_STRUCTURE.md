# Complete File Structure & Guide

## 📁 All Project Files

### Original MySQL Version

| File | Purpose |
|------|---------|
| `config.php` | MySQL database configuration |
| `db_setup.php` | Create MySQL database & tables |
| `index.php` | Home page with stats |
| `lost_items.php` | Browse lost items |
| `found_items.php` | Browse found items |
| `report_lost.php` | Report lost item form |
| `report_found.php` | Report found item form |
| `view_item.php` | View item details |
| `process_report.php` | Handle form (MySQL) |
| `style.css` | Styling for all pages |
| `README.md` | Original documentation |

**To use**: Start MySQL and visit `http://localhost/LAF/index.php`

---

### Firebase NoSQL Version

| File | Purpose |
|------|---------|
| `firebase_config.php` | 🔥 Firebase REST API functions |
| `index_firebase.php` | Home page (Firebase) |
| `lost_items_firebase.php` | Browse lost items (Firebase) |
| `found_items_firebase.php` | Browse found items (Firebase) |
| `report_lost.php` | Report lost form → Firebase |
| `report_found.php` | Report found form → Firebase |
| `view_item_firebase.php` | View item details (Firebase) |
| `process_report_firebase.php` | Handle form (Firebase) |
| `style.css` | Shared styling |
| `FIREBASE_SETUP_GUIDE.md` | 📖 Detailed Firebase setup |
| `QUICK_START_FIREBASE.md` | ⚡ 5-minute setup |
| `MYSQL_VS_FIREBASE.md` | 📊 Comparison guide |

**To use**: Get Firebase credentials and visit `http://localhost/LAF/index_firebase.php`

---

## 🚀 Quick Navigation

### For MySQL Users
```
Start → index.php → Set up MySQL → Run db_setup.php → Ready!
```

### For Firebase Users
```
Start → Get Firebase credentials → Update firebase_config.php → 
index_firebase.php → Ready!
```

---

## 📋 File Details

### Configuration Files

#### `config.php` (MySQL)
```php
DB_HOST = 'localhost'
DB_USER = 'root'
DB_PASS = ''
DB_NAME = 'lost_found_db'
```
**Used by**: index.php, lost_items.php, found_items.php, etc.

#### `firebase_config.php` (Firebase)
```php
FIREBASE_PROJECT_ID = 'your-project-id'
FIREBASE_API_KEY = 'your-api-key'
FIREBASE_DATABASE_URL = 'https://...'
```
**Functions provided**:
- `getAllItems()`
- `getItemById()`
- `addItem()`
- `updateItem()`
- `deleteItem()`
- `searchItems()`
- `getCategories()`

**Used by**: index_firebase.php, lost_items_firebase.php, etc.

---

### Main Pages

#### `index.php` / `index_firebase.php`
- Homepage with 4 action cards
- Statistics dashboard
- Links to all features

#### `lost_items.php` / `lost_items_firebase.php`
- List all lost items
- Search by name/description
- Filter by category
- Click to view details

#### `found_items.php` / `found_items_firebase.php`
- List all found items
- Search functionality
- Category filter
- View item details

#### `report_lost.php`
- Form to report lost items
- Fields: name, category, description, date, location, contact info
- Works with both MySQL and Firebase (based on form action)

#### `report_found.php`
- Form to report found items
- Same fields as report_lost.php
- Works with both MySQL and Firebase

#### `view_item.php` / `view_item_firebase.php`
- Shows complete item details
- Full description
- Contact information
- Direct email/phone links

---

### Processing Files

#### `process_report.php` (MySQL)
```php
Receives form data → Validates → 
Inserts into MySQL → Redirects with message
```

#### `process_report_firebase.php` (Firebase)
```php
Receives form data → Validates → 
Posts to Firebase REST API → Redirects with message
```

---

### Database Files

#### `db_setup.php` (MySQL only)
- **Run once** to set up database
- Creates database: `lost_found_db`
- Creates table: `items`
- Adds columns and indexes

#### Firebase (no setup file needed!)
- Manual setup in Firebase Console
- Or use `firebase_config.php` functions to create structure

---

### Styling File

#### `style.css` (Shared)
- Used by all HTML pages
- Responsive design (mobile-friendly)
- Modern color scheme
- CSS variables for easy customization

**Colors**:
```css
--primary-color: #2c3e50 (dark blue)
--accent-color: #e74c3c (red)
--success-color: #27ae60 (green)
--info-color: #3498db (light blue)
```

---

### Documentation Files

| File | For | Read First |
|------|-----|-----------|
| `README.md` | MySQL users | ✅ Yes |
| `QUICK_START_FIREBASE.md` | Firebase users | ✅ Yes |
| `FIREBASE_SETUP_GUIDE.md` | Firebase detailed | After quick start |
| `MYSQL_VS_FIREBASE.md` | Comparing both | Planning stage |
| `FILE_STRUCTURE.md` | Understanding all files | Now! |

---

## 🔄 How Data Flows

### MySQL Flow
```
User Form (report_lost.php)
    ↓
PHP Form Handler (process_report.php)
    ↓
Validation Check
    ↓
INSERT query to MySQL
    ↓
Database stores item
    ↓
User redirected to home
    ↓
Next user queries MySQL → Gets item
```

### Firebase Flow
```
User Form (report_lost.php)
    ↓
PHP Form Handler (process_report_firebase.php)
    ↓
Validation Check
    ↓
REST API POST to Firebase
    ↓
Firebase Cloud stores item
    ↓
User redirected to home
    ↓
Next user queries Firebase via REST → Gets item
```

---

## 📝 Database Schema

### Item Fields (Same for both MySQL & Firebase)

```
item_type       : 'lost' or 'found'
item_name       : String (e.g., "Blue Backpack")
category        : String (e.g., "Bags & Backpacks")
description     : Text (detailed description)
date_reported   : Date (YYYY-MM-DD)
location        : String (e.g., "Library - 2nd Floor")
status          : 'unresolved' or 'claimed'
contact_name    : String (reporter's name)
contact_email   : Email (reporter's email)
contact_phone   : String/Optional (reporter's phone)
created_at      : Timestamp (when added)
image_path      : String/Reserved (for future image upload)
```

### Item Categories
1. Bags & Backpacks
2. Electronics
3. Jewelry
4. Keys
5. Clothing
6. Documents
7. Wallets
8. Other

---

## 🔐 Security

### Against SQL Injection
- **MySQL**: Using `real_escape_string()`
- **Firebase**: Using REST API (not vulnerable)

### Against XSS Attacks
- Using `htmlspecialchars()` on all output
- Both versions protected

### Data Validation
- Email format validation
- Required field checks
- Date validation (must be past date)
- Type checking

### Firebase Security Rules (Test Mode)
```json
{
  "rules": {
    "items": {
      ".read": true,      // Anyone can read
      ".write": false     // Can't write (handled by REST API)
    }
  }
}
```

---

## 🎨 Customization

### Change Colors
Edit `style.css` CSS variables:
```css
:root {
    --primary-color: #2c3e50;    /* Change this */
    --accent-color: #e74c3c;     /* Or this */
}
```

### Change Site Name
Edit these files:
- `index.php` → Line 10
- `index_firebase.php` → Line 10
- `style.css` → `.logo` text

### Add New Categories
In forms (`report_lost.php`, `report_found.php`):
```html
<option value="Sports Equipment">Sports Equipment</option>
```

---

## 📱 Mobile Responsive

All files are responsive:
- **Desktop**: 1200px+ full layout
- **Tablet**: 768px to 1199px adjusted grid
- **Mobile**: <768px stacked layout

Test with browser DevTools: F12 → Toggle Device Toolbar

---

## 🚨 Common Errors & Fixes

| Error | MySQL | Firebase |
|-------|-------|----------|
| "Connection failed" | MySQL not running | Wrong credentials |
| "Database not found" | Run db_setup.php | Wrong database URL |
| "Items not appearing" | Check MySQL access | Check API key |
| "Form not submitting" | Check form action | Check form action |
| "No categories showing" | No items in DB | No items in Firebase |

---

## 📊 Performance Tips

### MySQL
- Add indexes on `item_type`, `category`, `status`
- Use pagination for large datasets
- Cache search results

### Firebase
- Use `indexOn` directive
- Enable CDN caching
- Limit queries with `.limitToFirst()` / `.limitToLast()`

---

## 🔗 Important URLs

After setup:

**MySQL Version**:
- Home: `http://localhost/LAF/index.php`
- Lost: `http://localhost/LAF/lost_items.php`
- Found: `http://localhost/LAF/found_items.php`
- Report: `http://localhost/LAF/report_lost.php`

**Firebase Version**:
- Home: `http://localhost/LAF/index_firebase.php`
- Lost: `http://localhost/LAF/lost_items_firebase.php`
- Found: `http://localhost/LAF/found_items_firebase.php`
- Report: `http://localhost/LAF/report_lost.php` (same form, different action)

---

## 📚 Learning Resources

- **PHP**: https://www.php.net/manual/
- **Firebase**: https://firebase.google.com/docs/database
- **MySQL**: https://dev.mysql.com/doc/
- **REST API**: https://restfulapi.net/

---

## 🎯 Next Steps

1. **Choose your database**
   - MySQL: Traditional, full control
   - Firebase: Modern, cloud-based

2. **Set up** (using README.md or QUICK_START_FIREBASE.md)

3. **Test** the system

4. **Customize** colors, categories, fields

5. **Deploy** to your college server

6. **Add features**:
   - Email notifications
   - Image uploads
   - User authentication
   - Admin dashboard
   - Mobile app

---

For specific setup: See appropriate guide
- MySQL: README.md
- Firebase: QUICK_START_FIREBASE.md

