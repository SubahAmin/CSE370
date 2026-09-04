<?php
require_once('auth.php');
require_role('borrower_flag');
require_once('DBconnect.php');

$today = date('Y-m-d');
$email = mysqli_real_escape_string($conn, $_SESSION['email'] ?? $_SESSION['user_id']);

$bc = mysqli_query($conn, "
    SELECT COUNT(*) AS cnt FROM loan l
    JOIN requests r ON l.REQUEST_ID = r.REQUEST_ID
    WHERE r.BORROWER_ID = '$email'
      AND DATEDIFF('$today', l.DUE_DATE) >= 15
      AND (l.ACTUAL_RETURN_DATE IS NULL
           OR l.ACTUAL_RETURN_DATE = '0000-00-00'
           OR CAST(l.ACTUAL_RETURN_DATE AS CHAR) = '0000-00-00')");
if($bc){ $br = mysqli_fetch_assoc($bc); if((int)$br['cnt'] > 0){ header("Location: home.php"); exit(); } }

$typeResult = mysqli_query($conn, "SELECT DISTINCT DEVICE_TYPE FROM donations ORDER BY DEVICE_TYPE");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Request a Device - AID SHARE</title>
  <link rel="stylesheet" href="css/bootstrap.min.css"/>
  <link rel="stylesheet" href="css/font-awesome.min.css"/>
  <link rel="stylesheet" href="css/animate.min.css"/>
  <link href="css/style.css" rel="stylesheet"/>
</head>
<body>
<header>
  <nav>
    <div class="nav_logo"><h1><a href="home.php">Aid Share</a></h1></div>
    <ul class="nav_link">
      <li><a href="home.php">Home</a></li>
      <li><a href="request_device.php">Borrow</a></li>
      <li><a href="return_device.php">Return</a></li>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </nav>
</header>
<main>
  <section class="add_device_box">
    <h1>Request a Device</h1>
    <?php if(isset($_GET['error'])): ?>
      <p style="color:red; margin-bottom:10px;">Please fill in every field.</p>
    <?php endif; ?>
    <form class="add_device_form" action="request_process.php" method="post">
      <select name="device_type" required style="width:100%; padding:15px; margin-bottom:20px; border:1px solid #ccc; border-radius:4px; font-size:16px;">
        <option value="">-- Select Device Type --</option>
        <?php while($t = mysqli_fetch_assoc($typeResult)): ?>
          <option value="<?php echo htmlspecialchars($t['DEVICE_TYPE']); ?>">
            <?php echo htmlspecialchars($t['DEVICE_TYPE']); ?>
          </option>
        <?php endwhile; ?>
      </select>
      <input type="number" name="duration" placeholder="Estimated days needed" min="1" required/>
      <input type="submit" value="Request Device"/>
    </form>
  </section>
</main>
</body>
</html>