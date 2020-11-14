<?php

require 'lib/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $error = $db->alterData("INSERT INTO event_likes SET event_id = ?, user_id = ?;", [$_POST['event_id'], $_POST['currentUserId']], true);

    if (!$error) {
      echo json_encode(['result' => 1]);
    } elseif ($error && strpos($error, 'Duplicate entry') !== false) {
      $db->alterData("DELETE FROM event_likes WHERE user_id = ?;", [$_POST['currentUserId']]);
      echo json_encode(['result' => -1]);
    } else {
      exit('An unkown error occurred: ' . $error);
    }

}