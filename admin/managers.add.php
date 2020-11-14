<?php include './../lib/db.php'; $currentPage = 'managers.php';?>
<?php include './cms.check-logged-in.php'; ?>

<?php

if (!isset($_POST['submit'])) goto end;

require '../lib/validator.class.php';

/* check all input by validator */
$validation = new ManagerValidator($_POST);
$errors = $validation->validateForm();

if (count($errors)) goto end;

/* check if username already exists */
$rows = $db->getData("SELECT * FROM managers WHERE username = ?;", [$_POST['username']]);

if ($rows !== 0) {
  $errors['username'] = 'This username has been taken.';
  goto end;
}

/* check if email already exists */
$rows = $db->getData("SELECT * FROM managers WHERE email = ?;", [$_POST['email']]);

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
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/managers/', '../img/managers/'));
} else {
  $saveUrl = '';
  $readUrl = '';
}

/* hash password */
$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

/* write to datbase */
$db->alterData("
  INSERT INTO 
    managers
  SET 
    fullname = ?,
    username = ?,
    password = ?,
    email = ?,
    level = ?,
    img_url = ?;
", [
  $_POST['fullname'],
  $_POST['username'],
  $password_hash,
  $_POST['email'],
  $_POST['level'],
  $readUrl
]);

/* write image to server folder */
if ($saveUrl && !move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
  exit('An error occur while writting file to server.');
};

header('Location: managers.php');
exit();

end:
?>

<?php include './components/header.php'; ?>

<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<script src="./js/managers.add.live-validate.js" type="module" defer></script>
<title>Add manager</title>

<?php include './components/navigation.php'; ?>

  <div class="container">
    <a class="btn btn-primary mt-4 px-4" href="managers.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
    <div class="row">
      <div class="col-lg-5 col-md-7 col-9 mx-auto mt-4">
        <h2 class="my-4">Add manager</h2>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="fullname">Fullname:</label>
            <input type="text" name="fullname" id="fullname" class="form-control live-validate" value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
          </div>
          <div id = 'fullname-error'>
            <?php
            if (isset($errors['fullname'])) {
              echo "
                <div class='alert alert-danger' role='alert'>
                  <strong>$errors[fullname]</strong>
                </div>
              ";
            }
            ?>
          </div>

          <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control live-validate" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
          </div>
          <div id = 'username-error'>
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
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control live-validate" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>">
          </div>
          <div id = 'password-error'>
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
            <label for="confirmPassword">Confirm password:</label>
            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" value="<?php echo htmlspecialchars($_POST['confirmPassword'] ?? ''); ?>">
          </div>
          <div id = 'confirmPassword-error'>
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
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" class="form-control live-validate" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>
          <div id = 'email-error'>
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

          <div class="form-group">
            <label for="level">Level:</label>
            <select name="level" id="level" class="custom-select">
              <option <?php echo isset($_POST['level']) && ($_POST['level'] === 'manager') ? 'selected' : ''; ?> value="manager">Manager</option>
              <?php
              if ($_SESSION['jadon_loggedIn']['level'] === 'super-admin') {
                $selected = isset($_POST['level']) && ($_POST['level'] === 'admin') ? 'selected' : '';
                echo "<option $selected value='admin'>Admin</option>";
              };
              ?>
            </select>
          </div>

          <div class="form-group my-4">
            <div class="image-area mb-2"></div>

            <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm">
              <i class="fa fa-upload mr-2"></i>Choose an image
              <input id="upload" type="file" name="upload">
            </label>
          </div>

          <button class="btn btn-success btn-block mt-4 py-2 mb-5" name="submit">Add</button>
        </form>
      </div>
    </div>
  </div>

<?php include './components/footer.php'; ?>
