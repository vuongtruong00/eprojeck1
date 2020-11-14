<?php include './../lib/db.php';
$currentPage = 'managers.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php
/* get current data of the manager */
$rows = $db->getData("SELECT * FROM managers WHERE id = ?;", [$_GET['id']]);

if ($rows === 0) {
  exit('This manager does not exist.');
}

$currentManager = $rows[0];

/* check if there was a post */
if (!isset($_POST['submit'])) goto end;

/* check if inputs have a wrong pattern */
require '../lib/validator.class.php';
$validation = new ManagerValidator($_POST);
$errors = $validation->validateForm();

if ($_POST['password'] === $currentManager['password']) {
  unset($errors['password']);
}

if (count($errors)) goto end;

/* check if username already exists */
$rows = $db->getData("SELECT * FROM managers WHERE username = ?;", [$_POST['username']]);

if ($rows !== 0 && $rows[0]['username'] !== $currentManager['username']) {
  $errors['username'] = 'This username has been taken.';
  goto end;
}

/* check if email already exists */
$rows = $db->getData("SELECT * FROM managers WHERE email = ?;", [$_POST['email']]);

if ($rows !== 0 && $rows[0]['email'] !== $currentManager['email']) {
  $errors['email'] = 'This email has been used.';
  goto end;
}

/* if the being edited account is super-admin then the level can not be changed */
$level = $currentManager['level'] === 'super-admin' ? 'super-admin' : $_POST['level'];

/* hash password */
$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

if ($_FILES['upload']['name']) {
  /* prepare image name if there is image uploaded */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/managers/', '../img/managers/'));

  /* update database */
  $db->alterData(
    "UPDATE
      managers
    SET 
      fullname = ?,
      username = ?,
      password = ?,
      email = ?,
      level = ?,
      img_url = ?
    WHERE 
      id = ?;",
    [
      $_POST['fullname'],
      $_POST['username'],
      $password_hash,
      $_POST['email'],
      $level,
      $readUrl,
      $currentManager['id']
    ]
  );

  /* update image to server folder */
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting new file to server.');
  }
  
  if ($currentManager['img_url']) {
    if (!unlink($currentManager['img_url'])) {
      exit('An error occur while delete old file form server.');
    } 
  } 

  if ($_SESSION['jadon_loggedIn']['id'] === $currentManager['id']) {
    $_SESSION['jadon_loggedIn']['img_url'] = $readUrl;
  }

} else {
  /* if there is no image chosen we will not update img_url and image in server folder */
  $db->alterData(
    "UPDATE
      managers
    SET 
      fullname = ?,
      username = ?,
      password = ?,
      email = ?,
      level = ?
    WHERE 
      id = ?;",
    [
      $_POST['fullname'],
      $_POST['username'],
      $password_hash,
      $_POST['email'],
      $level,
      $currentManager['id']
    ]
  );
}

header('Location: managers.php');
exit();

end:
?>

<?php include './components/header.php'; ?>

<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<title>Edit manager</title>

<?php include './components/navigation.php'; ?>

<div class="container">
  <a class="btn btn-primary mt-4 px-4" href="managers.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
  <div class="row">
    <div class="col-lg-5 col-md-7 col-9 mx-auto mt-4">
      <h2 class="my-4">Edit manager</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $currentManager['id'] ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label for="fullname">Fullname:</label>
          <input type="text" name="fullname" id="fullname" class="form-control" value="<?php echo htmlspecialchars($_POST['fullname'] ?? $currentManager['fullname']); ?>">
        </div>

        <?php
        if (isset($errors['fullname'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[fullname]</strong>
              </div>
            ";
        }
        ?>

        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? $currentManager['username']); ?>">
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
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" class="form-control" value="<?php echo htmlspecialchars($_POST['password'] ?? $currentManager['password']); ?>">
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

        <div class="form-group">
          <label for="email">Email:</label>
          <input type="text" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? $currentManager['email']); ?>">
        </div>

        <?php
        if (isset($errors['email'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[email]</strong>
              </div>
            ";
        }
        ?>

        <?php if ($currentManager['level'] !== 'super-admin') { ?>
          <div class="form-group">
            <label for="level">Level:</label>
            <select name="level" id="level" class="custom-select">
              <option <?php echo (isset($_POST['level']) && ($_POST['level'] === 'manager')) || $currentManager['level'] === 'manager' ? 'selected' : ''; ?> value="manager">Manager</option>
              <?php
              if ($_SESSION['jadon_loggedIn']['level'] === 'super-admin') {
                $selected = (isset($_POST['level']) && ($_POST['level'] === 'admin')) || $currentManager['level'] === 'admin' ? 'selected' : '';
                echo "<option $selected value='admin'>Admin</option>";
              };
              ?>
            </select>
          </div>
        <?php } ?>

        <div class="form-group my-4">
          <div class="image-area mb-2">

            <?php
            if (!empty($currentManager['img_url'])) {
              echo "<img class='img-fluid rounded shadow-sm mx-auto d-block' src='$currentManager[img_url]' alt='image'>";
            }
            ?>

          </div>

          <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm">
            <i class="fa fa-upload mr-2"></i>Choose an image
            <input id="upload" type="file" name="upload">
          </label>
        </div>

        <button class="btn btn-success btn-block mt-4 py-2 mb-5" name="submit">Save changes</button>
      </form>
    </div>
  </div>
</div>

<?php include './components/footer.php'; ?>