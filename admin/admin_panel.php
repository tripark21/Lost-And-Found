//- http://localhost/LAF/admin/admin_panel.php


<?php
session_start();
include __DIR__ . '/../config/postgres_config.php';

// ── Filters ──────────────────────────────────────────────────
$filter_type   = isset($_GET['type'])     ? trim($_GET['type'])     : '';
$filter_status = isset($_GET['status'])   ? trim($_GET['status'])   : '';
$filter_search = isset($_GET['search'])   ? trim($_GET['search'])   : '';

$where  = "WHERE 1=1";
$params = [];

if ($filter_type !== '') {
    $where .= " AND item_type = :type";
    $params[':type'] = $filter_type;
}
if ($filter_status !== '') {
    $where .= " AND status = :status";
    $params[':status'] = $filter_status;
}
if ($filter_search !== '') {
    $where .= " AND (item_name ILIKE :search OR contact_name ILIKE :search OR location ILIKE :search)";
    $params[':search'] = '%' . $filter_search . '%';
}

$stmt = $conn->prepare("SELECT * FROM items $where ORDER BY created_at DESC");
$stmt->execute($params);
$items = $stmt->fetchAll();

// ── Stats ─────────────────────────────────────────────────────
$stats_stmt = $conn->query("
    SELECT
        COUNT(*) AS total,
        COUNT(CASE WHEN item_type='lost'  AND status='unresolved' THEN 1 END) AS lost,
        COUNT(CASE WHEN item_type='found' AND status='unresolved' THEN 1 END) AS found,
        COUNT(CASE WHEN status='claimed'  THEN 1 END) AS claimed,
        COUNT(CASE WHEN status='returned' THEN 1 END) AS returned
    FROM items
");
$stats = $stats_stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel – Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-body">

<nav class="navbar">
    <div class="nav-container">
        <h1 class="logo">🎓 Campus Lost &amp; Found</h1>
        <ul class="nav-menu">
            <li><a href="../index_postgres.php">Home</a></li>
            <li><a href="../pages/lost_items_postgres.php">Lost Items</a></li>
            <li><a href="../pages/found_items_postgres.php">Found Items</a></li>
            <li><a href="../report_lost_postgres.php">Report Lost</a></li>
            <li><a href="../report_found_postgres.php">Report Found</a></li>
            <li><a href="admin_panel.php" class="active">Admin</a></li>
        </ul>
    </div>
</nav>

<div class="admin-container">

    <!-- Header -->
    <div class="admin-header">
        <div>
            <h2 class="admin-title">Admin Panel</h2>
            <p class="admin-subtitle">Manage all lost and found reports</p>
        </div>
        <a href="../report_lost_postgres.php" class="btn btn-primary">+ New Report</a>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="admin-stats">
        <div class="admin-stat-card total">
            <span class="stat-number"><?php echo (int)$stats['total']; ?></span>
            <span class="stat-label">Total Reports</span>
        </div>
        <div class="admin-stat-card lost">
            <span class="stat-number"><?php echo (int)$stats['lost']; ?></span>
            <span class="stat-label">Active Lost</span>
        </div>
        <div class="admin-stat-card found">
            <span class="stat-number"><?php echo (int)$stats['found']; ?></span>
            <span class="stat-label">Active Found</span>
        </div>
        <div class="admin-stat-card claimed">
            <span class="stat-number"><?php echo (int)$stats['claimed']; ?></span>
            <span class="stat-label">Claimed</span>
        </div>
        <div class="admin-stat-card returned">
            <span class="stat-number"><?php echo (int)$stats['returned']; ?></span>
            <span class="stat-label">Returned</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="admin-filters">
        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search name, reporter, location…"
                value="<?php echo htmlspecialchars($filter_search); ?>" class="filter-input">

            <select name="type" class="filter-select">
                <option value="">All Types</option>
                <option value="lost"  <?php echo $filter_type === 'lost'  ? 'selected' : ''; ?>>Lost</option>
                <option value="found" <?php echo $filter_type === 'found' ? 'selected' : ''; ?>>Found</option>
            </select>

            <select name="status" class="filter-select">
                <option value="">All Statuses</option>
                <option value="unresolved" <?php echo $filter_status === 'unresolved' ? 'selected' : ''; ?>>Unresolved</option>
                <option value="claimed"    <?php echo $filter_status === 'claimed'    ? 'selected' : ''; ?>>Claimed</option>
                <option value="returned"   <?php echo $filter_status === 'returned'   ? 'selected' : ''; ?>>Returned</option>
            </select>

            <button type="submit" class="btn btn-search">Filter</button>
            <a href="admin_panel.php" class="btn btn-secondary">Reset</a>
        </form>
        <p class="results-count"><?php echo count($items); ?> record(s) found</p>
    </div>

    <!-- Table -->
    <div class="admin-table-wrap">
        <?php if (count($items) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Reporter</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr class="table-row status-row-<?php echo $item['status']; ?>">
                    <td class="td-id">#<?php echo (int)$item['id']; ?></td>
                    <td>
                        <span class="item-type <?php echo $item['item_type']; ?>">
                            <?php echo strtoupper($item['item_type']); ?>
                        </span>
                    </td>
                    <td class="td-name">
                        <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                        <?php if (!empty($item['media_filename'])): ?>
                            <span class="media-icon" title="Has media">📎</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                    <td class="td-location">📍 <?php echo htmlspecialchars($item['location']); ?></td>
                    <td class="td-reporter">
                        <div><?php echo htmlspecialchars($item['contact_name']); ?></div>
                        <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>" class="reporter-email">
                            <?php echo htmlspecialchars($item['contact_email']); ?>
                        </a>
                    </td>
                    <td class="td-date"><?php echo date('M j, Y', strtotime($item['date_reported'])); ?></td>
                    <td>
                        <span class="status-pill status-<?php echo $item['status']; ?>">
                            <?php echo ucfirst($item['status']); ?>
                        </span>
                    </td>
                    <td class="td-actions">
                        <!-- View -->
                        <a href="../pages/view_item_postgres.php?id=<?php echo (int)$item['id']; ?>"
                            class="action-btn view" title="View">👁</a>

                        <!-- Status Change -->
                        <?php if ($item['status'] === 'unresolved'): ?>
                            <form method="POST" action="handlers/admin_update_status.php" class="inline-form">
                                <input type="hidden" name="id"     value="<?php echo (int)$item['id']; ?>">
                                <input type="hidden" name="status" value="claimed">
                                <button type="submit" class="action-btn claimed" title="Mark as Claimed">✅</button>
                            </form>
                            <form method="POST" action="handlers/admin_update_status.php" class="inline-form">
                                <input type="hidden" name="id"     value="<?php echo (int)$item['id']; ?>">
                                <input type="hidden" name="status" value="returned">
                                <button type="submit" class="action-btn returned" title="Mark as Returned">🔄</button>
                            </form>
                        <?php elseif ($item['status'] !== 'unresolved'): ?>
                            <form method="POST" action="handlers/admin_update_status.php" class="inline-form">
                                <input type="hidden" name="id"     value="<?php echo (int)$item['id']; ?>">
                                <input type="hidden" name="status" value="unresolved">
                                <button type="submit" class="action-btn reopen" title="Re-open">↩️</button>
                            </form>
                        <?php endif; ?>

                        <!-- Delete -->
                        <form method="POST" action="handlers/admin_delete_item.php" class="inline-form"
                            onsubmit="return confirm('Delete this item? This cannot be undone.');">
                            <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
                            <button type="submit" class="action-btn delete" title="Delete">🗑</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="no-items" style="padding: 3rem; text-align:center;">
                <p>No records found matching your filters.</p>
            </div>
        <?php endif; ?>
    </div>

</div><!-- /admin-container -->

<footer>
    <p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found &mdash; Admin Panel</p>
</footer>

</body>
</html>
<?php $conn = null; ?>