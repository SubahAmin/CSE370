<?php
// Central place for session handling + role checks.
// Every page that needs to know "who's logged in" should
// require_once('auth.php') at the very top, before any HTML output.

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Must be logged in at all, regardless of role.
function require_login(){
    if(!isset($_SESSION['user_id'])){
        header("Location: index.php");
        exit();
    }
}

// Must be logged in AND have the given role flag set to 'YES'
// (e.g. require_role('donor_flag'), require_role('coordinator_flag')).
function require_role($flagField){
    require_login();
    $value = $_SESSION[$flagField] ?? '';
    if(strtoupper(trim($value)) !== 'YES'){
        show_no_access();
    }
}

// Must be logged in AND must NOT have the given role flag set to 'YES'
// (e.g. require_not_role('donor_flag') to block donors from a page).
function require_not_role($flagField){
    require_login();
    $value = $_SESSION[$flagField] ?? '';
    if(strtoupper(trim($value)) === 'YES'){
        show_no_access();
    }
}

// Shared "you don't have permission" page. Used whenever a donor/borrower
// tries to open a page meant for another role (most importantly Coordinator).
function show_no_access(){
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>No Access - AID SHARE</title>
      <link rel="stylesheet" href="css/bootstrap.min.css" />
      <link rel="stylesheet" href="css/font-awesome.min.css" />
      <link rel="stylesheet" href="css/animate.min.css" />
      <link href="css/style.css?v=2" rel="stylesheet" />
    </head>
    <body>
    <header>
      <nav>
        <div class="nav_logo">
          <h1><a href="home.php">Aid Share</a></h1>
        </div>
        <ul class="nav_link">
          <li><a href="home.php">Home</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <section class="add_device_box">
        <h1>No Access</h1>
        <p style="text-align:center;">You don't have permission to view this page.</p>
        <p style="text-align:center; margin-top:20px;"><a href="home.php">Back to Home</a></p>
      </section>
    </main>
    </body>
    </html>
    <?php
    exit();
}
?>