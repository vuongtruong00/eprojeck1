<?php include 'lib/db.php'; ?>
<?php 
unset($_SESSION['user_loggedIn']);
setcookie('user_loggedIn', '', time() - 86400, '/');
$url = isset($_GET['prev']) ? urldecode($_GET['prev']) : 'index.php';
header('Location: ' . $url);
exit();