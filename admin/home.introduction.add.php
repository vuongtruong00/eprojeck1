<?php include './../lib/db.php';
$currentPage = 'home.introduction.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php
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

  /* insert data into database */
  $db->alterData(
    "
      INSERT INTO 
        home_introduction
      SET 
        img_url = ?,
        title = ?,
        subtitle = ?,
        content = ?;
    ", 
    [
      $readUrl,
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['content'],
    ]
  );
  
  /* write image to server folder */
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting file to server.');
  };

} else { 
  /* insert data into database without image */
  $db->alterData(
    "
      INSERT INTO 
        home_introduction
      SET 
        title = ?,
        subtitle = ?,
        content = ?;
    ", 
    [
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['content'],
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
<title>Add Introduction</title>
<?php include './components/navigation.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-12 mx-auto mt-4">
      <a class="btn btn-primary my-2 px-2 px-4" href="home.introduction.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
      <h2 class="my-4">Add an introduction</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">

        <div class="row">
          <div class="col-lg-6 order-lg-0 order-1">
            <div class="form-group mt-3">
              <label for="title">title:</label>
              <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
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
              <textarea type="text" name="subtitle" id="subtitle" rows="10" class="form-control"><?php echo htmlspecialchars($_POST['subtitle'] ?? ''); ?></textarea>
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
              <div class="image-area mb-2"></div>

              <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm">
                <i class="fa fa-upload mr-2"></i>Choose an image
                <input id="upload" type="file" name="upload">
              </label>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label for="content">content:</label>
          <textarea type="text" name="content" id="content" class="form-control" ><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
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
