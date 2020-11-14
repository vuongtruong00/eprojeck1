<?php require 'lib/db.php';
$currentPage = 'our-work.php' ?>
<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/our-work.css">
<title>Our work</title>
<?php require 'components/navigation.php'; ?>

<div class="container outmost-container">
  <div class="row">
  <?php 
  if (isset($_GET['category_id'])) {
    $rows = $db->getData("SELECT * FROM events WHERE category_id = ? ORDER BY event_date DESC;", [$_GET['category_id']]);
  } else {
    $rows = $db->getData("SELECT * FROM events;");
  }

  if ($rows !== 0) {
    foreach ($rows as $row) {
      $imgUrl = substr($row['img_url'], 1);
      
      if (strlen($row['subtitle']) > 70) {
        $shortSubtitle = substr($row['subtitle'], 0, 68);
        $shortSubtitle = substr($shortSubtitle, 0, strrpos($shortSubtitle, ' ')) . '...';
      } else {
        $shortSubtitle = $row['subtitle'];
      }

      $numberOfLike = $db->getData("SELECT COUNT(*) AS number_of_like FROM event_likes WHERE event_id = ?;", [$row['id']])[0]['number_of_like'];
      $numberOfcomment = $db->getData("SELECT COUNT(*) AS number_of_comment FROM event_comments WHERE event_id = ?;", [$row['id']])[0]['number_of_comment'];  

      echo "
        <div class='col-sm-10 col-md-6 col-xl-4 mx-auto mb-4'>
          <div class='card border-0 shadow-sm'>

            <img src='$imgUrl' class='card-img-top' alt='image'>

            <div class='card-body text-center'>
              <h3 class='card-title mb-3 text-capitalize'>$row[title]</h3>
              <div class='card-text text-black-50 long-content'>$shortSubtitle</div>
              <a class='text-primary stretched-link mt-3 d-block' href='our-work-detail.php?id=$row[id]'></a>
            </div>

            <div class='card-footer bg-white mx-4 px-1 d-flex justify-content-between'>
              <div class='like-comment'>
                <div class='like'><i class='fas fa-heart text-black-50'></i></i><span>$numberOfLike</span></div>
                <div class='comment'><i class='fas fa-comment text-black-50'></i><span>$numberOfcomment</span></div>
              </div>

              <div class='text-black-50'>$row[event_date]</div>
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
