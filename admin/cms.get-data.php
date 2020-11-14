<?php
include '../lib/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_POST['query'])) {
    $rows = $db->getData($_POST['query'], json_decode($_POST['params']));
  } else {
    $rows = $db->getData("SELECT * FROM $_POST[table];");
  }
  
  if ($rows === 0) {
    echo json_encode('');
  } else {
    echo json_encode(['rows' => $rows, 'currentUser' => $_SESSION['jadon_loggedIn']]);
  }

}

exit();

