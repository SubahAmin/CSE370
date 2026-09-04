<?php
require_once('auth.php');
require_role('coordinator_flag');
require_once('DBconnect.php');

$today = date('Y-m-d');

$waitlistSql = "
    SELECT w.WAITLIST_ID, w.QUEUE_POSITION, w.NOTIFIED_FLAG,
           w.DEVICE_ID, dn.DEVICE_TYPE,
           r.REQUEST_DATE, r.ESTIMATED_DURATION, r.BORROWER_ID,
           l.DUE_DATE AS EXPECTED_AVAILABLE
    FROM waitlist w
    JOIN requests r   ON w.REQUEST_ID  = r.REQUEST_ID
    JOIN device d     ON w.DEVICE_ID   = d.DEVICE_ID
    JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
    LEFT JOIN loan l  ON l.DEVICE_ID   = w.DEVICE_ID
                      AND l.ACTUAL_RETURN_DATE = '0000-00-00'
    ORDER BY w.DEVICE_ID, w.QUEUE_POSITION ASC";
$waitlistResult = mysqli_query($conn, $waitlistSql);

$maintSql = "
    SELECT m.MAINTAINENCE_ID, m.LOG_DATE, m.CLEARED_FOR_LENDING,
           m.NOTES, m.COORDINATOR_ID,
           m.DEVICE_ID, dn.DEVICE_TYPE, d.DEVICE_CONDITION
    FROM maintainence m
    JOIN device d     ON m.DEVICE_ID   = d.DEVICE_ID
    JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
    ORDER BY m.LOG_DATE DESC, m.MAINTAINENCE_ID DESC";
$maintResult = mysqli_query($conn, $maintSql);

$overdueSql = "
    SELECT l.LOAN_ID, l.DEVICE_ID, dn.DEVICE_TYPE,
           l.DUE_DATE, r.BORROWER_ID,
           DATEDIFF('$today', l.DUE_DATE) AS DAYS_OVERDUE
    FROM loan l
    JOIN device d     ON l.DEVICE_ID   = d.DEVICE_ID
    JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
    JOIN requests r   ON l.REQUEST_ID  = r.REQUEST_ID
    WHERE l.ACTUAL_RETURN_DATE = '0000-00-00'
      AND l.DUE_DATE < '$today'
    ORDER BY DAYS_OVERDUE DESC";
$overdueResult = mysqli_query($conn, $overdueSql);

$deviceSql = "
    SELECT d.DEVICE_ID, dn.DEVICE_TYPE, dn.DESCRIPTION_TEXT,
           d.DEVICE_CONDITION, d.STATUS
    FROM device d
    JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
    ORDER BY dn.DEVICE_TYPE, d.DEVICE_ID";
$deviceResult = mysqli_query($conn, $deviceSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coordinator - AID SHARE</title>
  <link rel="stylesheet" href="css/bootstrap.min.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <link rel="stylesheet" href="css/animate.min.css"/>
  <link href="css/style.css" rel="stylesheet"/>
  <style>
    /* nav panels */
    .nav-panel {
      display: none;
      width: 100%;
      background: rgba(20,20,20,0.96);
      border-top: 2px solid rgba(255,255,255,0.12);
      padding: 22px 5vw;
      animation: fadeSlide 0.18s ease;
    }
    @keyframes fadeSlide {
      from { opacity:0; transform:translateY(-6px); }
      to   { opacity:1; transform:translateY(0); }
    }
    .nav-panel h3 { color:#fff; font-size:1.05rem; font-weight:600; margin-bottom:14px; }
    .panel-empty  { color:#aaa; font-style:italic; }
    .nav-tab-link.nav-active { color:#f0c040 !important; border-bottom:2px solid #f0c040; padding-bottom:2px; }

    /* tables inside nav panels */
    .panel-table { width:100%; border-collapse:collapse; font-size:.88rem; margin-top:6px; }
    .panel-table th { background:#2c3e50; color:#fff; padding:9px 12px; text-align:center; white-space:nowrap; }
    .panel-table td { padding:8px 12px; text-align:center; border-bottom:1px solid rgba(255,255,255,0.1); color:#ddd; }
    .panel-table tr:last-child td { border-bottom:none; }
    .panel-table tr:nth-child(even) td { background:rgba(255,255,255,0.04); }
    .panel-table tr:hover td { background:rgba(255,255,255,0.09); }

    /* badges */
    .badge-r { background:#27ae60; color:#fff; padding:2px 8px; border-radius:10px; font-size:.78rem; }
    .badge-w { background:#f39c12; color:#fff; padding:2px 8px; border-radius:10px; font-size:.78rem; }
    .badge-a { background:#e74c3c; color:#fff; padding:2px 8px; border-radius:10px; font-size:.78rem; }

    /* device table in main */
    .device-main { max-width:1100px; margin:36px auto 60px; padding:32px 28px;
                   background:rgba(255,255,255,0.55); border-radius:10px;
                   box-shadow:0 2px 8px rgba(0,0,0,0.15); }
    .device-main h2 { text-align:center; font-weight:500; font-size:1.45rem; margin-bottom:20px; }
    .coord-table { width:100%; border-collapse:collapse; font-size:.91rem; }
    .coord-table th { background:#2c3e50; color:#fff; padding:10px 14px; text-align:center; white-space:nowrap; }
    .coord-table td { padding:9px 14px; text-align:center; border-bottom:1px solid #ddd; }
    .coord-table tr:last-child td { border-bottom:none; }
    .coord-table tr:nth-child(even) td { background:#f5f7fa; }
    .coord-table tr:hover td { background:#eaf0fb; }
    .empty-note { text-align:center; opacity:.65; font-style:italic; padding:16px 0; }
  </style>
</head>
<body>
<header>
  <nav>
    <div class="nav_logo"><h1><a href="home.php">Aid Share</a></h1></div>
    <ul class="nav_link">
      <li><a href="home.php" onclick="closeAll()">Home</a></li>
      <li><a href="#" class="nav-tab-link" onclick="toggle('overdue',this)">Overdue</a></li>
      <li><a href="#" class="nav-tab-link" onclick="toggle('waitlist',this)">Waitlist</a></li>
      <li><a href="#" class="nav-tab-link" onclick="toggle('maintenance',this)">Maintenance</a></li>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </nav>

  <!-- OVERDUE panel -->
  <div id="panel-overdue" class="nav-panel">
    <h3>&#9888; Overdue Loans</h3>
    <?php if(!$overdueResult || mysqli_num_rows($overdueResult) === 0): ?>
      <p class="panel-empty">No overdue loans right now.</p>
    <?php else: ?>
    <table class="panel-table">
      <tr><th>Loan ID</th><th>Device ID</th><th>Type</th><th>Borrower</th><th>Due Date</th><th>Days Overdue</th><th>Status</th></tr>
      <?php while($row = mysqli_fetch_assoc($overdueResult)):
        $days = (int)$row['DAYS_OVERDUE'];
        if($days >= 14)    $badge = '<span class="badge-a">Critical</span>';
        elseif($days >= 5) $badge = '<span class="badge-w">Follow Up</span>';
        else               $badge = '<span class="badge-r">Reminder</span>';
      ?>
      <tr>
        <td><?php echo htmlspecialchars($row['LOAN_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_TYPE']); ?></td>
        <td><?php echo htmlspecialchars($row['BORROWER_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DUE_DATE']); ?></td>
        <td><strong style="color:#fff;"><?php echo $days; ?></strong></td>
        <td><?php echo $badge; ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <?php endif; ?>
  </div>

  <!-- WAITLIST panel -->
  <div id="panel-waitlist" class="nav-panel">
    <h3>Current Waitlist</h3>
    <?php if(!$waitlistResult || mysqli_num_rows($waitlistResult) === 0): ?>
      <p class="panel-empty">No one is currently on the waitlist.</p>
    <?php else: ?>
    <table class="panel-table">
      <tr><th>Pos</th><th>Waitlist ID</th><th>Device ID</th><th>Type</th>
          <th>Borrower</th><th>Request Date</th><th>Est. Days</th><th>Expected Available</th><th>Notified</th></tr>
      <?php while($row = mysqli_fetch_assoc($waitlistResult)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['QUEUE_POSITION']); ?></td>
        <td><?php echo htmlspecialchars($row['WAITLIST_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_TYPE']); ?></td>
        <td><?php echo htmlspecialchars($row['BORROWER_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['REQUEST_DATE']); ?></td>
        <td><?php echo htmlspecialchars($row['ESTIMATED_DURATION']); ?></td>
        <td><?php echo htmlspecialchars($row['EXPECTED_AVAILABLE'] ?? 'Unknown'); ?></td>
        <td><?php echo htmlspecialchars($row['NOTIFIED_FLAG']); ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <?php endif; ?>
  </div>

  <!-- MAINTENANCE panel -->
  <div id="panel-maintenance" class="nav-panel">
    <h3>Maintenance Log</h3>
    <?php if(!$maintResult || mysqli_num_rows($maintResult) === 0): ?>
      <p class="panel-empty">No maintenance records yet.</p>
    <?php else: ?>
    <table class="panel-table">
      <tr><th>ID</th><th>Date</th><th>Device ID</th><th>Type</th>
          <th>Condition</th><th>Notes</th><th>Cleared</th><th>Logged By</th></tr>
      <?php while($row = mysqli_fetch_assoc($maintResult)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['MAINTAINENCE_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['LOG_DATE']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_TYPE']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_CONDITION']); ?></td>
        <td><?php echo htmlspecialchars($row['NOTES']); ?></td>
        <td><?php echo htmlspecialchars($row['CLEARED_FOR_LENDING']); ?></td>
        <td><?php echo htmlspecialchars($row['COORDINATOR_ID']); ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <?php endif; ?>
  </div>


</header>

<main>
  <!-- Device inventory always visible -->
  <div class="device-main">
    <h2>Device Inventory</h2>
    <?php if(!$deviceResult || mysqli_num_rows($deviceResult) === 0): ?>
      <p class="empty-note">No devices in the system yet.</p>
    <?php else: ?>
    <table class="coord-table">
      <tr><th>Device ID</th><th>Type</th><th>Description</th><th>Condition</th><th>Status</th></tr>
      <?php while($row = mysqli_fetch_assoc($deviceResult)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['DEVICE_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_TYPE']); ?></td>
        <td><?php echo htmlspecialchars($row['DESCRIPTION_TEXT']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_CONDITION']); ?></td>
        <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <?php endif; ?>
  </div>
</main>

<script>
var active = null, activeLink = null;
function toggle(name, link) {
  event.preventDefault();
  var panel = document.getElementById('panel-' + name);
  if(active && active !== panel) { active.style.display='none'; activeLink.classList.remove('nav-active'); }
  if(!active || active !== panel) {
    panel.style.display='block'; link.classList.add('nav-active');
    active=panel; activeLink=link;
  } else {
    panel.style.display='none'; link.classList.remove('nav-active');
    active=null; activeLink=null;
  }
}
function closeAll() {
  if(active){ active.style.display='none'; activeLink.classList.remove('nav-active'); active=null; activeLink=null; }
}
</script>
</body>
</html>