<?php
// Firebase Configuration
define('FIREBASE_PROJECT_ID', 'your-firebase-project-id');
define('FIREBASE_API_KEY', 'your-firebase-api-key');
define('FIREBASE_DATABASE_URL', 'https://your-firebase-project-id.firebaseio.com');

// Firebase Realtime Database Reference
define('FIREBASE_DB', FIREBASE_DATABASE_URL . '/items.json');

// Function to make HTTP requests to Firebase
function makeFirebaseRequest($url, $method = 'GET', $data = null) {
    $url .= '?auth=' . FIREBASE_API_KEY;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// Get all items from Firebase
function getAllItems($type = null, $status = 'unresolved') {
    $result = makeFirebaseRequest(FIREBASE_DB);
    
    if ($result['status'] != 200 || !is_array($result['data'])) {
        return [];
    }
    
    $items = [];
    foreach ($result['data'] as $key => $item) {
        $item['id'] = $key;
        
        // Filter by type if specified
        if ($type && isset($item['item_type']) && $item['item_type'] !== $type) {
            continue;
        }
        
        // Filter by status
        if (isset($item['status']) && $item['status'] !== $status) {
            continue;
        }
        
        $items[] = $item;
    }
    
    return $items;
}

// Get single item by ID
function getItemById($id) {
    $url = FIREBASE_DATABASE_URL . '/items/' . $id . '.json';
    $result = makeFirebaseRequest($url);
    
    if ($result['status'] == 200 && $result['data']) {
        $result['data']['id'] = $id;
        return $result['data'];
    }
    
    return null;
}

// Add new item to Firebase
function addItem($itemData) {
    $result = makeFirebaseRequest(FIREBASE_DB, 'POST', $itemData);
    return $result;
}

// Update item in Firebase
function updateItem($id, $itemData) {
    $url = FIREBASE_DATABASE_URL . '/items/' . $id . '.json';
    $result = makeFirebaseRequest($url, 'PATCH', $itemData);
    return $result;
}

// Delete item from Firebase
function deleteItem($id) {
    $url = FIREBASE_DATABASE_URL . '/items/' . $id . '.json';
    $result = makeFirebaseRequest($url, 'DELETE');
    return $result;
}

// Get distinct categories
function getCategories($type = null) {
    $items = getAllItems($type);
    $categories = [];
    
    foreach ($items as $item) {
        if (isset($item['category']) && !in_array($item['category'], $categories)) {
            $categories[] = $item['category'];
        }
    }
    
    sort($categories);
    return $categories;
}

// Search items
function searchItems($searchTerm, $type, $category = null) {
    $items = getAllItems($type);
    $results = [];
    
    foreach ($items as $item) {
        $matchSearch = stripos($item['item_name'], $searchTerm) !== false || 
                       stripos($item['description'], $searchTerm) !== false;
        
        $matchCategory = $category === null || $item['category'] === $category;
        
        if ($matchSearch && $matchCategory) {
            $results[] = $item;
        }
    }
    
    // Sort by date (newest first)
    usort($results, function($a, $b) {
        return strtotime($b['date_reported']) - strtotime($a['date_reported']);
    });
    
    return $results;
}
?>
