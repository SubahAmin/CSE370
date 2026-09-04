<?php
require_once('auth.php');
if(isset($_SESSION['user_id'])){
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="About the site"/>
	<meta name="author" content="Author name"/>
	<title>AID SHARE</title>

<!--core CSS -->
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
      <h1>Sign In</h1>

       <?php if (isset($_GET['error'])): ?>
        <p style="color:red; margin-bottom:10px;">Incorrect password or username</p>
       <?php endif; ?>
       <?php if (isset($_GET['upgraded'])): ?>
        <p style="color:green; margin-bottom:10px;">Role added! Sign in to see it.</p>
       <?php endif; ?>

      <form class="login_form" action="login.php" method="post">
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Username"
        />
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Password"
        />
        <input type="submit" value="Submit" />
       </form>
      <p style="margin-top:10px;">New user? <a href="signup.php">Sign Up</a></p>
    </div>
  </section>
</main>
</body>
</html>