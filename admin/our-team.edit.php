<?php include './../lib/db.php';
$currentPage = 'our-team.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php
/* get current member */
$rows = $db->getData("SELECT * FROM team_members WHERE id = ?;", [$_GET['id']]);

if ($rows === 0) {
  exit('There is no such member id');
}

$currentMember = $rows[0];

/* check if there was a submission */
if (!isset($_POST['submit'])) goto end;

/* check if inputs have an incorrect pattern */
require '../lib/validator.class.php';
$validation = new TeamMemberValidator($_POST);
$errors = $validation->validateForm();
if (count($errors)) goto end;

/* check if there is no image selected */
if ($_FILES['upload']['name']) {
  /* prepare image name if there is image uploaded */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/our-team/', '../img/our-team/'));
  
  /* insert data into database */
  $db->alterData(
    "
      UPDATE 
        team_members
      SET 
        fullname = ?,
        img_url = ?,
        role = ?,
        description = ?,
        facebook = ?,
        twitter = ?,
        linkedin = ?
      WHERE
        id = ?;
    ",
    [
      $_POST['fullname'],
      $readUrl,
      $_POST['role'],
      $_POST['description'],
      $_POST['facebook'],
      $_POST['twitter'],
      $_POST['linkedin'],
      $currentMember['id']
    ]
  );
  
  /* update image to server folder */
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting new file to server.');
  } elseif (!unlink($currentMember['img_url'])) {
    exit('An error occur while delete old file form server.');
  }

} else {
  /* update data into database without image*/
  $db->alterData(
    "
      UPDATE 
        team_members
      SET
        fullname = ?,
        role = ?,
        description = ?,
        facebook = ?,
        twitter = ?,
        linkedin = ?
      WHERE
        id = ?;
    ",
    [
      $_POST['fullname'],
      $_POST['role'],
      $_POST['description'],
      $_POST['facebook'],
      $_POST['twitter'],
      $_POST['linkedin'],
      $currentMember['id'],
    ]
  );
}

header('Location: our-team.php');
exit();

end:
?>

<?php include './components/header.php'; ?>
<script src="../lib/vendor/tinymce/tinymce.min.js"></script>
<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<title>Edit team member</title>
<?php include './components/navigation.php'; ?>

<div class="container">
  <a class="btn btn-primary mt-4 px-2 px-4" href="our-team.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
  <div class="row">
    <div class="col-11 col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto">
      <h2 class="my-4">Edit team member</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $currentMember['id'] ?>" method="post" enctype="multipart/form-data">
        <div class="form-group my-4">
          <div class="image-area mb-2">
            <?php 
            echo "
              <img src='$currentMember[img_url]' alt='image' class='img-fluid rounded shadow-sm mx-auto d-block' >
            ";
            ?>
          </div>

          <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm">
            <i class="fa fa-upload mr-2"></i>Choose an image
            <input id="upload" type="file" name="upload">
          </label>
        </div>

        <div class="form-group">
          <label for="fullname">fullname:</label>
          <input type="text" name="fullname" id="fullname" class="form-control" value="<?php echo htmlspecialchars($_POST['fullname'] ?? $currentMember['fullname']); ?>">
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
          <label for="role">role:</label>
          <input type="text" name="role" id="role" class="form-control" value="<?php echo htmlspecialchars($_POST['role'] ?? $currentMember['role']); ?>">
        </div>
        <?php
        if (isset($errors['role'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[role]</strong>
              </div>
            ";
        }
        ?>

        <div class="form-group">
          <label for="description">description:</label>
          <textarea type="text" name="description" id="description" class="form-control" rows="4" ><?php echo htmlspecialchars($_POST['description'] ?? $currentMember['description']); ?></textarea>
        </div>
        <?php
        if (isset($errors['description'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[description]</strong>
              </div>
            ";
        }
        ?>

        <div class="form-group">
          <label for="facebook">facebook:</label>
          <input type="text" name="facebook" id="facebook" class="form-control" value="<?php echo htmlspecialchars($_POST['facebook'] ?? $currentMember['facebook']); ?>">
        </div>
        <?php
        if (isset($errors['facebook'])) {
          echo "
              <div class='alert alert-danger' facebook='alert'>
                <strong>$errors[facebook]</strong>
              </div>
            ";
        }
        ?>

        <div class="form-group">
          <label for="twitter">twitter:</label>
          <input type="text" name="twitter" id="twitter" class="form-control" value="<?php echo htmlspecialchars($_POST['twitter'] ?? $currentMember['twitter']); ?>">
        </div>
        <?php
        if (isset($errors['twitter'])) {
          echo "
              <div class='alert alert-danger' twitter='alert'>
                <strong>$errors[twitter]</strong>
              </div>
            ";
        }
        ?>

        <div class="form-group">
          <label for="linkedin">linkedin:</label>
          <input type="text" name="linkedin" id="linkedin" class="form-control" value="<?php echo htmlspecialchars($_POST['linkedin'] ?? $currentMember['linkedin']); ?>">
        </div>
        <?php
        if (isset($errors['linkedin'])) {
          echo "
              <div class='alert alert-danger' linkedin='alert'>
                <strong>$errors[linkedin]</strong>
              </div>
            ";
        }
        ?>


        <button class="btn btn-success btn-block mt-4 py-2 mb-5" name="submit">Save</button>
      </form>
    </div>
  </div>
</div>

<?php include './components/footer.php'; ?>