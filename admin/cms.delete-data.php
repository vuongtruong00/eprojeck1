<?php 
include '../lib/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $field = $_POST['field'] ?? 'id';
  $value = $_POST['value'] ?? $_POST['id'];
  $db->alterData("DELETE FROM $_POST[table] WHERE $field = ?;", [$value]);
  echo json_encode(['success' => true]);
}

exit();