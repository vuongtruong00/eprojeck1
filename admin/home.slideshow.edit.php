<?php include './../lib/db.php';
$currentPage = 'index.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php

$rows = $db->getData("SELECT * FROM home_slideshow WHERE id = ?;", [$_GET['id']]);

if ($rows === 0) {
  exit('This image does not exist.');
}

$currentImage = $rows[0];

if (!isset($_POST['submit'])) goto end;

require '../lib/validator.class.php';

/* check if inputs have a correct pattern */
$validation = new HomeSlideshowValidator($_POST);
$errors = $validation->validateForm();
if (count($errors)) goto end;

if ($_FILES['upload']['name']) {
  /* prepare image name */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/home-slideshow/', '../img/home-slideshow/'));

  /* prepare query and params if there is image uploaded */
  $params = [$readUrl, $_POST['title'], $_POST['caption']];
  $query = "
    UPDATE 
      home_slideshow
    SET 
      img_url = ?,
      title = ?,
      caption = ?,
      img_order = ?
    WHERE
      id = '$currentImage[id]';
  ";

} else {
  /* prepare query and params if there is NO image uploaded */
  $params = [$_POST['title'], $_POST['caption']];
  $query = "
    UPDATE 
      home_slideshow
    SET 
      title = ?,
      caption = ?,
      img_order = ?
    WHERE
      id = '$currentImage[id]';
  ";
}

/* prepare the image order */
$imgOrder = $currentImage['img_order'];

if ($_POST['order'] !== "auto" && $_POST['order'] !== $currentImage['img_order']) {

  if ($db->getData('SELECT * FROM home_slideshow WHERE img_order = ?;', [$_POST['order']]) !== 0) {
    $db->alterData("UPDATE home_slideshow SET img_order = ? WHERE img_order = ?", [$currentImage['img_order'], $_POST['order']]);
  }

  $imgOrder = $_POST['order'];
}

$params[] = $imgOrder;

/* update database */
$db->alterData($query, $params);

/* update image to server folder */
if ($saveUrl) {
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting new file to server.');
  } elseif (!unlink($currentImage['img_url'])) {
    exit('An error occur while delete old file form server.');
  }
}

header('Location: index.php');
exit();

end:
?>

<?php include './components/header.php'; ?>

<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<title>Edit slideshow image</title>

<?php include './components/navigation.php'; ?>

<div class="container">
  <a class="btn btn-primary mt-4 px-4" href="index.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
  <div class="row">
    <div class="col-lg-5 col-md-7 col-9 mx-auto mt-4">
      <h2 class="my-4">Edit image</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $currentImage['id'] ?>" method="post" enctype="multipart/form-data">

        <div class="form-group my-4">
          <div class="image-area mb-2">
            <?php
            if (!empty($currentImage['img_url'])) {
              echo "<img class='img-fluid rounded shadow-sm mx-auto d-block' src='$currentImage[img_url]' alt='image'>";
            }
            ?>
          </div>

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
          <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? $currentImage['title']); ?>">
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
          <textarea type="text" name="caption" id="caption" class="form-control"><?php echo htmlspecialchars($_POST['caption'] ?? $currentImage['caption']); ?></textarea>
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
          <input type="text" name="order" id="order" class="form-control" value="<?php echo htmlspecialchars($_POST['order'] ?? $currentImage['img_order']); ?>">
          <small>Note: auto is equivalent to stay unchanged</small>
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