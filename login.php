<?php require 'lib/db.php';
$currentPage = 'login.php' ?>

<?php

if (isset($_SESSION['user_loggedIn'])) { 
  header('location: index.php');
}

if (isset($_POST['submit'])) {

  require 'lib/validator.class.php';
  $validation = new LoginValidator($_POST);
  $errors = $validation->validateForm();

  if (!count($errors)) {
    $rows = $db->getData("SELECT * FROM users WHERE username = ?", [$_POST['username']]);
    if ($rows === 0) {
      $errors['username'] = 'Username does not exist.';
    } else {
      $user = $rows[0];
      if (!password_verify( $_POST['password'], $user['password'])) {
        $errors['password'] = 'Wrong password.';
      } else {
        $_SESSION['user_loggedIn'] = $user;

        if (isset($_POST['remember'])) {
          setcookie('user_loggedIn', serialize($user), time() + 86400, '/');
        }
        
        header('Location: ' . urldecode($_GET['prev']));
        exit();
      }
    }
  }
}

?>

<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/login.css">
<title>Login</title>
<?php require 'components/navigation.php'; ?>

<main>
  <div class="container">
    <div class="row">
      <div class="col-lg-5 col-md-7 col-9" id="formWrapper">
        <?php
        if (isset($_SESSION['success'])) {
          echo "
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
              <strong>$_SESSION[success]</strong>
              <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
              </button>
            </div>
          ";
  
          unset($_SESSION['success']);
        }
        ?>
  
        <h1 class="my-5 text-center display-4">Welcome!</h1>
  
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?prev='; echo $_GET['prev'] ?? 'index.php'; ?>" method="post">
          <div class="form-group">
            <label for="username"></label>
            <i class="fas fa-user"></i>
            <input autocomplete="off" placeholder="Username" type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
          </div>
  
          <?php
          if (isset($errors['username'])) {
            echo "
                <div class='alert alert-danger' role='alert'>
                  <strong>$errors[username]</strong>
                </div>
              ";
          }
          ?>
  
          <div class="form-group">
            <label for="password"></label>
            <i class="fas fa-lock"></i>
            <input placeholder="Password" type="password" name="password" id="password" class="form-control" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>">
          </div>
  
          <?php
          if (isset($errors['password'])) {
            echo "
                <div class='alert alert-danger' role='alert'>
                  <strong>$errors[password]</strong>
                </div>
              ";
          }
          ?>
  
          <div class="form-check d-flex justify-content-between">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" class="form-control" value="<?php echo htmlspecialchars($_POST['remember'] ?? ''); ?>">
            <label class="form-check-label" for="remember"><small>Remember me</small></label>
            <a href="forget-password.php" class='forget'><small>Forget password?</small></a>
          </div>
  
          <button class="btn btn-primary btn-block rounded-pill shadow" name="submit">Login</button>
        </form>
      </div>
    </div>
  </div>
</main>


<?php require 'components/footer.php'; ?>