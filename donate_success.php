<?php
require_once('auth.php');
require_role('donor_flag');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Donation Complete - AID SHARE</title>
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
      <li><a href="donate.php">Donate</a></li>
    </ul>
  </nav>
</header>
<main>
  <section class="add_device_box">
    <h1>Donation Complete!</h1>
    <p style="text-align:center;">Thank you — your device has been added to the inventory.</p>
    <p style="text-align:center; margin-top:20px;"><a href="home.php">Back to Home</a></p>
  </section>
</main>
</body>
</html>
