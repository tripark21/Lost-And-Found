# Campus Lost & Found System

A simple and elegant lost and found system for college campuses built with PHP and MySQL.

## Features

✅ **Browse Lost Items** - Search through items reported as lost
✅ **Browse Found Items** - Check found items reported by campus members
✅ **Report Lost Items** - Users can report items they've lost
✅ **Report Found Items** - Users can report items they've found
✅ **Search & Filter** - Search by item name, description, or category
✅ **Contact Information** - Direct contact details for item reporters
✅ **Responsive Design** - Works on desktop, tablet, and mobile devices
✅ **Statistics Dashboard** - View total lost, found, and resolved items

## Project Structure

```
LAF/
├── index.php              # Home page with statistics
├── lost_items.php         # View and search lost items
├── found_items.php        # View and search found items
├── report_lost.php        # Form to report lost items
├── report_found.php       # Form to report found items
├── view_item.php          # Detailed item view
├── process_report.php     # Handle form submissions
├── db_setup.php           # Database initialization
├── config.php             # Database configuration
├── style.css              # Stylesheet
└── README.md              # This file
```

## Installation & Setup

### Prerequisites

- XAMPP (with Apache & MySQL)
- PHP 7.0 or higher
- MySQL 5.7 or higher

### Step 1: Create Database

1. Start XAMPP
2. Open your browser and go to `http://localhost/phpmyadmin`
3. Navigate to `http://localhost/LAF/db_setup.php` to automatically create the database and tables
4. You should see: "Database created successfully" and "Items table created successfully"

### Step 2: Access the Application

Open your browser and navigate to:
```
http://localhost/LAF/
```

## Database Schema

### items table

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| item_type | ENUM | 'lost' or 'found' |
| item_name | VARCHAR(100) | Name of the item |
| description | TEXT | Detailed description |
| category | VARCHAR(50) | Item category |
| date_reported | DATE | Date item was lost/found |
| location | VARCHAR(255) | Location where lost/found |
| status | ENUM | 'unresolved' or 'claimed' |
| contact_name | VARCHAR(100) | Reporter's name |
| contact_email | VARCHAR(100) | Reporter's email |
| contact_phone | VARCHAR(20) | Reporter's phone (optional) |
| image_path | VARCHAR(255) | Image path (reserved for future use) |
| created_at | TIMESTAMP | Record creation timestamp |

## Categories

- Bags & Backpacks
- Electronics
- Jewelry
- Keys
- Clothing
- Documents
- Wallets
- Other

## Configuration

Edit `config.php` to change database settings:

```php
define('DB_HOST', 'localhost');      // Database host
define('DB_USER', 'root');          // Database username
define('DB_PASS', '');              // Database password
define('DB_NAME', 'lost_found_db'); // Database name
```

## Features Explained

### 1. Home Page (index.php)
- Dashboard showing system statistics
- Quick access to all features
- Live counts of lost, found, and resolved items

### 2. Browse Lost Items (lost_items.php)
- View all unresolved lost items
- Search by name or description
- Filter by category
- View contact information of reporters

### 3. Browse Found Items (found_items.php)
- View all unresolved found items
- Search and filter functionality
- Contact information for item finders

### 4. Report Lost Item (report_lost.php)
- User-friendly form to report lost items
- Required fields: Item name, category, description, date lost, location, name, email
- Optional field: Phone number

### 5. Report Found Item (report_found.php)
- Similar form to report items found on campus
- Same fields and validation as lost item form

### 6. Item Details (view_item.php)
- Detailed view of any item
- Full description and contact information
- Direct email and phone links for contact

## Usage Guide

### For Someone Looking for a Lost Item:
1. Go to "Lost Items"
2. Search for your item or browse by category
3. Check the description and contact the reporter
4. Help reunite items with their owners!

### For Someone Reporting a Lost Item:
1. Click "Report Lost Item"
2. Fill in the form with item details
3. Provide accurate contact information
4. Submit the form

### For Someone Finding an Item:
1. Click "Report Found Item"
2. Describe the item you found
3. Provide your contact information
4. Await contact from the owner

## Validation

The system includes validation for:
- Required fields (item name, category, description, etc.)
- Email format validation
- Date validation (must be in the past)
- SQL injection prevention using prepared statements

## Future Enhancements

- [ ] Image upload functionality
- [ ] Email notifications
- [ ] Admin dashboard
- [ ] Mark items as claimed/resolved
- [ ] User accounts
- [ ] Advanced search filters
- [ ] Item matching algorithm
- [ ] SMS notifications

## Security Notes

- Always validate and sanitize user inputs
- Use prepared statements for database queries
- Consider adding CSRF tokens
- Implement rate limiting for form submissions
- Add admin verification for sensitive operations

## Support

For issues or suggestions, please check:
- Database configuration in `config.php`
- MySQL is running in XAMPP
- Database tables are created (run `db_setup.php`)
- All fields are properly filled in forms

## License

This project is free to use and modify for educational purposes.

## Version

Version 1.0.0 - March 2026
