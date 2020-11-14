<?php 

if (!isset($_SESSION['jadon_loggedIn']) && isset($_COOKIE['jadon_loggedIn'])) {
  $_SESSION['jadon_loggedIn'] = unserialize($_COOKIE['jadon_loggedIn']);
} 

if (isset($_SESSION['jadon_loggedIn'])) {
  /* check if the account is still existing */
  $rows = $db->getData("SELECT * FROM managers WHERE id = ?;", [$_SESSION['jadon_loggedIn']['id']]);

  if ($rows === 0) {
    header('location: logout.php');
    exit();
  }

  /* check last activity time */
  $now = new DateTime(null, new DateTimeZone('Asia/Ho_Chi_Minh'));
  $db->alterData("UPDATE managers SET last_activity_time = ? WHERE id = ?;", [$now->format('Y-m-d H:i:s'), $_SESSION['jadon_loggedIn']['id']]);
} else {
  header('Location: cms.login.php');
  exit();
}

