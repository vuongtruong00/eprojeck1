<?php require 'lib/db.php';
$currentPage = 'login.php' ?>

<?php

if (!isset($_POST['submit'])) goto end;

require 'lib/validator.class.php';

/* check all input by validator */
$validation = new UserValidator($_POST);
$errors = $validation->validateForm();

if (count($errors)) goto end;

/* check if username already exists */
$rows = $db->getData("SELECT * FROM users WHERE username = ?;", [$_POST['username']]);

if ($rows !== 0) {
  $errors['username'] = 'This username has been taken.';
  goto end;
}

/* check if email already exists */
$rows = $db->getData("SELECT * FROM users WHERE email = ?;", [$_POST['email']]);

if ($rows !== 0) {
  $errors['email'] = 'This email has been used.';
  goto end;
}

/* check confirm password */
if ($_POST['password'] !== $_POST['confirmPassword']) {
  $errors['confirmPassword'] = 'Confirm password does not match.';
  goto end;
}

/* prepare image name */
if ($_FILES['upload']['name']) {
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], './img/users/', './img/users/'));
} else {
  $saveUrl = '';
  $readUrl = '';
}

/* hash password */
$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

/* write to datbase */
$db->alterData("
  INSERT INTO 
    users
  SET 
    username = ?,
    password = ?,
    email = ?,
    img_url = ?;
", [
  $_POST['username'],
  $password_hash,
  $_POST['email'],
  $readUrl
]);

/* write image to server folder */
if ($saveUrl && !move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
  exit('An error occur while writting file to server.');
};

$_SESSION['success'] = 'Register successfully!';
header('Location: login.php');
exit();

end:

?>

<?php require 'components/header.php'; ?>
<script src='admin/js/shared/displayUploadImage.js' type="module" defer></script>
<script src='js/register.live-validate.js' type="module" defer></script>
<link rel="stylesheet" href="css/register.css">
<title>Register</title>
<?php require 'components/navigation.php'; ?>

<main>
  <div class="container">
    <h1 class="py-5 display-4 text-center">Welcome!</h1>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
      <div class="row" id="formWrapper">
        <div class="col-sm-10 col-md-8 col-lg-5 mx-auto">
          <div class="form-group">
            <label for="username"></label>
            <input type="text" name="username" id="username" placeholder="Username" autocomplete="off" class="form-control live-validate" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
          </div>
          <div id='username-error'>
            <?php
            if (isset($errors['username'])) {
              echo "
                    <div class='alert alert-danger' role='alert'>
                      <strong>$errors[username]</strong>
                    </div>
                  ";
            }
            ?>
          </div>

          <div class="form-group">
            <label for="password"></label>
            <input type="password" name="password" id="password" class="form-control live-validate" placeholder="Password" autocomplete="off" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>">
          </div>
          <div id='password-error'>
            <?php
            if (isset($errors['password'])) {
              echo "
                    <div class='alert alert-danger' role='alert'>
                      <strong>$errors[password]</strong>
                    </div>
                  ";
            }
            ?>
          </div>

          <div class="form-group">
            <label for="confirmPassword"></label>
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm password" autocomplete="off" class="form-control" value="<?php echo htmlspecialchars($_POST['confirmPassword'] ?? ''); ?>">
          </div>
          <div id='confirmPassword-error'>
            <?php
            if (isset($errors['confirmPassword'])) {
              echo "
                    <div class='alert alert-danger' role='alert'>
                      <strong>$errors[confirmPassword]</strong>
                    </div>
                  ";
            }
            ?>
          </div>

          <div class="form-group">
            <label for="email"></label>
            <input type="text" name="email" id="email" class="form-control live-validate" placeholder="Email" autocomplete="off" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>
          <div id='email-error'>
            <?php
            if (isset($errors['email'])) {
              echo "
                    <div class='alert alert-danger' role='alert'>
                      <strong>$errors[email]</strong>
                    </div>
                  ";
            }
            ?>
          </div>


          <button class="btn btn-success btn-block shadow-sm" name="submit">Add</button>
        </div>

        <div class="col-sm-10 col-md-8 col-lg-5 mx-auto">
          <div class="my-4">
            <div class="image-area mb-2"></div>
  
            <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 shadow-sm my-4">
              <i class="fa fa-upload mr-2 d-inline"></i>Choose an image
              <input id="upload" type="file" name="upload">
            </label>
          </div>
        </div>
      </div>
    </form>

  </div>
</main>


<?php require 'components/footer.php'; ?>