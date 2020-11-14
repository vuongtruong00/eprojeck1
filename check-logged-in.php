<?php 

if (!isset($_SESSION['user_loggedIn']) && isset($_COOKIE['user_loggedIn'])) {
  $_SESSION['user_loggedIn'] = unserialize($_COOKIE['user_loggedIn']);
} 

if (isset($_SESSION['user_loggedIn'])) {
  $rows = $db->getData("SELECT * FROM users WHERE id = ?;", [$_SESSION['user_loggedIn']['id']]);

  if ($rows === 0) {
    header('location: logout.php');
    exit();
  }
}

?>

