<?php
require_once('auth.php');
require_role('borrower_flag');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Request Status - AID SHARE</title>
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/animate.min.css" />
  <link href="css/style.css" rel="stylesheet" />
</head>
<body>
<header>
  <nav>
    <div class="nav_logo">
      <h1><a href="home.php">Aid Share</a></h1>
    </div>
    <ul class="nav_link">
      <li><a href="home.php">Home</a></li>
      <li><a href="request_device.php">Borrow</a></li>
      <li><a href="return_device.php">Return</a></li>
    </ul>
  </nav>
</header>
<main>
  <section class="add_device_box">
    <?php $status = $_GET['status'] ?? ''; ?>

    <?php if($status === 'approved'): ?>
      <h1>Request Approved</h1>
      <p style="text-align:center;">
        Device <strong><?php echo htmlspecialchars($_GET['device'] ?? ''); ?></strong> has been assigned to you.<br />
        It's due back on <strong><?php echo htmlspecialchars($_GET['due'] ?? ''); ?></strong>.
      </p>

    <?php elseif($status === 'waitlisted'): ?>
      <h1>Added to Waitlist</h1>
      <p style="text-align:center;">
        Every matching device is currently on loan.<br />
        You're position <strong>#<?php echo htmlspecialchars($_GET['position'] ?? ''); ?></strong> in line for
        device <strong><?php echo htmlspecialchars($_GET['device'] ?? ''); ?></strong>,
        expected back around <strong><?php echo htmlspecialchars($_GET['expected'] ?? ''); ?></strong>.
      </p>

    <?php elseif($status === 'none'): ?>
      <h1>No Matching Devices</h1>
      <p style="text-align:center;">We don't have any devices of that type in the system yet.</p>

    <?php else: ?>
      <h1>Unknown Status</h1>
    <?php endif; ?>

    <p style="text-align:center; margin-top:20px;"><a href="home.php">Back to Home</a></p>
  </section>
</main>
</body>
</html>
