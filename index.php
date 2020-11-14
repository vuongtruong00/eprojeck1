<?php require 'lib/db.php';
$currentPage = 'index.php' ?>
<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/index.css">
<title>Home</title>
<?php require 'components/navigation.php'; ?>

<header>
  <?php
  $images = $db->getData("SELECT * FROM home_slideshow ORDER BY img_order;");
  ?>
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="5000">
    <ol class="carousel-indicators">
      <?php
      if ($images !== 0) {
        foreach ($images as $i => $img) {
          $imgUrl = substr($img['img_url'], 1);
          $active = $i === 0 ? 'active' : '';
          echo "
            <li data-target='#carouselExampleIndicators' data-slide-to='$i' class='$active'></li>
          ";
        }
      }
      ?>
    </ol>
    <div class="carousel-inner" role="listbox">
      <?php
      if ($images !== 0) {
        foreach ($images as $i => $img) {
          $imgUrl = substr($img['img_url'], 1);
          $active = $i === 0 ? 'active' : '';
          echo "
            <div class='carousel-item $active' style='background-image: linear-gradient(rgba(0, 0, 0, .1), 
            rgba(0, 0, 0, .5)), url($imgUrl)'>
              <div class='carousel-caption d-none d-md-block'>
                <h2 class='display-4 text-capitalize'>$img[title]</h2>
                <p class='lead'>$img[caption]</p>
              </div>
            </div>
          ";
        }
      }
      ?>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
</header>

<main>
<!-- Page Content -->
<?php
$rows = $db->getData("SELECT * FROM home_introduction;");

if ($rows !== 0) {
  foreach ($rows as $i => $row) {
    $order = $i % 2 === 0 ? '1' : '0';

    if ($row['img_url'] !== null) {
      $imgUrl = substr($row['img_url'], 1);
      $content = "
        <div class='col-lg-6 order-$order'>
          <img src='$imgUrl' alt='Image' class='img-fluid intro-img'>
        </div>
        <div class='col-lg-6'>$row[content]</div>
      ";
    } else {
      $content = "<div class='col'>$row[content]</div>";
    }

    echo "
      <section class='py-5'>
        <div class='container'>
          <h2 class='text-center text-capitalize'>$row[title]</h2>
          <h4 class='px-5 py-4 font-weight-normal text-center mb-4'>$row[subtitle]</h4>
          <div class='row'>
            $content
          </div>
        </div>
      </section>
    ";
  }
}
?>
</main>

<?php require 'components/go-to-join.php'; ?>
<?php require 'components/footer.php'; ?>


