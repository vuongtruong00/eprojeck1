<?php include './../lib/db.php';
$currentPage = 'our-work.php'; ?>
<?php include './cms.check-logged-in.php'; ?>

<?php
/* get current event */
$rows = $db->getData("SELECT * FROM events WHERE id = ?;", [$_GET['id']]);

if ($rows === 0) {
  exit('There is no such an event id');
}

$currentEvent = $rows[0];

/* check if there was a submission */
if (!isset($_POST['submit'])) goto end;

/* check if inputs have an incorrect pattern */
require '../lib/validator.class.php';
$validation = new EventValidator($_POST);
$errors = $validation->validateForm();
if (count($errors)) {
  goto end;
}

/* check if there is no image selected */
if ($_FILES['upload']['name']) {
  /* prepare image name if there is image uploaded */
  ['saveUrl' => $saveUrl, 'readUrl' => $readUrl] = (prepareFileUrl($_FILES['upload']['name'], '../img/our-work/thumbnails/', '../img/our-work/thumbnails/'));

  /* update data into database */
  $db->alterData(
    "
      UPDATE 
        events
      SET 
        img_url = ?,
        title = ?,
        subtitle = ?,
        description = ?,
        event_date = ?
      WHERE
        id = ?;
    ",
    [
      $readUrl,
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['description'],
      $_POST['event_date'],
      $currentEvent['id']
    ]
  );

  /* update image to server folder */
  if (!move_uploaded_file($_FILES['upload']['tmp_name'], $saveUrl)) {
    exit('An error occur while writting new file to server.');
  } elseif (!unlink($currentEvent['img_url'])) {
    exit('An error occur while delete old file form server.');
  }

} else {
  /* update data into database without image */
  $db->alterData(
    "
      UPDATE 
        events
      SET 
        category_id = ?,
        title = ?,
        subtitle = ?,
        description = ?,
        event_date = ?
      WHERE
        id = ?;
    ",
    [
      $_POST['category_id'],
      $_POST['title'],
      $_POST['subtitle'],
      $_POST['description'],
      $_POST['event_date'],
      $currentEvent['id']
    ]
  );
}


header('Location: our-work.php');
exit();

end:
?>

<?php include './components/header.php'; ?>
<script src="../lib/vendor/tinymce/tinymce.min.js"></script>
<script src="./js/shared/displayUploadImage.js" type="module" defer></script>
<script src="./js/shared/tinymce.init.js" type="module" defer></script>
<title>Edit event</title>
<?php include './components/navigation.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-12 mx-auto mt-4">
      <a class="btn btn-primary my-2 px-2 px-4" href="our-work.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
      <h2 class="my-4">Edit event</h2>

      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $currentEvent['id'] ?>" method="post" enctype="multipart/form-data">

        <div class="row">
          <div class="col-lg-6 order-lg-0 order-1">

            <div class="form-group mt-3">
              <label for="category_id">category:</label>
              <select name="category_id" id="category_id" class='custom-select'>
                <?php
                  $categories = $db->getData("SELECT * FROM service_categories;");
                  foreach ($categories as $cate) {
                    $selected = htmlspecialchars($_POST['category_id'] ?? $currentEvent['category_id']) == $cate['id'] ? 'selected' : '';
                    echo "
                      <option $selected value='$cate[id]'>$cate[name]</option>
                    ";
                  }
                ?>
              </select>
            </div>

            <div class="form-group mt-3">
              <label for="event_date">event_date:</label>
              <input type="date" name="event_date" id="event_date" class="form-control" value="<?php echo htmlspecialchars($_POST['event_date'] ?? $currentEvent['event_date']); ?>">
            </div>

            <?php
            if (isset($errors['event_date'])) {
              echo "
                <div class='alert alert-danger' role='alert'>
                  <strong>$errors[event_date]</strong>
                </div>
              ";
            }
            ?>

            <div class="form-group mt-3">
              <label for="title">title:</label>
              <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? $currentEvent['title']); ?>">
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
              <textarea type="text" name="subtitle" id="subtitle" rows="10" class="form-control"><?php echo htmlspecialchars($_POST['subtitle'] ?? $currentEvent['subtitle']); ?></textarea>
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
                  echo "<img src='$currentEvent[img_url]' alt='image' class='img-fluid rounded shadow-sm mx-auto d-block' >";
                ?>
              </div>

              <label for="upload" class="file-upload btn btn-secondary btn-block rounded-pill border-0 py-2 shadow-sm">
                <i class="fa fa-upload mr-2"></i>Choose an thumbnail
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
          </div>
        </div>


        <div class="form-group">
          <label for="content">description:</label>
          <textarea type="text" name="description" id="content" class="form-control"></textarea>
          <div id='contenContainer' class='d-none'><?php echo htmlspecialchars($_POST['description'] ?? $currentEvent['description']); ?></div>
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


        <button class="btn btn-success d-block py-2 px-5 ml-auto mb-5" name="submit">Save</button>
      </form>
    </div>
  </div>
</div>

<?php include './components/footer.php'; ?>