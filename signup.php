<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up - AID SHARE</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/animate.min.css" />
    <link href="css/style.css" rel="stylesheet" />
</head>
<body>
<header>
  <nav>
    <div class="nav_logo">
      <h1><a href="index.php">Aid Share</a></h1>
    </div>
    <ul class="nav_link">
      <li><a href="index.php">Home</a></li>
    </ul>
  </nav>
</header>
<main>
  <section class="login">
    <div class="login_box">
      <h1>Sign Up</h1>

      <?php
        $error = $_GET['error'] ?? '';
        $errorMessages = [
            'mismatch' => 'Passwords do not match.',
            'dup'      => 'That email is already registered under a different password.',
            'role'     => "You're already signed up with that role.",
            'general'  => 'Please fill in every field correctly.',
        ];
      ?>
      <?php if($error !== '' && isset($errorMessages[$error])): ?>
        <p style="color:red; margin-bottom:10px;"><?php echo htmlspecialchars($errorMessages[$error]); ?></p>
      <?php endif; ?>

      <form class="login_form" action="signup_process.php" method="post">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm_password" placeholder="Confirm Password" required />

        <select name="role" required>
          <option value="">-- Select Role --</option>
          <option value="Donor">Donor</option>
          <option value="Borrower">Borrower</option>
          <option value="Coordinator">Coordinator</option>
        </select>

        <p style="font-size:13px; opacity:0.75; margin:4px 0 12px;">
         Already have an account under a different role? Sign up again with the
         same email and password and pick the new role to add it to your account.
        </p>

        <input type="submit" value="Sign Up" />
      </form>

      <p style="text-align:center; margin-top:14px; font-size:.95rem;">
      Already have an account? <a href="index.php">Sign In</a>
  </section>
</main>
</body>
</html>