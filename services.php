<?php require 'lib/db.php';
$currentPage = 'services.php' ?>
<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/services.css">
<title>Services</title>
<?php require 'components/navigation.php'; ?>

<div class="container outmost-container">
  <div class="row">
  <?php 
  if (isset($_GET['category_id'])) {
    $rows = $db->getData("SELECT * FROM services WHERE category_id = ? ORDER BY created_at DESC;", [$_GET['category_id']]);
  } else {
    $rows = $db->getData("SELECT * FROM services;");
  }


  if ($rows !== 0) {
    foreach ($rows as $row) {
      $imgUrl = substr($row['img_url'], 1);

      if (strlen($row['subtitle']) > 140) {
        $shortSubtitle = substr($row['subtitle'], 0, 137);
        $shortSubtitle = substr($shortSubtitle, 0, strrpos($shortSubtitle, ' ')) . '...';
      } else {
        $shortSubtitle = $row['subtitle'];
      }

      echo "
        <div class='col-sm-10 col-md-6 mx-auto mb-4'>
          <div class='card border-0 shadow-sm'>
            <img src='$imgUrl' class='card-img-top' alt='image'>
            <div class='card-body text-center'>
              <h2 class='card-title mb-3 text-capitalize'>$row[title]</h2>
              <div class='card-text text-black-50 long-content'>$shortSubtitle</div>
              <a class='text-primary stretched-link mt-3 d-block' href='service-detail.php?id=$row[id]'>See details</a>
            </div>
          </div>
        </div>
      ";
    }
  }
  ?>
  </div>
</div>


<?php require 'components/go-to-join.php'; ?>
<?php require 'components/footer.php'; ?>
