<?php
/**
 * Email Notification Helper
 * Campus Lost & Found
 *
 * Uses PHP's built-in mail() function.
 * For production, replace with PHPMailer + SMTP for reliable delivery.
 */

function sendStatusNotification(array $item, string $new_status): bool {
    $to      = $item['contact_email'];
    $name    = $item['contact_name'];
    $item_name = $item['item_name'];
    $type    = ucfirst($item['item_type']);
    $site    = 'Campus Lost & Found';

    if ($new_status === 'claimed') {
        $subject = "✅ Your {$type} Report Has Been Claimed — {$item_name}";
        $action  = 'claimed';
        $color   = '#4d9fff';
        $icon    = '🎉';
        $message = "Great news! Your {$type} report for <strong>{$item_name}</strong> has been marked as <strong>claimed</strong> by our admin team.";
        $detail  = "This means someone has identified this item as theirs. If you haven't already been contacted, please check your inbox for any messages.";
    } elseif ($new_status === 'returned') {
        $subject = "🎉 Your Item Has Been Returned — {$item_name}";
        $action  = 'returned';
        $color   = '#00d68f';
        $icon    = '✨';
        $message = "Wonderful news! Your {$type} report for <strong>{$item_name}</strong> has been marked as <strong>returned</strong>.";
        $detail  = "The item has been successfully reunited with its owner. Thank you for helping our campus community!";
    } else {
        return false; // Don't send for other status changes
    }

    $year = date('Y');
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>{$subject}</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#0a0a0a; color:#e0e0e0; }
    .wrap { max-width:560px; margin:40px auto; }
    .card { background:#111; border:1px solid #222; border-radius:12px; overflow:hidden; }
    .top  { background:linear-gradient(135deg,#0f1e38,#0a2a20); padding:32px; text-align:center; border-bottom:1px solid #222; }
    .top .logo { font-size:13px; font-weight:700; color:#888; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:20px; }
    .top .icon { font-size:2.5rem; margin-bottom:12px; }
    .top h1 { font-size:1.3rem; color:#fff; font-weight:700; letter-spacing:-0.01em; }
    .body { padding:28px 32px; }
    .badge { display:inline-block; background:{$color}1a; color:{$color}; border:1px solid {$color}33; padding:4px 12px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:16px; }
    .body p { font-size:14px; line-height:1.7; color:#aaa; margin-bottom:12px; }
    .item-box { background:#1a1a1a; border:1px solid #2a2a2a; border-radius:8px; padding:16px 20px; margin:20px 0; }
    .item-box .lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#555; margin-bottom:6px; }
    .item-box .val { font-size:1.1rem; font-weight:700; color:#fff; }
    .footer { padding:20px 32px; border-top:1px solid #1a1a1a; text-align:center; font-size:12px; color:#444; }
    .footer a { color:#00d68f; text-decoration:none; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="top">
        <div class="logo">🎓 {$site}</div>
        <div class="icon">{$icon}</div>
        <h1>Item Status Updated</h1>
      </div>
      <div class="body">
        <span class="badge">{$action}</span>
        <p>Hi <strong style="color:#e0e0e0">{$name}</strong>,</p>
        <p>{$message}</p>
        <div class="item-box">
          <div class="lbl">Item Reported</div>
          <div class="val">{$item_name}</div>
        </div>
        <p>{$detail}</p>
        <p style="margin-top:20px;">If you have any questions, please contact your campus lost &amp; found office.</p>
      </div>
      <div class="footer">
        <p>&copy; {$year} {$site}. This is an automated notification.</p>
      </div>
    </div>
  </div>
</body>
</html>
HTML;

    // Plain text fallback
    $plain = strip_tags(str_replace(['<br>', '<br/>', '</p>', '</div>'], "\n", $html));

    $boundary = md5(time());
    $headers  = implode("\r\n", [
        "From: {$site} <noreply@campus-laf.edu>",
        "Reply-To: noreply@campus-laf.edu",
        "MIME-Version: 1.0",
        "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
        "X-Mailer: PHP/" . PHP_VERSION,
    ]);

    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n{$plain}\r\n\r\n";
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n{$html}\r\n\r\n";
    $body .= "--{$boundary}--";

    return @mail($to, $subject, $body, $headers);
}