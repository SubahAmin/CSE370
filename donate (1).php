<?php
require_once('auth.php');
require_role('donor_flag');   // only donors get past this line
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Donate a Device - AID SHARE</title>
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
    <h1>Donate a Device</h1>

    <?php if(isset($_GET['error'])): ?>
      <p style="color:red; margin-bottom:10px;">Please fill in every field.</p>
    <?php endif; ?>

    <form class="add_device_form" action="donate_process.php" method="post">
      <input type="text" name="device_type" placeholder="Device Type (e.g. Wheelchair, Crutch)" required />

      <input type="text" name="description_text" placeholder="Description (e.g. Travel Wheelchair)" />

      <input type="text" name="device_condition" placeholder="Condition (e.g. Perfect, Slightly Damaged)" required />

      <input type="submit" value="Donate Device" />
    </form>
  </section>
</main>
</body>
</html>
