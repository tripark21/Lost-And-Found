# Firebase Setup Guide - Lost & Found System

## Overview

This guide explains how to convert the Lost & Found system from **MySQL (SQL)** to **Firebase (NoSQL)**.

### Key Differences:
- **MySQL**: Relational database (tables, rows, columns), runs on server
- **Firebase Realtime Database**: Cloud-based NoSQL (JSON structure), data stored in Firebase servers
- **Advantages of Firebase**: 
  - No server database setup needed
  - Real-time synchronization
  - Cloud backup automatic
  - Scalable
  - Free tier available

---

## Step 1: Create Firebase Project

### 1.1 Go to Firebase Console
1. Visit: https://console.firebase.google.com/
2. Sign in with Google account (create one if needed)

### 1.2 Create New Project
1. Click **"Add Project"**
2. Enter Project Name: `college-lost-found`
3. Accept terms and click **"Create Project"**
4. Wait for project to be created

### 1.3 Create Realtime Database
1. In Firebase Console, click **"Realtime Database"** (left sidebar)
2. Click **"Create Database"**
3. Select Region: `us-central1` (or closest to you)
4. Choose Rules:
   - Select **"Start in Test Mode"** (for development)
   - Click **"Enable"**

---

## Step 2: Get Firebase Credentials

### 2.1 Get Database URL
1. In Realtime Database section, copy the database URL
2. It looks like: `https://college-lost-found-xxxxx.firebaseio.com`

### 2.2 Get API Key
1. Click **"Project Settings"** (gear icon, top right)
2. Go to **"Service Accounts"** tab
3. Scroll down and click **"Database Secrets"** (if available)
4. Or use the Web API Key from **"General"** tab
5. Copy the API Key

### 2.3 Get Project ID
1. In Project Settings, copy the **Project ID**

---

## Step 3: Update Firebase Config

1. Open [firebase_config.php](firebase_config.php)
2. Replace credentials:

```php
define('FIREBASE_PROJECT_ID', 'your-project-id-here');
define('FIREBASE_API_KEY', 'your-api-key-here');
define('FIREBASE_DATABASE_URL', 'https://your-project-id.firebaseio.com');
```

### Example:
```php
define('FIREBASE_PROJECT_ID', 'college-lost-found-abc123');
define('FIREBASE_API_KEY', 'AIzaSyDx1234567890_abcdefghijk');
define('FIREBASE_DATABASE_URL', 'https://college-lost-found-abc123.firebaseio.com');
```

---

## Step 4: Update Report Forms

The report forms need to point to Firebase handler.

### 4.1 Update report_lost.php
Change the form action from:
```html
<form method="POST" action="process_report.php" ...>
```

To:
```html
<form method="POST" action="process_report_firebase.php" ...>
```

### 4.2 Update report_found.php
Same change as above - update form action to `process_report_firebase.php`

---

## Step 5: Test the System

### 5.1 Start XAMPP
1. Start Apache
2. No need to start MySQL anymore!

### 5.2 Test Firebase Files
1. Navigate to: `http://localhost/LAF/index_firebase.php`
2. Try to report an item (Report Lost/Found)
3. Check if item appears in lists

### 5.3 Check Firebase Console
1. Go to Firebase Console
2. Click on **"Realtime Database"**
3. You should see your items appearing as JSON data:

```json
{
  "items": {
    "-NxKxJk...": {
      "item_type": "lost",
      "item_name": "Blue Backpack",
      "description": "Missing blue backpack...",
      "category": "Bags & Backpacks",
      "date_reported": "2026-03-04",
      "location": "Library",
      "status": "unresolved",
      "contact_name": "John Doe",
      "contact_email": "john@college.edu",
      "contact_phone": "123-456-7890",
      "created_at": "2026-03-04 10:30:45"
    }
  }
}
```

---

## File Mapping

### Original MySQL Files → Firebase Files

| MySQL Version | Firebase Version | Purpose |
|--------------|-----------------|---------|
| config.php | firebase_config.php | Database configuration |
| index.php | index_firebase.php | Home page |
| lost_items.php | lost_items_firebase.php | Browse lost items |
| found_items.php | found_items_firebase.php | Browse found items |
| view_item.php | view_item_firebase.php | Item details |
| process_report.php | process_report_firebase.php | Handle form submissions |
| report_lost.php | report_lost.php | Report lost form (use with firebase) |
| report_found.php | report_found.php | Report found form (use with firebase) |

---

## Firebase Functions in firebase_config.php

```php
// Get all items (optionally filter by type and status)
getAllItems($type = null, $status = 'unresolved')

// Get single item by ID
getItemById($id)

// Add new item
addItem($itemData)

// Update existing item
updateItem($id, $itemData)

// Delete item
deleteItem($id)

// Get distinct categories
getCategories($type = null)

// Search items with filtering
searchItems($searchTerm, $type, $category = null)
```

---

## Setting Security Rules (Important!)

By default, Firebase is in "Test Mode" - anyone can read/write data. For production:

1. Go to **Realtime Database** → **Rules** tab
2. Replace with secure rules:

```json
{
  "rules": {
    "items": {
      ".read": true,
      ".write": false,
      ".indexOn": ["item_type", "category", "status"]
    }
  }
}
```

This allows:
- **Read**: Anyone can view items
- **Write**: Only authenticated users (you can add auth later)

---

## Troubleshooting

### Issue: "Connection failed" / No items showing
**Solution:**
1. Check firebase_config.php - verify all credentials
2. Make sure database URL has no trailing slash
3. Enable CORS in Firebase (already handled by REST API)

### Issue: "401 Unauthorized" error
**Solution:**
- Verify API Key is correct
- Check if database is in "Test Mode"
- Ensure database URL is correct

### Issue: Changes not appearing
**Solution:**
1. Wait 1-2 seconds (Firebase needs time to sync)
2. Refresh browser
3. Check Firebase Console to see if data was actually added

### Issue: Search not working
**Solution:**
- Search is done client-side in PHP
- Make sure items have the correct fields
- Check browser console for errors

---

## Advantages of Firebase

✅ **No server setup** - Database hosted in cloud  
✅ **Real-time** - Changes sync instantly  
✅ **Scalable** - Handles millions of items  
✅ **Automatic backups** - Data is backed up by Google  
✅ **Free tier** - Generous free usage limits  
✅ **Security** - Google-managed infrastructure  
✅ **Analytics** - Built-in analytics dashboard  

---

## Limitations

⚠️ Need internet connection (can't work offline)  
⚠️ Firebase costs money after free tier  
⚠️ JSON structure less normalized than SQL  
⚠️ Complex queries harder than SQL  
⚠️ Cold start responses (slightly slower first request)  

---

## Next Steps

1. **Add Authentication**: Let users sign up/login
2. **Add Images**: Store photos with items
3. **Email Notifications**: Notify when matches found
4. **Mobile App**: Create Android/iOS app
5. **Admin Dashboard**: Manage items and users

---

## Quick Reference - URL Navigation

After setup, use these URLs:

- **Home**: `http://localhost/LAF/index_firebase.php`
- **Lost Items**: `http://localhost/LAF/lost_items_firebase.php`
- **Found Items**: `http://localhost/LAF/found_items_firebase.php`
- **Report Lost**: `http://localhost/LAF/report_lost.php` (points to firebase handler)
- **Report Found**: `http://localhost/LAF/report_found.php` (points to firebase handler)

---

## Firebase Data Structure Example

```javascript
lost_found_db
├── items
│   ├── -NxKxJk1234...
│   │   ├── item_type: "lost"
│   │   ├── item_name: "Blue Backpack"
│   │   ├── description: "..."
│   │   └── created_at: "2026-03-04"
│   ├── -MyKyJk5678...
│   │   ├── item_type: "found"
│   │   └── ...
```

---

For detailed Firebase documentation: https://firebase.google.com/docs/database

