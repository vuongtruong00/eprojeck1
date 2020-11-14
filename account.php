<?php include './lib/db.php';
$currentPage = 'login.php'; ?>
<?php include './check-logged-in.php'; ?>

<?php

if (!isset($_SESSION['user_loggedIn'])) { 
  header('location: index.php');
  exit();
}

if ($_GET['id'] != $_SESSION['user_loggedIn']['id']) {
  exit('<h1>You need to authenticate before accessing this page.</h1>');
}

/* get current data of the manager */
$rows = $db->getData("SELECT * FROM users WHERE id = ?;", [$_GET['id']]);

if ($rows === 0) {
  exit('This user does not exist.');
}

$currentUser = $rows[0];

/* check if there was a post */
if (!isset($_POST['submit'])) goto end;

/* check if inputs have a wrong pattern */
require './lib/validator.class.php';
$validation = new Validator($_POST);
$validation->setFields([
  'password' => [
    'regex' => '/^[A-Za-z0-9]{4,20}$/',
    'message' => 'password must be 4-20 characters and alphanumeric.'
  ],
  'email' => [
    'regex' => '/^\w{4,20}@\w{2,20}\.\w{2,20}$/',
    'message' => 'please enter a valid email (e.g., John_Doe@gmail.com)'
  ] 
]);
$errors = $validation->validateForm();

if ($_POST['password'] === $currentUser['password']) {
  unset($errors['password']);
}

if (count($errors)) goto end;

/* check if email already exists */
$rows = $db->getData("SELECT * FROM users WHERE email = ?;", [$_POST['email']]);

if ($rows !== 0 && $rows[0]['email'] !== $currentUser['email']) {
  $errors['email'] = 'This email has been used.';
  goto end;
}

/* hash password */
$password_hash = $_POST['password'] !== $currentUser['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $currentUser['password'];

if ($_FILES['upload']['name']) {
  /* prepare image name if there is image uploaded */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], './img/users/', './img/users/'));

  /* update database */
  $db->alterData(
    "UPDATE
      users
    SET 
      password = ?,
      email = ?,
      img_url = ?
    WHERE 
      id = ?;",
    [
      $password_hash,
      $_POST['email'],
      $readUrl,
      $currentUser['id']
    ]
  );

  /* update image in server folder */
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting new file to server.');
  }
  
  if ($currentUser['img_url']) {
    if (!unlink($currentUser['img_url'])) {
      exit('An error occur while delete old file form server.');
    } 
  } 

  if ($_SESSION['user_loggedIn']['id'] === $currentUser['id']) {
    $_SESSION['user_loggedIn']['img_url'] = $readUrl;
  }

} else {
  /* if there is no image chosen we will not update img_url and image in server folder */
  $db->alterData(
    "UPDATE
      users
    SET 
      password = ?,
      email = ?
    WHERE 
      id = ?;",
    [
      $password_hash,
      $_POST['email'],
      $currentUser['id']
    ]
  );
}

header('Location: index.php');
exit();

end:
?>

<?php include './components/header.php'; ?>

<script src="admin/js/shared/displayUploadImage.js" type="module" defer></script>
<link rel='stylesheet' href='css/account.css'>
<title>Manage account</title>

<?php include './components/navigation.php'; ?>

<main>
<div class="container">
  <div class="row">
    <div class="col-lg-5 col-md-7 col-9 mx-auto" id="formWrapper">
      <h2 class="my-4">Manage account</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $currentUser['id'] ?>" method="post" enctype="multipart/form-data">

        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" class="form-control" value="<?php echo htmlspecialchars($_POST['password'] ?? $currentUser['password']); ?>">
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
          <input type="text" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? $currentUser['email']); ?>">
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

        <div>
          <div class="image-area mb-2">

            <?php
            if (!empty($currentUser['img_url'])) {
              echo "<img class='img-fluid rounded shadow-sm mx-auto d-block' src='$currentUser[img_url]' alt='image'>";
            }
            ?>

          </div>

          <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm my-2">
            <i class="fa fa-upload mr-2"></i>Choose an image
            <input id="upload" type="file" name="upload">
          </label>
        </div>

        <button class="btn btn-success rounded-pill shadow btn-block mt-2 py-2 mb-1" name="submit">Save changes</button>
        <a class="btn btn-primary rounded-pill shadow btn-block mt-2 py-2 mb-5" href="users.php">Back</a>
      </form>
    </div>
  </div>
</div>
</main>
<?php include './components/footer.php'; ?>