<?php
include 'lib/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_POST['getCurrentUser']) && $_POST['getCurrentUser']) {
    echo json_encode(['currentUser' => $_SESSION['user_loggedIn'] ?? null]);
    exit();
  } elseif (isset($_POST['query'])) {
    $rows = $db->getData($_POST['query'], json_decode($_POST['params']));
  } else {
    $rows = $db->getData("SELECT * FROM $_POST[table];");
  }

  if ($rows === 0) {
    echo json_encode(['rows' => [], 'currentUser' => $_SESSION['user_loggedIn'] ?? null]);
  } else {
    echo json_encode(['rows' => $rows, 'currentUser' => $_SESSION['user_loggedIn'] ?? null]);
  }

}

exit();

