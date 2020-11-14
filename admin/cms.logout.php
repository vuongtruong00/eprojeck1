<?php include '../lib/db.php'; ?>
<?php 
unset($_SESSION['jadon_loggedIn']);
setcookie('jadon_loggedIn', '', time() - 86400, '/');
header('Location: index.php');
exit();