<?php
session_start();
include __DIR__ . '/../config/postgres_config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: ../index_postgres.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM items WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();
if (!$item) { header("Location: ../index_postgres.php"); exit; }

// ── Smart matching: same category + keyword overlap + ±30 days ─────────────
$opposite = $item['item_type'] === 'lost' ? 'found' : 'lost';

// Extract keywords from item name for scoring
$keywords = array_filter(explode(' ', preg_replace('/[^a-z0-9 ]/', '', strtolower($item['item_name']))), fn($w) => strlen($w) > 3);

$match_stmt = $conn->prepare("
    SELECT id, item_name, location, date_reported, contact_name, category, description
    FROM items
    WHERE item_type  = :type
      AND status     = 'unresolved'
      AND id        != :id
      AND date_reported BETWEEN :d_from AND :d_to
    ORDER BY
        CASE WHEN category = :cat THEN 0 ELSE 1 END,
        ABS(date_reported - :ref_date) ASC
    LIMIT 5
");
$match_stmt->execute([
    ':type'     => $opposite,
    ':id'       => $id,
    ':cat'      => $item['category'],
    ':d_from'   => date('Y-m-d', strtotime($item['date_reported'] . ' -30 days')),
    ':d_to'     => date('Y-m-d', strtotime($item['date_reported'] . ' +30 days')),
    ':ref_date' => $item['date_reported'],
]);
$raw_matches = $match_stmt->fetchAll();

// Score matches by keyword overlap
$matches = [];
foreach ($raw_matches as $m) {
    $m_words = strtolower($m['item_name'] . ' ' . $m['description']);
    $score   = 0;
    foreach ($keywords as $kw) {
        if (str_contains($m_words, $kw)) $score++;
    }
    $label = $m['category'] === $item['category'] ? 'Same Category' : 'Same Timeframe';
    if ($score > 0)  $label = "🔑 Keyword Match ({$score})";
    $m['score_label'] = $label;
    $m['score']       = $score + ($m['category'] === $item['category'] ? 1 : 0);
    $matches[] = $m;
}
usort($matches, fn($a, $b) => $b['score'] - $a['score']);
$matches = array_slice($matches, 0, 4);

// ── Timeline steps ──────────────────────────────────────────────────────────
$done_submitted = true;
$done_review    = in_array($item['status'], ['unresolved','claimed','returned','archived']);
$done_claimed   = in_array($item['status'], ['claimed','returned']);
$done_returned  = $item['status'] === 'returned';

$timeline = [
    ['label' => 'Report Submitted',   'sub' => date('M j, Y', strtotime($item['created_at'])), 'done' => $done_submitted],
    ['label' => 'Under Review',        'sub' => 'Active on the system',                          'done' => $done_review],
    ['label' => ucfirst($item['item_type']==='lost'?'Item Claimed':'Item Returned'), 'sub' => $done_claimed?'Completed':'Pending', 'done' => $done_claimed],
    ['label' => 'Resolved',            'sub' => $done_returned?'Completed':'Pending',             'done' => $done_returned],
];

$back_url   = $item['item_type'] === 'lost' ? 'lost_items_postgres.php'  : 'found_items_postgres.php';
$back_label = $item['item_type'] === 'lost' ? 'Lost Items' : 'Found Items';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['item_name']); ?> – Campus Lost &amp; Found</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="../index_postgres.php" class="logo"><div class="logo-mark">🎓</div><span>Campus Lost &amp; Found</span></a>
        <button class="nav-toggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
        <ul class="nav-menu">
            <li><a href="../index_postgres.php">Home</a></li>
            <li><a href="lost_items_postgres.php">Lost Items</a></li>
            <li><a href="found_items_postgres.php">Found Items</a></li>
            <li><a href="../report_lost_postgres.php">Report Lost</a></li>
            <li><a href="../report_found_postgres.php">Report Found</a></li>
            <li class="nav-admin"><a href="../admin/admin_panel.php">Admin</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;">
        <a href="<?php echo $back_url; ?>" class="back-link">← Back to <?php echo $back_label; ?></a>
        <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>
    </div>

    <!-- Match Banner -->
    <?php if (count($matches) > 0): ?>
    <div class="match-banner">
        <div class="match-banner-icon">🔗</div>
        <div style="flex:1;">
            <h4>Possible Matches Found</h4>
            <p>We found <?php echo count($matches); ?> <?php echo $opposite; ?> item<?php echo count($matches)>1?'s':''; ?> that may be related — same timeframe<?php echo count(array_filter($matches,fn($m)=>$m['category']===$item['category']))>0?', same category':''; ?>, or keyword overlap.</p>
            <div class="match-list">
                <?php foreach ($matches as $m): ?>
                    <div class="match-item">
                        <div>
                            <strong><?php echo htmlspecialchars($m['item_name']); ?></strong>
                            <span>📍 <?php echo htmlspecialchars($m['location']); ?> · <?php echo date('M j', strtotime($m['date_reported'])); ?></span>
                        </div>
                        <div style="display:flex;gap:.5rem;align-items:center;flex-shrink:0;">
                            <span class="match-score"><?php echo htmlspecialchars($m['score_label']); ?></span>
                            <a href="view_item_postgres.php?id=<?php echo (int)$m['id']; ?>" class="btn btn-ghost btn-sm">View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="detail-grid">
        <!-- Main -->
        <div class="detail-main">

            <!-- Title -->
            <div class="title-panel">
                <div class="badges-row">
                    <span class="badge <?php echo $item['item_type']==='lost'?'badge-lost':'badge-found'; ?>"><?php echo ucfirst($item['item_type']); ?></span>
                    <span class="badge badge-cat"><?php echo htmlspecialchars($item['category']); ?></span>
                    <span class="badge badge-<?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span>
                </div>
                <h1><?php echo htmlspecialchars($item['item_name']); ?></h1>
                <div class="title-panel-meta">
                    <span>📅 <?php echo date('F j, Y', strtotime($item['date_reported'])); ?></span>
                    <span>📍 <?php echo htmlspecialchars($item['location']); ?></span>
                    <span>🕒 Reported <?php echo date('M j, Y', strtotime($item['created_at'])); ?></span>
                </div>
            </div>

            <!-- Media -->
            <?php if (!empty($item['media_filename'])): ?>
            <div class="detail-panel">
                <div class="detail-panel-head"><h3>Attached Media</h3></div>
                <div class="detail-panel-body" style="padding:1rem;">
                    <div class="media-box">
                        <?php if ($item['media_type']==='image'): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>" alt="Item photo">
                        <?php else: ?>
                            <video controls><source src="../uploads/<?php echo htmlspecialchars($item['media_filename']); ?>"></video>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Details -->
            <div class="detail-panel">
                <div class="detail-panel-head"><h3>Item Details</h3></div>
                <div class="detail-panel-body">
                    <div class="detail-info-grid">
                        <div class="info-cell"><div class="lbl">Date <?php echo ucfirst($item['item_type']); ?></div><div class="val"><?php echo date('F j, Y', strtotime($item['date_reported'])); ?></div></div>
                        <div class="info-cell"><div class="lbl">Location</div><div class="val"><?php echo htmlspecialchars($item['location']); ?></div></div>
                        <div class="info-cell"><div class="lbl">Category</div><div class="val"><?php echo htmlspecialchars($item['category']); ?></div></div>
                        <div class="info-cell"><div class="lbl">Status</div><div class="val"><?php echo ucfirst($item['status']); ?></div></div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="detail-panel">
                <div class="detail-panel-head"><h3>Description</h3></div>
                <div class="detail-panel-body">
                    <p class="desc-body"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="detail-side">

            <!-- Contact -->
            <div class="contact-panel">
                <h4>Contact Reporter</h4>
                <div class="contact-row"><div class="c-icon">👤</div><span><?php echo htmlspecialchars($item['contact_name']); ?></span></div>
                <div class="contact-row"><div class="c-icon">✉️</div><a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>?subject=Re: <?php echo urlencode($item['item_name']); ?>"><?php echo htmlspecialchars($item['contact_email']); ?></a></div>
                <?php if (!empty($item['contact_phone'])): ?>
                    <div class="contact-row"><div class="c-icon">☎️</div><span><?php echo htmlspecialchars($item['contact_phone']); ?></span></div>
                <?php endif; ?>
                <a href="mailto:<?php echo htmlspecialchars($item['contact_email']); ?>?subject=Re: <?php echo urlencode($item['item_name']); ?> (Campus Lost and Found)"
                   class="btn btn-accent" style="width:100%;justify-content:center;margin-top:1rem;">
                    📧 Send Email
                </a>
            </div>

            <!-- Timeline -->
            <div class="detail-panel">
                <div class="detail-panel-head"><h3>Status Timeline</h3></div>
                <div class="detail-panel-body">
                    <div class="timeline">
                        <?php foreach ($timeline as $step): ?>
                            <div class="tl-item">
                                <div class="tl-dot <?php echo $step['done']?'done':'pending'; ?>">
                                    <?php echo $step['done']?'✓':'○'; ?>
                                </div>
                                <div class="tl-content">
                                    <strong><?php echo $step['label']; ?></strong>
                                    <span><?php echo $step['sub']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Tip -->
            <div class="tip-box">
                💡 <strong>Safety tip:</strong> If this is your item, contact the reporter via email. Always verify your identity in a safe, public location when collecting items.
            </div>

            <a href="<?php echo $back_url; ?>" class="btn btn-ghost" style="width:100%;justify-content:center;">← Back to <?php echo $back_label; ?></a>
        </div>
    </div>
</div>

<footer><p>&copy; <?php echo date('Y'); ?> Campus Lost &amp; Found</p></footer>
<script src="../js/nav.js"></script>
</body>
</html>
<?php $conn = null; ?>