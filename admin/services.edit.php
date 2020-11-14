<?php include './../lib/db.php';
$currentPage = 'services.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php
/* get current data */
$rows = $db->getData("SELECT * FROM services WHERE id = ?;", [$_GET['id']]);

if ($rows === 0) {
  exit('There is no such service id');
}

$currentService = $rows[0];

/* check if there was a submission */
if (!isset($_POST['submit'])) goto end;

/* check if inputs have an incorrect pattern */
require '../lib/validator.class.php';
$validation = new ServiceValidator($_POST);
$errors = $validation->validateForm();
if (count($errors)) goto end;

if ($_FILES['upload']['name']) {
  /* prepare image name if there is image uploaded */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/services/', '../img/services/'));

  /* insert data into database */
  $db->alterData(
    "
      UPDATE 
        services
      SET 
        category_id = ?,
        img_url = ?,
        title = ?,
        subtitle = ?,
        content = ?
      WHERE
        id = ?;
    ", 
    [
      $_POST['category_id'],
      $readUrl,
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['content'],
      $currentService['id']
    ]
  );
  
  /* update image to server folder */
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting new file to server.');
  } elseif (!unlink($currentService['img_url'])) {
    exit('An error occur while delete old file form server.');
  }

} else { 
  /* insert data into database without image */
  $db->alterData(
    "
      UPDATE 
        services
      SET 
        category_id = ?,
        title = ?,
        subtitle = ?,
        content = ?
      WHERE
        id = ?;
    ", 
    [
      $_POST['category_id'],
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['content'],
      $currentService['id']
    ]
  );
}

header('Location: services.php');
exit();

end:
?>

<?php include './components/header.php'; ?>
<script src="../lib/vendor/tinymce/tinymce.min.js"></script>
<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<script src="./js/shared/tinymce.init.js" type="module" defer></script>
<title>Edit Service</title>
<?php include './components/navigation.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-12 mx-auto mt-4">
      <a class="btn btn-primary my-2 px-2 px-4" href="services.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
      <h2 class="my-4">Edit service</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $currentService['id'] ?>" method="post" enctype="multipart/form-data">

        <div class="row">
          <div class="col-lg-6 order-lg-0 order-1">
            <div class="form-group mt-3">
              <label for="category_id">category:</label>
              <select name="category_id" id="category_id" class='custom-select'>
                <?php
                  $categories = $db->getData("SELECT * FROM service_categories;");
                  foreach ($categories as $cate) {
                    $selected = htmlspecialchars($_POST['category_id'] ?? $currentService['category_id']) == $cate['id'] ? 'selected' : '';
                    echo "
                      <option $selected value='$cate[id]'>$cate[name]</option>
                    ";
                  }
                ?>
              </select>
            </div>

            <div class="form-group mt-3">
              <label for="title">title:</label>
              <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? $currentService['title']); ?>">
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
              <textarea type="text" name="subtitle" id="subtitle" rows="10" class="form-control"><?php echo htmlspecialchars($_POST['subtitle'] ?? $currentService['subtitle']); ?></textarea>
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
                  echo "<img src='$currentService[img_url]' alt='image' class='img-fluid rounded shadow-sm mx-auto d-block' >";
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
          <textarea type="text" name="content" id="content" class="form-control" ></textarea>
          <div id='contenContainer' class='d-none'><?php echo htmlspecialchars($_POST['content'] ?? $currentService['content']); ?></div>
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
