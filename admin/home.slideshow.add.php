<?php include './../lib/db.php';
$currentPage = 'index.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php
/* check if there was a submission */
if (!isset($_POST['submit'])) goto end;

/* check if inputs have an incorrect pattern */
require '../lib/validator.class.php';
$validation = new HomeSlideshowValidator($_POST);
$errors = $validation->validateForm();

if ($_FILES['upload']['name']) {
  /* prepare image name if there is image uploaded */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/home-slideshow/', '../img/home-slideshow/'));
} else {
  $errors['upload'] = 'Please choose an image.';
}

if (count($errors)) {
  $errors['upload'] = 'Please choose an image.';
  goto end;
} 

/* prepare the image order */
$rows = $db->getData('SELECT * FROM home_slideshow ORDER BY img_order DESC LIMIT 1;');

if ($rows !== 0) {
  $imgOrder = $rows[0]['img_order'] + 1;
} else {
  $imgOrder = 1;
}

if ($_POST['order'] !== "auto") {

  if ($db->getData('SELECT * FROM home_slideshow WHERE img_order = ?;', [$_POST['order']]) !== 0) {
    $db->alterData("UPDATE home_slideshow SET img_order = ? WHERE img_order = ?", [$imgOrder, $_POST['order']]);
  }

  $imgOrder = $_POST['order'];
}

/* prepare query */
$query = "
  INSERT INTO 
    home_slideshow
  SET 
    img_url = ?,
    title = ?,
    caption = ?,
    img_order = ?
";

/*  write image url to datbase  */
$db->alterData($query, [$readUrl, $_POST['title'], $_POST['caption'], $imgOrder]);

/* write image to server folder */
if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
  exit('An error occur while writting file to server.');
};

header('Location: index.php');
exit();

end:
?>

<?php include './components/header.php'; ?>

<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<title>Add slideshow image</title>

<?php include './components/navigation.php'; ?>

<div class="container">
  <a class="btn btn-primary mt-4 px-4" href="index.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
  <div class="row">
    <div class="col-lg-5 col-md-7 col-9 mx-auto mt-4">
      <h2 class="my-4">Add image</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">

        <div class="form-group my-4">
          <div class="image-area mb-2"></div>

          <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm">
            <i class="fa fa-upload mr-2"></i>Choose an image
            <input id="upload" type="file" name="upload">
          </label>
        </div>

        <?php
        if (isset($errors['upload'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[upload]</strong>
              </div>
            ";
        }
        ?>

        <div class="form-group">
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
          <label for="caption">caption:</label>
          <textarea type="text" name="caption" id="caption" class="form-control"><?php echo htmlspecialchars($_POST['caption'] ?? ''); ?></textarea>
        </div>

        <?php
        if (isset($errors['caption'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[caption]</strong>
              </div>
            ";
        }
        ?>

        <div class="form-group">
          <label for="order">order:</label>
          <input type="text" name="order" id="order" class="form-control" value="<?php echo htmlspecialchars($_POST['order'] ?? 'auto'); ?>">
        </div>

        <?php
        if (isset($errors['order'])) {
          echo "
              <div class='alert alert-danger' role='alert'>
                <strong>$errors[order]</strong>
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