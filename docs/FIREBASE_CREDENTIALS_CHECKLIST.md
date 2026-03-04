# Firebase Credentials Checklist ✅

Use this sheet to collect your Firebase information before adding to code.

---

## 📋 Your Firebase Credentials

Copy each item from Firebase Console and paste here:

### ✏️ Project Name
```
Name: ________________________
(What you called your Firebase project)
```

### 🆔 Project ID
```
Where to find: Firebase Console → Project Settings → General tab (top)
Your Project ID:  ________________________

Example: college-lost-found-abc123
```

### 🔗 Database URL
```
Where to find: Realtime Database page → URL in browser address bar
Your Database URL:  ________________________

Example: https://college-lost-found-abc123.firebaseio.com

⚠️ IMPORTANT: Must include https:// at start and end with .com
```

### 🔑 API Key
```
Where to find: Project Settings → General tab → Web API Key (scroll down)
Your API Key:  ________________________

Example: AIzaSyDx1234567890_abcdefghijk

⚠️ IMPORTANT: This is SECRET - don't share it!
```

---

## 📝 Code Update

Once you have all 3 credentials above, do this:

### Step 1: Open File
```
Path: c:\xampp\apache\htdocs\LAF\config\firebase_config.php
```

### Step 2: Find These Lines (should be at TOP)
```php
define('FIREBASE_PROJECT_ID', 'your-firebase-project-id');
define('FIREBASE_API_KEY', 'your-firebase-api-key');
define('FIREBASE_DATABASE_URL', 'https://your-firebase-project-id.firebaseio.com');
```

### Step 3: Replace Each One

**Replace Line 1:**
- OLD: `define('FIREBASE_PROJECT_ID', 'your-firebase-project-id');`
- NEW: `define('FIREBASE_PROJECT_ID', '________________________');`
  - (Paste Your Project ID inside quotes)

**Replace Line 2:**
- OLD: `define('FIREBASE_API_KEY', 'your-firebase-api-key');`
- NEW: `define('FIREBASE_API_KEY', '________________________');`
  - (Paste Your API Key inside quotes)

**Replace Line 3:**
- OLD: `define('FIREBASE_DATABASE_URL', 'https://your-firebase-project-id.firebaseio.com');`
- NEW: `define('FIREBASE_DATABASE_URL', '________________________');`
  - (Paste Your Database URL inside quotes)

### Step 4: Save
- Press **Ctrl+S**
- Done! ✅

---

## ✅ Verification Checklist

After entering credentials, check these:

- [ ] Project ID has NO spaces
- [ ] API Key is longer than 20 characters
- [ ] Database URL starts with `https://`
- [ ] Database URL ends with `.firebaseio.com`
- [ ] File is SAVED (no dot indicator in editor)
- [ ] You can access `http://localhost/LAF/index_firebase.php` without errors

---

## 🧪 Test Your Setup

### Quick Test:
1. Go to: `http://localhost/LAF/index_firebase.php`
2. You should see homepage ✅
3. No red errors = Success! 🎉

### Full Test:
1. Click "Report Lost Item"
2. Fill form (use fake data for testing)
3. Click Submit
4. If redirected home without error = Working! ✅

### Verify in Firebase:
1. Go to Firebase Console
2. Go to Realtime Database
3. See your test item? = Perfect! 🎉

---

## 🆘 Emergency Copy-Paste Template

If you get confused, use this template:

```php
<?php
// Firebase Configuration
define('FIREBASE_PROJECT_ID', 'PASTE_PROJECT_ID_HERE');
define('FIREBASE_API_KEY', 'PASTE_API_KEY_HERE');
define('FIREBASE_DATABASE_URL', 'PASTE_DATABASE_URL_HERE');
```

Then replace:
- `PASTE_PROJECT_ID_HERE` with your actual Project ID
- `PASTE_API_KEY_HERE` with your actual API Key  
- `PASTE_DATABASE_URL_HERE` with your actual Database URL

---

## 📞 Still Confused?

| Problem | Solution |
|---------|----------|
| Can't find Project ID | Go to Firebase → Settings ⚙️ icon → General tab |
| Can't find API Key | Same place as Project ID (scroll down) |
| Can't find Database URL | Go to Realtime Database → look at URL bar of browser |
| Getting errors | Double-check NO extra spaces in credentials |
| Credentials expired | They don't! Just copy fresh from Firebase |

---

**Remember:** Keep this file safe! You'll need these credentials if you need to update them later.
