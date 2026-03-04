# Firebase Quick Start (5 Minutes)

## Step 1: Create Firebase Project (2 min)

1. Go to: https://console.firebase.google.com/
2. Sign in with Google
3. Click **"Add Project"**
4. Name: `college-lost-found`
5. Accept terms → **Create**

---

## Step 2: Create Database (1 min)

1. Click **"Realtime Database"** (left menu)
2. Click **"Create Database"**
3. Region: `us-central1`
4. Mode: **"Start in Test Mode"**
5. Click **"Enable"**

---

## Step 3: Get Credentials (1 min)

Copy these 3 things:

**A) Database URL**
- In Realtime Database page, look at the URL bar
- Copy from: `https://...` to `.com`
- Example: `https://college-lost-found-abc123.firebaseio.com`

**B) API Key**
- Click ⚙️ icon (top right) → **"Project Settings"**
- Go to **"General"** tab
- Find "Web API Key" 
- Copy it

**C) Project ID**
- In Project Settings, copy "Project ID"
- Example: `college-lost-found-abc123`

---

## Step 4: Add to Config (1 min)

Open: `firebase_config.php`

Replace these lines:
```php
define('FIREBASE_PROJECT_ID', 'college-lost-found-abc123');
define('FIREBASE_API_KEY', 'AIzaSyDx1234567890_abc');
define('FIREBASE_DATABASE_URL', 'https://college-lost-found-abc123.firebaseio.com');
```

Save the file.

---

## Step 5: Test It!

1. Go to: `http://localhost/LAF/index_firebase.php`
2. Click "Report Lost Item"
3. Fill form and submit
4. Go to Firebase Console → Find your item in JSON! ✅

---

## Navigation

After setup, bookmark these URLs:

- **Home**: http://localhost/LAF/index_firebase.php
- **Lost Items**: http://localhost/LAF/lost_items_firebase.php
- **Found Items**: http://localhost/LAF/found_items_firebase.php

---

## Done! 🎉

System is now using Firebase instead of MySQL!

### What's Different?
- No MySQL database needed
- Data is in Firebase Cloud
- Real-time updates
- Automatic backups
- Scales automatically

### Files Used:
- `index_firebase.php` - Home page
- `lost_items_firebase.php` - Lost items list
- `found_items_firebase.php` - Found items list
- `firebase_config.php` - Configuration
- `report_lost.php` → `process_report_firebase.php`
- `report_found.php` → `process_report_firebase.php`

---

## Issues?

**"Error: Firebase database not found"**
- Check credentials in `firebase_config.php`
- Make sure database URL has no typos

**"Items not saving"**
- Verify API Key is correct
- Check Firebase is in "Test Mode"

**"Nothing showing up"**
- Wait 2 seconds (sync delay)
- Refresh browser
- Check Firebase Console

---

## Next Steps

1. ✅ Database setup done
2. 🔒 Set security rules (see FIREBASE_SETUP_GUIDE.md)
3. 📧 Add email notifications (Firebase Extensions)
4. 📱 Build mobile app (Firebase Mobile SDK)

---

For detailed info: Read **FIREBASE_SETUP_GUIDE.md**

