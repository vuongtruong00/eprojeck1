<?php
include '../lib/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $result = uploadFiles($_FILES['upload'], $_POST['savePath'], $_POST['readPath'], $_POST['query'], json_decode($_POST['params']));
  echo json_encode(['result' => $result]);
}

exit();

