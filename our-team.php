<?php require 'lib/db.php';
$currentPage = 'our-team.php' ?>
<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/our-team.css">
<title>Our Team</title>
<?php require 'components/navigation.php'; ?>

<div class="container outmost-container">
  <h1 class="tittle">Our Team</h1>
  <div class="row">
  <?php 

  $rows = $db->getData("SELECT * FROM team_members;");

  if ($rows !== 0) {
    foreach ($rows as $row) {
      $imgUrl = substr($row['img_url'], 1);
      if (strlen($row['description']) > 45) {
        $description = substr($row['description'], 0, 43);
        $more = substr($row['description'], 43);
        $showMoreBtn = "<a class='text-primary mt-2 d-block show-more'><span>Show more</span><i class='fas fa-chevron-down ml-2'></i></a>";
        $dots = '...';
      } else {
        $description = $row['description'];
        $more = '';
        $showMoreBtn = "<div class='show-more-place-holder'></div>";
        $dots = '';
      }

      echo "
        <div class='col-10 col-sm-8 col-md-6 col-lg-4 col-xl-3 mx-auto p-2'>
          <div class='card border-0 shadow-sm'>
            <img src='$imgUrl' class='card-img-top' alt='image'>
            <div class='card-body text-center'>
              <h5 class='card-title mb-2 text-capitalize'>$row[fullname]</h5>
              <h6 class='card-text text-black-50 mb-2'>$row[role]</h6>
              <div class='card-text text-black-50 text-left long-content'>$description<span class='dots'>$dots</span><span class='more'>$more</span></div>
              $showMoreBtn
              <div class='social-media-container'>
                <a href='$row[facebook]' class='social-media-link'><i class='fab fa-facebook-f'></i></a>
                <a href='$row[twitter]' class='social-media-link'><i class='fab fa-twitter'></i></a>
                <a href='$row[linkedin]' class='social-media-link'><i class='fab fa-linkedin-in'></i></a>
              </div>
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
