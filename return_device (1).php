<?php
require_once('auth.php');
require_role('borrower_flag'); 
require_once('DBconnect.php');

$borrowerId = $_SESSION["email"] ?? $_SESSION["user_id"];
$borrowerIdEsc = mysqli_real_escape_string($conn, $borrowerId);

// only loans tied to a request the logged-in borrower actually made
$sql = "SELECT l.LOAN_ID, l.DEVICE_ID, dn.DEVICE_TYPE, l.DUE_DATE
        FROM loan l
        JOIN device d ON l.DEVICE_ID = d.DEVICE_ID
        JOIN donations dn ON d.DONATION_ID = dn.DONATION_ID
        JOIN requests r ON l.REQUEST_ID = r.REQUEST_ID
        WHERE l.ACTUAL_RETURN_DATE = '0000-00-00' AND r.BORROWER_ID = '$borrowerIdEsc'
        ORDER BY l.DUE_DATE";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Log a Return - AID SHARE</title>
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
    <h1>Log a Return</h1>

    <?php if(isset($_GET['error'])): ?>
      <p style="color:red; margin-bottom:10px;">Please fill in every field.</p>
    <?php endif; ?>

    <?php if(mysqli_num_rows($result) === 0): ?>
      <p style="text-align:center;">You don't have any devices currently on loan.</p>
    <?php else: ?>
    <form class="add_device_form" action="return_process.php" method="post">
      <select name="loan_id" required style="width:100%; padding:15px; margin-bottom:20px; border:1px solid #ccc; border-radius:4px; font-size:16px;">
        <option value="">-- Select Your Device --</option>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <option value="<?php echo htmlspecialchars($row['LOAN_ID']); ?>">
            <?php echo htmlspecialchars($row['DEVICE_ID'] . ' - ' . $row['DEVICE_TYPE'] . ' (due ' . $row['DUE_DATE'] . ')'); ?>
          </option>
        <?php endwhile; ?>
      </select>

      <input type="text" name="condition_note" placeholder="Condition on return (e.g. Slightly Damaged)" required />

      <select name="cleared_for_lending" required style="width:100%; padding:15px; margin-bottom:20px; border:1px solid #ccc; border-radius:4px; font-size:16px;">
        <option value="">-- Cleared for lending again? --</option>
        <option value="Yes">Yes - ready to lend again</option>
        <option value="No">No - needs maintenance first</option>
      </select>

      <input type="submit" value="Log Return" />
    </form>
    <?php endif; ?>
  </section>
</main>
</body>
</html>