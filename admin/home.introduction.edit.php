<?php include './../lib/db.php';
$currentPage = 'home.introduction.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php
/* load current data */
$rows = $db->getData("SELECT * FROM home_introduction WHERE id = ?;", [$_GET['id']]);
if ($rows === 0) {
  exit('There is no such introduction id');
}

$currentIntroduction = $rows[0];

/* check if there was a submission */
if (!isset($_POST['submit'])) goto end;

/* check if inputs have an incorrect pattern */
require '../lib/validator.class.php';
$validation = new HomeIntroductionValidator($_POST);
$errors = $validation->validateForm();
if (count($errors)) goto end;

if ($_FILES['upload']['name']) {
  /* prepare image name if there is image uploaded */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/home-introduction/', '../img/home-introduction/'));

  /* update data into database */
  $db->alterData(
    "
      UPDATE
        home_introduction
      SET 
        img_url = ?,
        title = ?,
        subtitle = ?,
        content = ?
      WHERE
        id = ?;
    ", 
    [
      $readUrl,
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['content'],
      $currentIntroduction['id']
    ]
  );
  
  /* update image to server folder */
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting new file to server.');
  }
  
  if ($currentIntroduction['img_url']) {
    if (!unlink($currentIntroduction['img_url'])) {
      exit('An error occur while delete old file form server.');
    } 
  } 

} else { 
  /* update data into database without image */
  $db->alterData(
    "
      UPDATE 
        home_introduction
      SET 
        title = ?,
        subtitle = ?,
        content = ?
      WHERE
        id = ?;
    ", 
    [
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['content'],
      $currentIntroduction['id']
    ]
  );
}

header('Location: home.introduction.php');
exit();

end:
?>

<?php include './components/header.php'; ?>
<script src="../lib/vendor/tinymce/tinymce.min.js"></script>
<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<script src="./js/shared/tinymce.init.js" type="module" defer></script>
<title>Edit introduction</title>
<?php include './components/navigation.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-12 mx-auto mt-4">
      <a class="btn btn-primary my-2 px-2 px-4" href="home.introduction.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
      <h2 class="my-4">Edit introduction</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $currentIntroduction['id'] ?>" method="post" enctype="multipart/form-data">

        <div class="row">
          <div class="col-lg-6 order-lg-0 order-1">
            <div class="form-group mt-3">
              <label for="title">title:</label>
              <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? $currentIntroduction['title']); ?>">
            </div>

            <?php
            if (isset($errors['title'])) {
              echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[title]</strong>
              </div>
            ";
            }
            ?>

            <div class="form-group">
              <label for="subtitle">subtitle:</label>
              <textarea type="text" name="subtitle" id="subtitle" rows="10" class="form-control"><?php echo htmlspecialchars($_POST['subtitle'] ?? $currentIntroduction['subtitle']); ?></textarea>
            </div>

            <?php
            if (isset($errors['subtitle'])) {
              echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[subtitle]</strong>
              </div>
            ";
            }
            ?>
          </div>

          <div class="col-lg-6 order-lg-1 order-0">
            <div class="form-group mt-5">
              <div class="image-area mb-2">
              <?php
              if (!empty($currentIntroduction['img_url'])) {
                echo "<img class='img-fluid rounded shadow-sm mx-auto d-block' src='$currentIntroduction[img_url]' alt='image'>";
              }
              ?>
              </div>

              <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm">
                <i class="fa fa-upload mr-2"></i>Choose an image
                <input id="upload" type="file" name="upload">
              </label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="content">content:</label>
          <textarea type="text" name="content" id="content" class="form-control"></textarea>
          <div id='contenContainer' class='d-none'><?php echo htmlspecialchars($_POST['content'] ?? $currentIntroduction['content']); ?></div>
        </div>

        <?php
        if (isset($errors['content'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[content]</strong>
              </div>
            ";
        }
        ?>

        <button class="btn btn-success d-block py-2 px-5 ml-auto mb-5" name="submit">Save</button>
      </form>
    </div>
  </div>
</div>

<?php include './components/footer.php'; ?>
