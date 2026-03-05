<?php
session_start();
include __DIR__ . '/../config/postgres_config.php';

$filter_type   = trim($_GET['type']   ?? '');
$filter_status = trim($_GET['status'] ?? '');
$filter_search = trim($_GET['search'] ?? '');
$page          = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 15;

$where  = "WHERE 1=1";
$params = [];
if ($filter_type   !== '') { $where .= " AND item_type=:type";    $params[':type']   = $filter_type; }
if ($filter_status !== '') { $where .= " AND status=:status";     $params[':status'] = $filter_status; }
if ($filter_search !== '') { $where .= " AND (item_name ILIKE :s OR contact_name ILIKE :s OR location ILIKE :s)"; $params[':s'] = '%'.$filter_search.'%'; }

$cnt = $conn->prepare("SELECT COUNT(*) FROM items $where");
$cnt->execute($params);
$total       = (int)$cnt->fetchColumn();
$total_pages = max(1, (int)ceil($total/$per_page));
$page        = min($page, $total_pages);
$offset      = ($page-1)*$per_page;

$stmt = $conn->prepare("SELECT * FROM items $where ORDER BY created_at DESC LIMIT :lim OFFSET :off");
$stmt->bindValue(':lim', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset,   PDO::PARAM_INT);
foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->execute();
$items = $stmt->fetchAll();

// Global stats
$stats = $conn->query("
    SELECT
        COUNT(*) AS total,
        COUNT(CASE WHEN item_type='lost'  AND status='unresolved' THEN 1 END) AS lost,
        COUNT(CASE WHEN item_type='found' AND status='unresolved' THEN 1 END) AS found,
        COUNT(CASE WHEN status='claimed'  THEN 1 END) AS claimed,
        COUNT(CASE WHEN status='returned' THEN 1 END) AS returned,
        COUNT(CASE WHEN status='archived' THEN 1 END) AS archived
    FROM items
")->fetch();

// Expiry preview count
$expiry_days = max(7, min(365, (int)($_GET['expiry_days'] ?? 30)));
$expiry_cnt  = (int)$conn->prepare("SELECT COUNT(*) FROM items WHERE status='unresolved' AND date_reported < CURRENT_DATE - INTERVAL '{$expiry_days} days'")->execute() ?
    $conn->query("SELECT COUNT(*) FROM items WHERE status='unresolved' AND date_reported < CURRENT_DATE - INTERVAL '{$expiry_days} days'")->fetchColumn() : 0;
$expiry_cnt_stmt = $conn->prepare("SELECT COUNT(*) FROM items WHERE status='unresolved' AND date_reported < CURRENT_DATE - INTERVAL '{$expiry_days} days'");
$expiry_cnt_stmt->execute();
$expiry_cnt = (int)$expiry_cnt_stmt->fetchColumn();

// By category chart
$by_cat = $conn->query("SELECT category, COUNT(*) AS cnt FROM items GROUP BY category ORDER BY cnt DESC LIMIT 8")->fetchAll();
$max_cat = $by_cat ? max(array_column($by_cat,'cnt')) : 1;

// Monthly trend
$trend = $conn->query("
    SELECT TO_CHAR(date_trunc('month',date_reported),'Mon YYYY') AS mon,
           date_trunc('month',date_reported) AS mon_date,
           COUNT(*) AS cnt
    FROM items
    WHERE date_reported >= CURRENT_DATE - INTERVAL '6 months'
    GROUP BY date_trunc('month',date_reported), mon
    ORDER BY mon_date
")->fetchAll();
$max_trend = $trend ? max(array_column($trend,'cnt')) : 1;

function buildQ(array $ov): string {
    $b = ['type'=>$_GET['type']??'','status'=>$_GET['status']??'','search'=>$_GET['search']??'','page'=>$_GET['page']??1];
    return '?'.http_build_query(array_filter(array_merge($b,$ov),fn($v)=>$v!==''&&$v!==0&&$v!=='0'));
}

$bar_cls = ['c1','c2','c3','c4','c5','c6','c7','c8'];
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
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="../index_postgres.php" class="logo"><div class="logo-mark">🎓</div><span>Campus Lost &amp; Found</span></a>
        <button class="nav-toggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
        <ul class="nav-menu">
            <li><a href="../index_postgres.php">Home</a></li>
            <li><a href="../pages/lost_items_postgres.php">Lost Items</a></li>
            <li><a href="../pages/found_items_postgres.php">Found Items</a></li>
            <li><a href="../report_lost_postgres.php">Report Lost</a></li>
            <li><a href="../report_found_postgres.php">Report Found</a></li>
            <li class="nav-admin"><a href="admin_panel.php" class="active">Admin</a></li>
        </ul>
    </div>
</nav>

<div class="admin-wrap">

    <!-- Header -->
    <div class="admin-top">
        <div>
            <h1>Admin Dashboard</h1>
            <p><?php echo date('l, F j, Y'); ?> · Campus Lost &amp; Found</p>
        </div>
        <div style="display:flex;gap:.65rem;flex-wrap:wrap;">
            <a href="../report_lost_postgres.php"  class="btn btn-surface btn-sm">+ Report Lost</a>
            <a href="../report_found_postgres.php" class="btn btn-accent  btn-sm">+ Report Found</a>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><span class="alert-icon">✅</span><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error"><span class="alert-icon">⚠️</span><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="a-stats">
        <div class="a-stat total">
            <div class="a-stat-num"><?php echo (int)$stats['total']; ?></div>
            <div class="a-stat-lbl">Total Reports</div>
        </div>
        <div class="a-stat lost">
            <div class="a-stat-num"><?php echo (int)$stats['lost']; ?></div>
            <div class="a-stat-lbl">Active Lost</div>
        </div>
        <div class="a-stat found">
            <div class="a-stat-num"><?php echo (int)$stats['found']; ?></div>
            <div class="a-stat-lbl">Active Found</div>
        </div>
        <div class="a-stat claimed">
            <div class="a-stat-num"><?php echo (int)$stats['claimed']; ?></div>
            <div class="a-stat-lbl">Claimed</div>
        </div>
        <div class="a-stat returned">
            <div class="a-stat-num"><?php echo (int)$stats['returned']; ?></div>
            <div class="a-stat-lbl">Returned</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-row">
        <div class="chart-panel">
            <h3>Items by Category</h3>
            <?php if ($by_cat): ?>
                <div class="bar-chart">
                    <?php foreach ($by_cat as $i=>$row): ?>
                        <div class="bar-row">
                            <span class="bar-lbl" title="<?php echo htmlspecialchars($row['category']); ?>"><?php echo htmlspecialchars($row['category']); ?></span>
                            <div class="bar-track"><div class="bar-fill <?php echo $bar_cls[$i%8]; ?>" style="width:<?php echo round(($row['cnt']/$max_cat)*100); ?>%"></div></div>
                            <span class="bar-cnt"><?php echo $row['cnt']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?><p style="color:var(--text-4);font-size:.8rem;">No data yet.</p><?php endif; ?>
        </div>

        <div class="chart-panel">
            <h3>Reports — Last 6 Months</h3>
            <?php if ($trend): ?>
                <div class="bar-chart">
                    <?php foreach ($trend as $row): ?>
                        <div class="bar-row">
                            <span class="bar-lbl"><?php echo htmlspecialchars($row['mon']); ?></span>
                            <div class="bar-track"><div class="bar-fill c1" style="width:<?php echo round(($row['cnt']/$max_trend)*100); ?>%"></div></div>
                            <span class="bar-cnt"><?php echo $row['cnt']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?><p style="color:var(--text-4);font-size:.8rem;">No data yet.</p><?php endif; ?>
        </div>
    </div>

    <!-- ⏰ Expiry / Auto-Archive Panel -->
    <div class="expiry-panel">
        <div class="expiry-info">
            <h4>⏰ Auto-Archive Old Reports</h4>
            <p>Automatically archive unresolved items older than a set number of days. Currently <strong style="color:#f5c842;"><?php echo $expiry_cnt; ?> item<?php echo $expiry_cnt!==1?'s':'';?></strong> would be archived with <?php echo $expiry_days;?>-day threshold.</p>
        </div>
        <form method="POST" action="handlers/admin_run_expiry.php" class="expiry-form">
            <label for="expiry_days">Archive items older than</label>
            <input type="number" id="expiry_days" name="expiry_days" value="<?php echo $expiry_days; ?>" min="7" max="365">
            <span style="color:var(--text-3);font-size:.8rem;">days</span>
            <button type="submit" class="btn btn-surface btn-sm"
                onclick="return confirm('Archive <?php echo $expiry_cnt;?> item(s) older than <?php echo $expiry_days;?> days? They will be hidden from public listings.');">
                Run Archive
            </button>
        </form>
    </div>

    <!-- Filters -->
    <div class="a-filters">
        <form method="GET" class="a-filter-row">
            <div class="a-filter-group" style="flex:2;">
                <label>Search</label>
                <input type="text" name="search" placeholder="Item name, reporter, location…" value="<?php echo htmlspecialchars($filter_search);?>" style="min-width:220px;">
            </div>
            <div class="a-filter-group">
                <label>Type</label>
                <select name="type">
                    <option value="">All Types</option>
                    <option value="lost"  <?php echo $filter_type==='lost' ?'selected':'';?>>Lost</option>
                    <option value="found" <?php echo $filter_type==='found'?'selected':'';?>>Found</option>
                </select>
            </div>
            <div class="a-filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="unresolved" <?php echo $filter_status==='unresolved'?'selected':'';?>>Unresolved</option>
                    <option value="claimed"    <?php echo $filter_status==='claimed'   ?'selected':'';?>>Claimed</option>
                    <option value="returned"   <?php echo $filter_status==='returned'  ?'selected':'';?>>Returned</option>
                    <option value="archived"   <?php echo $filter_status==='archived'  ?'selected':'';?>>Archived</option>
                </select>
            </div>
            <div class="a-filter-actions">
                <button type="submit" class="btn btn-accent btn-sm">Filter</button>
                <a href="admin_panel.php" class="btn btn-ghost btn-sm">Reset</a>
            </div>
        </form>
        <div class="a-filter-meta">
            <span><?php echo $total;?> record<?php echo $total!==1?'s':'';?></span>
            <?php if($total_pages>1):?><span>Page <?php echo $page;?> of <?php echo $total_pages;?></span><?php endif;?>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrap">
        <div class="table-scroll">
            <?php if (count($items)>0): ?>
            <table class="a-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Type</th><th>Item</th><th>Location</th>
                        <th>Reporter</th><th>Date</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="td-id">#<?php echo (int)$item['id'];?></td>
                        <td><span class="badge <?php echo $item['item_type']==='lost'?'badge-lost':'badge-found';?>"><?php echo ucfirst($item['item_type']);?></span></td>
                        <td class="td-item">
                            <strong><?php echo htmlspecialchars($item['item_name']);?></strong>
                            <?php if(!empty($item['media_filename'])):?><span style="font-size:.72rem;color:var(--blue);">📎</span><?php endif;?>
                            <span><?php echo htmlspecialchars($item['category']);?></span>
                        </td>
                        <td class="td-loc">📍 <?php echo htmlspecialchars(mb_substr($item['location'],0,28));?><?php echo mb_strlen($item['location'])>28?'…':'';?></td>
                        <td class="td-rep">
                            <?php echo htmlspecialchars($item['contact_name']);?>
                            <a href="mailto:<?php echo htmlspecialchars($item['contact_email']);?>"><?php echo htmlspecialchars($item['contact_email']);?></a>
                        </td>
                        <td class="td-date"><?php echo date('M j, Y',strtotime($item['date_reported']));?></td>
                        <td>
                            <span class="badge badge-<?php echo $item['status'];?>"><?php echo ucfirst($item['status']);?></span>
                        </td>
                        <td class="td-acts">
                            <a href="../pages/view_item_postgres.php?id=<?php echo (int)$item['id'];?>" class="act-btn view" title="View">👁</a>

                            <?php if($item['status']==='unresolved'): ?>
                                <form method="POST" action="handlers/admin_update_status.php" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo (int)$item['id'];?>">
                                    <input type="hidden" name="status" value="claimed">
                                    <button type="submit" class="act-btn claimed" title="Mark Claimed — sends email notification">✅</button>
                                </form>
                                <form method="POST" action="handlers/admin_update_status.php" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo (int)$item['id'];?>">
                                    <input type="hidden" name="status" value="returned">
                                    <button type="submit" class="act-btn returned" title="Mark Returned — sends email notification">🔄</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="handlers/admin_update_status.php" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo (int)$item['id'];?>">
                                    <input type="hidden" name="status" value="unresolved">
                                    <button type="submit" class="act-btn reopen" title="Re-open">↩️</button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" action="handlers/admin_delete_item.php" class="inline-form"
                                onsubmit="return confirm('Permanently delete item #<?php echo (int)$item['id'];?>? This cannot be undone.');">
                                <input type="hidden" name="id" value="<?php echo (int)$item['id'];?>">
                                <button type="submit" class="act-btn del" title="Delete">🗑</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
            <?php else: ?>
                <div style="text-align:center;padding:4rem 2rem;">
                    <p style="font-size:2rem;margin-bottom:.75rem;opacity:.3;">📋</p>
                    <p style="color:var(--text-4);font-size:.875rem;">No records found.</p>
                </div>
            <?php endif;?>
        </div>

        <div class="table-foot">
            <span>Showing <?php echo $total>0?min($offset+1,$total):0;?>–<?php echo min($offset+$per_page,$total);?> of <?php echo $total;?> records
                &nbsp;·&nbsp; <span title="✅ Claimed and 🔄 Returned actions automatically send an email notification to the reporter." style="cursor:help;color:var(--accent-text);opacity:.7;">📧 Email notifications enabled</span>
            </span>
            <?php if($total_pages>1):?>
                <div style="display:flex;gap:.3rem;align-items:center;">
                    <?php if($page>1):?><a href="<?php echo buildQ(['page'=>$page-1]);?>" class="pg-btn btn-sm">←</a><?php endif;?>
                    <?php for($p=max(1,$page-2);$p<=min($total_pages,$page+2);$p++):?>
                        <a href="<?php echo buildQ(['page'=>$p]);?>" class="pg-btn <?php echo $p===$page?'active':'';?>"><?php echo $p;?></a>
                    <?php endfor;?>
                    <?php if($page<$total_pages):?><a href="<?php echo buildQ(['page'=>$page+1]);?>" class="pg-btn">→</a><?php endif;?>
                </div>
            <?php endif;?>
        </div>
    </div>

</div>

<footer><p>&copy; <?php echo date('Y');?> Campus Lost &amp; Found · Admin Panel</p></footer>
<script src="../js/nav.js"></script>
</body>
</html>
<?php $conn = null;?>