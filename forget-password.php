<?php require 'lib/db.php';
$currentPage = 'login.php' ?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (isset($_POST['submit'])) {

  require './lib/validator.class.php';
  $validation = new Validator($_POST);
  $validation->setFields([
    'email' => [
      'regex' => '/^\w{4,20}@\w{2,20}\.\w{2,20}$/',
      'message' => 'please enter a valid email (e.g., John_Doe@gmail.com)'
    ]
  ]);
  $errors = $validation->validateForm();

  if (!count($errors)) {
    $rows = $db->getData("SELECT * FROM users WHERE email = ?", [$_POST['email']]);
    if ($rows === 0) {
      $errors['email'] = 'This email has not been registered.';
    } else {
      $user = $rows[0];

      /* generate random password */
      $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $newPassword = '';

      for ($i = 0; $i < 10; $i++) {
        $randomCharacter = $characters[rand(0, strlen($characters) - 1)];
        $newPassword .= $randomCharacter;
      }

      $newPassword_hash = password_hash($newPassword, PASSWORD_DEFAULT);

      /* update password in database */
      $db->alterData("UPDATE users SET password = ? WHERE email = ?", [$newPassword_hash, $_POST['email']]);

      /* send email */
      require './lib/vendor/PHPMailer/Exception.php';
      require './lib/vendor/PHPMailer/PHPMailer.php';
      require './lib/vendor/PHPMailer/SMTP.php';

      $mail = new PHPMailer(true);

      try {
        //Server settings
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jadon.c1908i@gmail.com';
        $mail->Password   = 'jadon2020';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom('jadon_c1908i@gmail.com', 'Jadon');
        $mail->addAddress($user['email'], $user['username']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password recovery';
        $mail->Body    = "<h1>Here is your new password: $newPassword</h1>";
        $mail->AltBody = "Here is your new password: $newPassword (alternative)";

        $mail->send();
        echo 'Message has been sent';
      } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
      // REFERENCE: https://github.com/PHPMailer/PHPMailer

      $_SESSION['success'] = "Your new password is sent to $user[email]";
      header('Location: login.php');
      exit();
    }
  }
}

?>

<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/forget-password.css">
<title>Forget password</title>
<?php require 'components/navigation.php'; ?>

<main>
  <div class="container">
    <div class="row">
      <div class="col-xl-5 col-lg-6 col-md-8 col-11 rounded" id="formWrapper">
        <h1 class="my-5 text-center">Fogot password?</h1>
        <p class="text-center mb-5">Enter your email to recover your password</p>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
          <div class="form-group">
            <label for="email"></label>
            <i class="fas fa-envelope"></i>
            <input autocomplete="off" placeholder="Email" type="text" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
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

          <button class="btn btn-primary btn-block rounded-pill shadow-sm" name="submit">Send me recovery email.</button>

          <small class="text-center d-block">Go back to <a href="login.php"><strong class='text-white'>login</strong></a></small>
        </form>
      </div>
    </div>
  </div>

</main>

<?php require 'components/footer.php'; ?>
