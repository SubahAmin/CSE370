<?php
require_once('auth.php');
require_login();
require_not_role('donor_flag');   // donors cannot see the inventory
require_not_role('borrower_flag'); // borrowers cannot see the inventory
require_once('DBconnect.php');


$sql = "SELECT d.DEVICE_ID, dn.DEVICE_TYPE, dn.DESCRIPTION_TEXT, d.DEVICE_CONDITION, d.STATUS
        FROM device d
        JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
        ORDER BY dn.DEVICE_TYPE, d.DEVICE_ID";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Device Inventory - AID SHARE</title>
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
      <li><a href="show_devices.php">Devices</a></li>
    </ul>
  </nav>
</header>
<main>
  <section class="devices">
    <h1>Device Inventory</h1>
    <table class="device_table">
      <tr>
        <th>Device ID</th>
        <th>Type</th>
        <th>Description</th>
        <th>Condition</th>
        <th>Status</th>
      </tr>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['DEVICE_ID']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_TYPE']); ?></td>
        <td><?php echo htmlspecialchars($row['DESCRIPTION_TEXT']); ?></td>
        <td><?php echo htmlspecialchars($row['DEVICE_CONDITION']); ?></td>
        <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </section>
</main>
</body>
</html>