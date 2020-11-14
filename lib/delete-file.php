<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $success = [];
  $failure = [];

  if (isset($_POST['fileNames'])) {

    foreach (json_decode($_POST['fileNames']) as $fileName) {
      if (unlink($fileName)) {
        $success[] = $fileName;
      } else {
        $failure[] = $fileName;
      }
    }
    
  } else {
    
    if (unlink($_POST['fileName'])) {
      $success[] = $_POST['fileName'];
    } else {
      $failure[] = $_POST['fileName'];
    }
    
  }
  
  echo json_encode(['success' => $success, 'failure' => $failure]);
}

exit();