<?php require 'lib/db.php';
$currentPage = 'services.php';

$rows = $db->getData("SELECT * FROM services WHERE id = ?;", [$_GET['id']]);

if ($rows === 0) {
  exit('These is no such id service');
}

$service = $rows[0];

function callBack($matches) {
  return ucfirst($matches[0]);
}

$title = ucfirst(preg_replace_callback('/(?<=\s)\w/', 'callBack', $service['title']));
$imgUrl = substr($service['img_url'], 1);
?>

<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/service-detail.css">
<title><?php echo $title; ?></title>
<?php require 'components/navigation.php'; ?>
  
<div style="background-image: linear-gradient(rgba(0, 0, 0, .1), rgba(0, 0, 0, .3)), url(<?php echo $imgUrl ?>)" class="jumbotron bg-cover text-white mb-0">
    <div class="container mt-5 py-5 text-center">
      <div class='row'>
        <div class='col-sm-11 col-lg-9 mx-auto'>
          <h1 class="display-4 font-weight-bold text-capitalize"><?php echo $service['title'] ?></h1>
          <p class="subtitle my-4"><?php echo $service['subtitle'] ?></p>
          <a href="email-us.php" role="button" class="btn btn-success px-5 py-2 mt-3 font-weight-bold">Join Us !</a>
        </div>
      </div>
    </div>
</div>

<section>
  <div class="container py-5">
      <div class="row">
          <div class="col-sm-11 col-lg-9 my-4 mx-auto">
            <?php echo $service['content'] ?>
          </div>
      </div>
  </div>
</section>

<?php 
$relatedServices = $db->getData("SELECT * FROM services WHERE category_id = ? ORDER BY created_at DESC;", [$service['category_id']]);

if ($relatedServices !== 0 && count($relatedServices) > 1) {
?>
<section>
  <div class="container py-5">
      <h2 class="text-center text-capitalize mb-5 section-title">Other themes</h2>
      <div class="row">
      <?php 

        foreach ($relatedServices as $i => $row) {
          if ($i > 3) break;

          if ($row['id'] !== $service['id']) {
            $imgUrl = substr($row['img_url'], 1);
            $shortSubtitle = strlen($row['subtitle']) > 110 ? substr($row['subtitle'], 0, 107) . '...' : $row['subtitle'];
            echo "
              <div class='col-10 col-sm-8 col-md-6 col-lg-4 mx-auto mb-4'>
                <div class='card border-0 shadow-sm'>
                  <img src='$imgUrl' class='card-img-top' alt='...'>
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
      <?php 
      if (count($relatedServices) > 4) {
        echo "
          <h4 class='text-center font-weight-light mt-4'><a href='services.php?category_id=$service[category_id]'>See more >></a></h4>
        ";
      }
      ?>
  </div>
</section>
<?php 
}
?>

<?php require 'components/go-to-join.php'; ?>
<?php require 'components/footer.php'; ?>
