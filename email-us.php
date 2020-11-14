<?php require 'lib/db.php';
$currentPage = 'email-us.php' ?>

<?php

if (!isset($_POST['submit'])) goto end;

require 'lib/validator.class.php';

/* check all input by validator */
$validation = new ClientValidator($_POST);
$errors = $validation->validateForm();

if (count($errors)) goto end;

/* write to datbase */
$db->alterData("
  INSERT INTO 
    clients
  SET 
    fullname = ?,
    phone = ?,
    email = ?,
    event_location = ?,
    event_date = ?,
    service_id = ?
", [
  $_POST['fullname'],
  $_POST['phone'],
  $_POST['email'],
  $_POST['eventLocation'],
  $_POST['eventDate'],
  $_POST['service_id']
]);

header('Location: index.php');
exit();

end:

?>

<?php require 'components/header.php'; ?>
<script src='js/email-us.js' type="module" defer></script>
<script src='js/email-us.live-validate.js' type="module" defer></script>
<link rel="stylesheet" href="css/email-us.css">
<title>Email us</title>
<?php require 'components/navigation.php'; ?>

<main>
  <div class="container">
    <h1 class="py-5 display-4 text-center">Welcome!</h1>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
      <div class="row" id="formWrapper">
        <div class="col-sm-10 col-md-8 col-lg-5 mx-auto">

          <div class="form-group">
            <label for="fullname">Fullname</label>
            <input type="fullname" name="fullname" id="fullname" class="form-control live-validate" autocomplete="off" value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
          </div>
          <div id='fullname-error'>
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
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" autocomplete="off" class="form-control live-validate" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
          </div>
          <div id='phone-error'>
            <?php
            if (isset($errors['phone'])) {
              echo "
                    <div class='alert alert-danger' role='alert'>
                      <strong>$errors[phone]</strong>
                    </div>
                  ";
            }
            ?>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" class="form-control live-validate" autocomplete="off" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
          </div>
          <div id='email-error'>
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

        </div>

        <div class="col-sm-10 col-md-8 col-lg-5 mx-auto">

          <div class="form-group">
            <label for="eventLocation">Event location</label>
            <input type="text" name="eventLocation" id="eventLocation" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($_POST['eventLocation'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label for="eventDate">Event date</label>
            <input type="date" name="eventDate" id="eventDate" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars($_POST['eventDate'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label for="category_id">Interested category</label>
            <div class='d-flex'>
              <select name="category_id" id="category_id" class='custom-select'>
                <?php
                  $categories = $db->getData("SELECT * FROM service_categories;");
                  foreach ($categories as $cate) {
                    $selected = htmlspecialchars($_POST['category_id'] ?? '') == $cate['id'] ? 'selected' : '';
                    echo "
                      <option $selected value='$cate[id]'>$cate[name]</option>
                    ";
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="service_id">Interested service</label>
            <div class='d-flex'>
              <select name="service_id" id="service_id" class='custom-select'>
                
              </select>
              <div id='selectedService' class="d-none"><?php echo htmlspecialchars($_POST['service_id'] ?? ''); ?></div>
            </div>
          </div>

        </div>

      </div>
      <button class="btn btn-success shadow-sm px-5 my-5 mx-auto d-block" name="submit">Send</button>
    </form>

  </div>
</main>


<?php require 'components/footer.php'; ?>