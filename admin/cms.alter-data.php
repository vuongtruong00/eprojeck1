<?php
include '../lib/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  $error = $db->alterData($_POST['query'], json_decode($_POST['params']), true);
  
  if ($error) {
    echo json_encode(['result' => -1, 'error' => $error]);
  } else {
    echo json_encode(['result' => 1]);
  }

}

exit();