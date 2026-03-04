# Firebase System Architecture 🏗️

## How Your Lost & Found App Talks to Firebase

```
┌─────────────────────────────────────────────────────────────────────┐
│                         YOUR BROWSER                                │
│  http://localhost/LAF/index_firebase.php                           │
└────────────────────────────────┬────────────────────────────────────┘
                                 │
                    User clicks "Report Lost Item"
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      HTML FORM PAGE                                 │
│  (report_lost.php)                                                 │
│                                                                    │
│  ┌─────────────────────────────────────────────────────────────┐  │
│  │ Item Name: ________________                               │  │
│  │ Category: [Dropdown]                                       │  │
│  │ Description: ___________________________                  │  │
│  │ Date Lost: __/__/____                                     │  │
│  │ Location: ________________                               │  │
│  │ Your Name: ________________                               │  │
│  │ Email: ________________                                   │  │
│  │ Phone: ________________                                   │  │
│  │                                                           │  │
│  │ [SUBMIT BUTTON] ← User clicks here                       │  │
│  └─────────────────────────────────────────────────────────────┘  │
└────────────────────────────────┬────────────────────────────────────┘
                                 │
                    User fills form and clicks Submit
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────┐
│              FORM HANDLER (handlers/process_report..)              │
│                                                                    │
│  1. Get form data from $_POST                                      │
│  2. Validate data (check email format, date, etc)                 │
│  3. Call Firebase function: addItem($itemData)                     │
│  4. Function returns success/error                                 │
│  5. Redirect back to home or show error                           │
└────────────────────────────────┬────────────────────────────────────┘
                                 │
                    Form data + Validation ✅
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────────┐
│         FIREBASE CONFIG (config/firebase_config.php)               │
│                                                                    │
│  Contains:                                                         │
│  ✓ FIREBASE_PROJECT_ID = "college-lost-found-abc123"            │
│  ✓ FIREBASE_API_KEY = "AIzaSy..."                               │
│  ✓ FIREBASE_DATABASE_URL = "https://...firebaseio.com"         │
│                                                                    │
│  Functions available:                                              │
│  • addItem($data)           → POST item to Firebase                │
│  • getAllItems($type)       → GET items from Firebase              │
│  • getItemById($id)         → GET one item                         │
│  • updateItem($id, $data)   → UPDATE item                          │
│  • deleteItem($id)          → DELETE item                          │
│  • searchItems($term)       → SEARCH items                         │
└────────────────────────────────┬────────────────────────────────────┘
                                 │
                   Using REST API + CURL
                   (HTTP Requests over Internet)
                                 │
                                 ▼
╔═════════════════════════════════════════════════════════════════════╗
║                    🌐 FIREBASE CLOUD                               ║
║                                                                    ║
║  Google's Servers (somewhere on the internet)                     ║
║                                                                    ║
║  Realtime Database storage:                                       ║
║  {                                                                ║
║    "items": {                                                     ║
║      "item_abc123": {                    ← Your lost item saved  ║
║        "item_name": "Red Phone",                                 ║
║        "item_type": "lost",                                      ║
║        "status": "unresolved",                                   ║
║        "category": "Electronics",                                ║
║        "description": "Red phone with...",                       ║
║        "location": "Library",                                    ║
║        "contact_name": "Your Name",                              ║
║        "contact_email": "email@gmail.com",                       ║
║        "date_reported": "2026-01-15"                            ║
║      }                                                            ║
║    }                                                              ║
║  }                                                                ║
║                                                                    ║
║  ✅ Data is NOW SAVED on Google Servers                          ║
║  ✅ Anyone with API key can access it                            ║
║  ✅ Data is backed up automatically                              ║
║  ✅ Changes appear REAL-TIME everywhere                          ║
║                                                                    ║
╚═════════════════════════════════════════════════════════════════════╝
                                 ▲
                                 │
                   Firebase Returns: {"name": "item_abc123"}
                   (Indicates item was saved successfully)
                                 │
                                 │
┌────────────────────────────────┴────────────────────────────────────┐
│              BACK TO YOUR BROWSER                                   │
│                                                                    │
│  ✅ Success message shown: "Item reported successfully!"          │
│  ✅ User redirected to home page                                  │
│  ✅ Statistics updated to show new item                           │
│                                                                    │
└─────────────────────────────────────────────────────────────────────┘
```

---

## When User Views Items

```
User clicks "View Lost Items"
        ↓
loads: pages/lost_items_firebase.php
        ↓
Calls: getAllItems('lost', 'unresolved')
        ↓
Function makes HTTPS request to Firebase:
  GET https://PROJECT_ID.firebaseio.com/items.json?auth=API_KEY
        ↓
Firebase returns ALL items in JSON format
        ↓
PHP loops through and displays as cards
        ↓
User sees items in browser ✅
```

---

## File Organization

```
c:\xampp\apache\htdocs\LAF\
│
├── 📁 config/
│   ├── firebase_config.php      ← Your credentials + functions
│   └── config.php               ← MySQL config (backup)
│
├── 📁 css/
│   └── style.css                ← All styling
│
├── 📁 pages/
│   ├── lost_items_firebase.php  ← View lost items
│   ├── found_items_firebase.php ← View found items
│   ├── view_item_firebase.php   ← View item details
│   ├── lost_items.php           ← MySQL version
│   ├── found_items.php          ← MySQL version
│   └── view_item.php            ← MySQL version
│
├── 📁 handlers/
│   ├── process_report_firebase.php  ← Handle Firebase forms
│   └── process_report.php           ← Handle MySQL forms
│
├── 📁 database/
│   └── db_setup.php             ← Create MySQL tables
│
├── 📁 docs/
│   ├── FIREBASE_BEGINNER_GUIDE.md         ← ⭐ START HERE
│   ├── FIREBASE_CREDENTIALS_CHECKLIST.md  ← Use this
│   ├── FIREBASE_SETUP_GUIDE.md
│   ├── QUICK_START_FIREBASE.md
│   ├── MYSQL_VS_FIREBASE.md
│   └── SYSTEM_OVERVIEW.md
│
├── 📄 index_firebase.php        ← Home page (Firebase)
├── 📄 index.php                 ← Home page (MySQL)
├── 📄 report_lost.php           ← Report lost form
├── 📄 report_found.php          ← Report found form
└── 📄 README.md                 ← Original docs
```

---

## Data Flow Examples

### Example 1: Add New Item
```
User submits form
    ↓
handlers/process_report_firebase.php receives data
    ↓
Validates: Is email correct? Is date in past?
    ↓
Calls: addItem($itemData)
    ↓
firebase_config.php makes HTTP POST request
    ↓
Firebase API: POST /items.json
    ↓
Firebase creates new item with unique ID
    ↓
Returns: {"name": "item_abc123"}
    ↓
PHP shows: "Success! Item added"
    ↓
Redirect to home page
```

### Example 2: Search for Items
```
User enters search term: "phone"
    ↓
pages/lost_items_firebase.php processes search
    ↓
Calls: getAllItems('lost', 'unresolved')
    ↓
firebase_config.php makes HTTP GET request
    ↓
Firebase API: GET /items.json
    ↓
Firebase sends ALL lost items as JSON
    ↓
PHP loops through and checks:
  - Does description contain "phone"?
  - Does name contain "phone"?
    ↓
Displays only matching items
    ↓
User sees search results
```

### Example 3: View Item Details
```
User clicks on item from list
    ↓
pages/view_item_firebase.php?id=item_abc123
    ↓
Calls: getItemById('item_abc123')
    ↓
firebase_config.php makes HTTP GET request
    ↓
Firebase API: GET /items/item_abc123.json
    ↓
Firebase returns just that ONE item
    ↓
PHP displays: name, description, contact info, etc
    ↓
User can click "Call" or "Email" to contact finder
```

---

## What Happens Behind the Scenes

```
Your Code:
    addItem(['item_name' => 'Phone', ...])
        ↓
firebase_config.php does this magic:
    1. Create JSON from data
    2. Add your credentials
    3. Make HTTPS request to Firebase
    4. Send JSON data
    5. Wait for Firebase response
    6. Return success/error to the function
        ↓
Your form handler gets result
        ↓
Shows message to user
```

---

## Key Files You Need to Know

| File | Purpose | When Used |
|------|---------|-----------|
| `config/firebase_config.php` | **Credentials + Functions** | Every Firebase request |
| `handlers/process_report_firebase.php` | **Receives form, sends to Firebase** | When user submits form |
| `pages/lost_items_firebase.php` | **Gets items from Firebase, displays** | When user views items |
| `pages/view_item_firebase.php` | **Gets one item, shows details** | When user clicks on item |

---

## How Your Credentials Are Used

```
config/firebase_config.php:
    define('FIREBASE_PROJECT_ID', 'college-lost-found-abc123');
    define('FIREBASE_API_KEY', 'AIzaSyDx...');
    define('FIREBASE_DATABASE_URL', 'https://college-lost-found-abc123.firebaseio.com');
                ↓
These are combined to make Firebase API request:
                ↓
    https://college-lost-found-abc123.firebaseio.com/items.json?auth=AIzaSyDx...
                ↓
Firebase recognizes:
    ✓ This is project: college-lost-found-abc123
    ✓ Using API key: AIzaSyDx... (this user has permission)
    ✓ Access database: firebaseio.com
                ↓
Firebase grants access and returns data ✅
```

---

## Summary

1. **User interacts** with your website
2. **Form is submitted** to handlers/process_report_firebase.php
3. **Data is validated** (check email, date, etc)
4. **firebase_config.php provides credentials** + makes HTTP request
5. **Request goes over INTERNET** to Firebase servers
6. **Firebase saves data** in cloud database
7. **Result returned** to your app
8. **User sees success message** ✅

**It all happens in under 1 second!**

---

## Next Steps

1. ✅ Read: `FIREBASE_BEGINNER_GUIDE.md` (in docs folder)
2. ✅ Fill: `FIREBASE_CREDENTIALS_CHECKLIST.md` with your credentials
3. ✅ Update: `config/firebase_config.php` with your credentials
4. ✅ Test: Go to `http://localhost/LAF/index_firebase.php`
5. ✅ Report: Submit a test item
6. ✅ Verify: Check in Firebase Console that item was saved

**You're all set!** 🚀
