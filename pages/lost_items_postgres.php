<?php
session_start();
include __DIR__ . '/../config/postgres_config.php';

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');
$sort     = trim($_GET['sort']     ?? 'newest');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 9;

$order_map = [
    'newest'  => 'date_reported DESC, created_at DESC',
    'oldest'  => 'date_reported ASC,  created_at ASC',
    'name_az' => 'item_name ASC',
    'name_za' => 'item_name DESC',
];
$order = $order_map[$sort] ?? $order_map['newest'];

$where  = "WHERE item_type='lost' AND status='unresolved'";
$params = [];

if ($search !== '') {
    $where .= " AND (item_name ILIKE :s OR description ILIKE :s OR location ILIKE :s)";
    $params[':s'] = '%' . $search . '%';
}
if ($category !== '') {
    $where .= " AND category = :cat";
    $params[':cat'] = $category;
}

$total       = (int)$conn->prepare("SELECT COUNT(*) FROM items $where")->execute($params) ? $conn->prepare("SELECT COUNT(*) FROM items $where")->execute($params) : 0;
$cnt_stmt    = $conn->prepare("SELECT COUNT(*) FROM items $where");
$cnt_stmt->execute($params);
$total       = (int)$cnt_stmt->fetchColumn();
$total_pages = max(1, (int)ceil($total / $per_page));
$page        = min($page, $total_pages);
$offset      = ($page - 1) * $per_page;

$stmt = $conn->prepare("SELECT * FROM items $where ORDER BY $order LIMIT :lim OFFSET :off");
$stmt->bindValue(':lim', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset,   PDO::PARAM_INT);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->execute();
$items = $stmt->fetchAll();

$cats = $conn->query("SELECT DISTINCT category FROM items WHERE item_type='lost' ORDER BY category")->fetchAll();

function q(array $ov): string {
    $b = ['search'=>$_GET['search']??'','category'=>$_GET['category']??'','sort'=>$_GET['sort']??'newest','page'=>$_GET['page']??1];
    return '?'.http_build_query(array_filter(array_merge($b,$ov),fn($v)=>$v!==''&&$v!==0&&$v!=='0'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Items – Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="../index_postgres.php" class="logo"><div class="logo-mark">🎓</div><span>Campus Lost &amp; Found</span></a>
        <button class="nav-toggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
        <ul class="nav-menu">
            <li><a href="../index_postgres.php">Home</a></li>
            <li><a href="lost_items_postgres.php" class="active">Lost Items</a></li>
            <li><a href="found_items_postgres.php">Found Items</a></li>
            <li><a href="../report_lost_postgres.php">Report Lost</a></li>
            <li><a href="../report_found_postgres.php">Report Found</a></li>
            <li class="nav-admin"><a href="../admin/admin_panel.php">Admin</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1>Lost Items</h1>
        <p>Items reported as lost by campus members. Spot yours? Contact the reporter directly.</p>
    </div>

    <div class="filters-bar">
        <form method="GET" class="filters-row">
            <div class="filter-group" style="flex:2;min-width:200px;">
                <label>Search</label>
                <input type="text" name="search" placeholder="Name, description, location…" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label>Category</label>
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($cats as $c): ?>
                        <option value="<?php echo htmlspecialchars($c['category']); ?>" <?php echo $category===$c['category']?'selected':''; ?>>
                            <?php echo htmlspecialchars($c['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Sort</label>
                <select name="sort">
                    <option value="newest"  <?php echo $sort==='newest' ?'selected':''; ?>>Newest First</option>
                    <option value="oldest"  <?php echo $sort==='oldest' ?'selected':''; ?>>Oldest First</option>
                    <option value="name_az" <?php echo $sort==='name_az'?'selected':''; ?>>Name A–Z</option>
                    <option value="name_za" <?php echo $sort==='name_za'?'selected':''; ?>>Name Z–A</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-accent">Search</button>
                <a href="lost_items_postgres.php" class="btn btn-ghost">Reset</a>
            </div>
        </form>
        <div class="results-meta">
            <span><?php echo $total; ?> item<?php echo $total!==1?'s':''; ?></span>
            <?php if($total_pages>1):?><span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span><?php endif; ?>
        </div>
    </div>

    <div class="items-grid">
        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $i => $item): ?>
                <div class="item-card" style="animation-delay:<?php echo $i*0.055; ?>s">
                    <?php if (!empty($item['media_filename']) && $item['media_type']==='image'): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>" alt="" class="item-card-img">
                    <?php elseif (!empty($item['media_filename']) && $item['media_type']==='video'): ?>
                        <video class="item-card-img" muted><source src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>"></video>
                    <?php endif; ?>

                    <div class="item-card-body">
                        <div class="badges-row">
                            <span class="badge badge-lost">Lost</span>
                            <span class="badge badge-cat"><?php echo htmlspecialchars($item['category']); ?></span>
                            <?php if (!empty($item['media_filename'])): ?><span class="badge badge-media">📎 Media</span><?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <div class="item-meta">
                            <div class="item-meta-row">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <?php echo date('M j, Y', strtotime($item['date_reported'])); ?>
                            </div>
                            <div class="item-meta-row">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <?php echo htmlspecialchars($item['location']); ?>
                            </div>
                        </div>
                        <p class="item-desc"><?php $d=htmlspecialchars($item['description']); echo mb_strlen($d)>95?mb_substr($d,0,95).'…':$d; ?></p>
                        <div class="card-sep"></div>
                        <div class="item-reporter">
                            <strong><?php echo htmlspecialchars($item['contact_name']); ?></strong>
                            · <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>"><?php echo htmlspecialchars($item['contact_email']); ?></a>
                        </div>
                    </div>
                    <div class="item-card-foot">
                        <a href="view_item_postgres.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-surface" style="width:100%;justify-content:center;">View Details →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <span class="empty-state-icon">🔍</span>
                <h3>No lost items found</h3>
                <p><?php echo ($search||$category)?'Try adjusting your filters.':'No lost items reported yet.'; ?></p>
                <a href="../report_lost_postgres.php" class="btn btn-accent">Report a Lost Item</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page>1):?><a href="<?php echo q(['page'=>$page-1]); ?>" class="pg-btn">←</a><?php else:?><span class="pg-btn dis">←</span><?php endif; ?>
            <?php $s2=max(1,$page-2);$e=min($total_pages,$page+2);
            if($s2>1):?><a href="<?php echo q(['page'=>1]); ?>" class="pg-btn">1</a><?php if($s2>2):?><span class="pg-btn dis">…</span><?php endif;endif;
            for($p=$s2;$p<=$e;$p++):?><a href="<?php echo q(['page'=>$p]); ?>" class="pg-btn <?php echo $p===$page?'active':''; ?>"><?php echo $p;?></a><?php endfor;
            if($e<$total_pages):if($e<$total_pages-1):?><span class="pg-btn dis">…</span><?php endif;?><a href="<?php echo q(['page'=>$total_pages]); ?>" class="pg-btn"><?php echo $total_pages;?></a><?php endif; ?>
            <?php if($page<$total_pages):?><a href="<?php echo q(['page'=>$page+1]); ?>" class="pg-btn">→</a><?php else:?><span class="pg-btn dis">→</span><?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<footer><p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found</p></footer>
<script src="../js/nav.js"></script>
</body>
</html>
<?php $conn = null; ?>