<?php
require_once('auth.php');
require_login();
require_once('DBconnect.php');

$today      = date('Y-m-d');
$email      = mysqli_real_escape_string($conn, $_SESSION['email'] ?? $_SESSION['user_id']);
$isBorrower = strtoupper(trim($_SESSION['borrower_flag']    ?? '')) === 'YES';
$isDonor    = strtoupper(trim($_SESSION['donor_flag']       ?? '')) === 'YES';
$isCoord    = strtoupper(trim($_SESSION['coordinator_flag'] ?? '')) === 'YES';
$isBlocked  = false; //Starts assuming not blocked. Will be overwritten by the query result.

if($isBorrower){
    $bc = mysqli_query($conn, "
        SELECT COUNT(*) AS cnt
        FROM loan l
        JOIN requests r ON l.REQUEST_ID = r.REQUEST_ID
        WHERE r.BORROWER_ID = '$email'
          AND DATEDIFF('$today', l.DUE_DATE) >= 15
          AND (l.ACTUAL_RETURN_DATE IS NULL
               OR l.ACTUAL_RETURN_DATE = '0000-00-00'
               OR CAST(l.ACTUAL_RETURN_DATE AS CHAR) = '0000-00-00')");
    if($bc){
        $br        = mysqli_fetch_assoc($bc);
        $isBlocked = (int)$br['cnt'] > 0;
        $_SESSION['blocked'] = $isBlocked;
    }
}

$overdueLoans = [];
if($isBorrower && !$isBlocked){
    $res = mysqli_query($conn, "
        SELECT l.LOAN_ID, l.DEVICE_ID, dn.DEVICE_TYPE, l.DUE_DATE,
               DATEDIFF('$today', l.DUE_DATE) AS DAYS_OVERDUE
        FROM loan l
        JOIN device d     ON l.DEVICE_ID   = d.DEVICE_ID
        JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
        JOIN requests r   ON l.REQUEST_ID  = r.REQUEST_ID
        WHERE (l.ACTUAL_RETURN_DATE IS NULL
               OR l.ACTUAL_RETURN_DATE = '0000-00-00'
               OR CAST(l.ACTUAL_RETURN_DATE AS CHAR) = '0000-00-00')
          AND l.DUE_DATE < '$today'
          AND r.BORROWER_ID = '$email'
        ORDER BY DAYS_OVERDUE DESC");
    while($row = mysqli_fetch_assoc($res)) $overdueLoans[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home - AID SHARE</title>
  <link rel="stylesheet" href="css/bootstrap.min.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <link rel="stylesheet" href="css/animate.min.css"/>
  <link href="css/style.css" rel="stylesheet"/>
  <style>
    .overdue-stack { position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:12px; max-width:380px; }
    .overdue-banner { border-radius:10px; overflow:hidden; font-family:inherit; box-shadow:0 4px 16px rgba(0,0,0,0.25); }
    .overdue-banner .bh { padding:10px 16px; font-weight:700; font-size:.9rem; display:flex; align-items:center; gap:8px; }
    .overdue-banner .bb { padding:10px 16px; font-size:.87rem; line-height:1.6; }
    .b-remind { background:#eafaf1; border:1px solid #27ae60; }
    .b-warn   { background:#fef9e7; border:1px solid #f39c12; }
    .b-alert  { background:#fdedec; border:1px solid #e74c3c; }
    .b-remind .bh { color:#1e8449; background:#d5f5e3; }
    .b-warn   .bh { color:#9a6700; background:#fdebd0; }
    .b-alert  .bh { color:#922b21; background:#fadbd8; }
    .ret-btn { display:inline-block; margin-top:8px; padding:6px 14px; border-radius:4px; text-decoration:none; font-size:.83rem; color:#fff; }
    .b-remind .ret-btn { background:#27ae60; }
    .b-warn   .ret-btn { background:#f39c12; }
    .b-alert  .ret-btn { background:#e74c3c; }

    .block-banner { max-width:680px; margin:40px auto; border-radius:10px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.35); }
    .block-banner .bk-head { background:#7b0000; color:#fff; padding:16px 24px; font-weight:700; font-size:1.05rem; display:flex; align-items:center; gap:10px; }
    .block-banner .bk-body { background:#fff3f3; border:2px solid #7b0000; border-top:none; padding:18px 24px; font-size:.95rem; line-height:1.75; color:#3a0000; }
    .bk-btn { display:inline-block; margin-top:12px; padding:9px 20px; border-radius:5px; background:#7b0000; color:#fff; text-decoration:none; font-size:.9rem; font-weight:600; }
    .bk-btn:hover { background:#5a0000; color:#fff; }
  </style>
</head>
<body>
<header>
  <nav>
    <div class="nav_logo"><h1><a href="home.php">Aid Share</a></h1></div>
    <ul class="nav_link">
      <li><a href="home.php">Home</a></li>
      <?php if(!$isBlocked): ?>
        <?php if($isBorrower): ?>
          <li><a href="request_device.php">Borrow</a></li>
          <li><a href="return_device.php">Return</a></li>
        <?php endif; ?>
        <?php if($isDonor): ?>
          <li><a href="donate.php">Donate</a></li>
        <?php endif; ?>
        <?php if($isCoord): ?>
          <li><a href="coordinator.php">Coordinator</a></li>
        <?php endif; ?>
      <?php endif; ?>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </nav>
</header>

<main>
  <?php if($isBlocked): ?>
  <div class="block-banner">
    <div class="bk-head">&#128683;&nbsp; Account Temporarily Blocked</div>
    <div class="bk-body">
      <p><strong>You have been blocked for failing to return your borrowed device.</strong></p>
      <p style="margin-top:8px;">Your loan is more than 15 days past its due date. You cannot
         request new devices or access any features until this is resolved.</p>
      <p style="margin-top:8px;">Please return the overdue device immediately, or contact a
         coordinator for further information.</p>
      <a class="bk-btn" href="return_device.php">Return Overdue Device</a>
    </div>
  </div>

  <?php else: ?>
  <div class="overdue-stack">
  <?php foreach($overdueLoans as $loan):
    $days = (int)$loan['DAYS_OVERDUE'];
    if($days >= 14){
      $cls='b-alert'; $icon='&#128721;';
      $head='Action Required — Device Seriously Overdue';
      $msg="Your loan of <strong>{$loan['DEVICE_TYPE']}</strong> (ID: <strong>{$loan['DEVICE_ID']}</strong>)
            was due on <strong>{$loan['DUE_DATE']}</strong> — <strong>$days days ago</strong>.
            A coordinator may contact you. Please return it immediately.";
    } elseif($days >= 5){
      $cls='b-warn'; $icon='&#9888;';
      $head='Overdue Notice — Please Return Soon';
      $msg="Your loan of <strong>{$loan['DEVICE_TYPE']}</strong> (ID: <strong>{$loan['DEVICE_ID']}</strong>)
            was due on <strong>{$loan['DUE_DATE']}</strong> — <strong>$days days ago</strong>.
            Someone on the waitlist may be waiting.";
    } else {
      $cls='b-remind'; $icon='&#8987;';
      $head='Friendly Reminder — Return Due';
      $msg="Your loan of <strong>{$loan['DEVICE_TYPE']}</strong> (ID: <strong>{$loan['DEVICE_ID']}</strong>)
            was due on <strong>{$loan['DUE_DATE']}</strong> —
            <strong>$days day".($days>1?'s':'')." ago</strong>. Please return it when you can.";
    }
  ?>
  <div class="overdue-banner <?php echo $cls; ?>">
    <div class="bh"><?php echo "$icon  $head"; ?></div>
    <div class="bb">
      <p><?php echo $msg; ?></p>
      <a class="ret-btn" href="return_device.php">Return This Device</a>
    </div>
  </div>
  <?php endforeach; ?>
  </div>

  <div class="home-hero">
    <h1>Welcome to Aid Share</h1>
    <p>Connecting people who need assistive devices with those who can lend them.</p>
    <?php if($isBorrower || $isDonor): ?>
      <p style="font-size:.95rem;opacity:.8;margin-top:6px;">Use the menu above to get started.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</main>
</body>
</html>