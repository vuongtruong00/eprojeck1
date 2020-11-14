<?php require 'lib/db.php';
$currentPage = 'our-work.php';

$events = $db->getData("SELECT * FROM events WHERE id = ?;", [$_GET['id']]);

if ($events === 0) {
  exit('There is no such event id');
}

$event = $events[0];

?>
<?php require 'components/header.php'; ?>
<link rel="stylesheet" href="css/our-work-detail.css">
<script src='js/our-work-detail.js' type="module" defer></script>
<title>Home</title>
<?php require 'components/navigation.php'; ?>

<header class='mb-3 bg-white'>
  <?php
  $images = $db->getData("SELECT * FROM event_images WHERE event_id = ? ;", [$_GET['id']]);
  ?>
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="5000">
    <div class="carousel-inner" role="listbox">
      <?php
      if ($images !== 0) {
        foreach ($images as $i => $img) {
          $imgUrl = substr($img['img_url'], 1);
          $active = $i === 0 ? 'active' : '';
          echo "
            <div class='carousel-item $active' style='background-image: url($imgUrl)'></div>
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
    <div class='images-container-wrapper'>
      <ol class="images-container carousel-indicators">
        <?php
        if ($images !== 0) {
          foreach ($images as $i => $img) {
            $imgUrl = substr($img['img_url'], 1);
            $active = $i === 0 ? 'active' : '';
            echo "
              <li data-target='#carouselExampleIndicators' data-slide-to='$i' class='$active'><img src='$imgUrl'></li>
            ";
          }
        }
        ?>
      </ol>

      <i class='fas fa-chevron-left ml-2'></i>
      <i class='fas fa-chevron-right ml-2'></i>
    </div>
  </div>
</header>

<main>
  <div class='container'>
    <div class='row my-5'>
      <div class='col-lg-8 shadow-sm bg-white p-4 position-relative main-left'>
        <div class='calendar text-center'>
          <div class='month-year bg-danger text-white text-uppercase'>
            <?php
            $event_day =  date('d', strtotime($event['event_date']));
            $event_month = date('M', strtotime($event['event_date']));
            $event_year = date('y', strtotime($event['event_date']));
            echo $event_month . ' ' . $event_year;
            ?>
          </div>
          <div class='day bg-white'><?php echo $event_day; ?></div>
        </div>
        <h1 class="title mb-4 text-capitalize"><?php echo $event['title']; ?></h1>
        <h5 class="my-3 font-weight-normal text-secondary font-italic"><?php echo $event['subtitle']; ?></h5>
        <div class='description'>
          <?php echo $event['description']; ?>
        </div>
        <div class='like-comment'>
          <div class='like' data-toggle="modal" data-target="#likeModal"><img src='img/svg/love.svg' alt='love-icon'></i><span></span></div>
          <div class='comment'><span></span><i class="fas fa-comments text-primary"></i></div>
        </div>
        <div class='like-comment-btn'>
          <div class='like-btn'><i class="far fa-heart mx-1"></i>Love</div>
          <div class='comment-btn'><i class="fas fa-edit mx-1"></i>Write a comment</div>
        </div>

        <div class="alert alert-warning d-none" role="alert" id='loginRequirement'>
          Please <a href="login.php?prev=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>" class="alert-link">login</a> to like and comment. If you are not a member yet, please click <a href="register.php" class="alert-link">Here</a> to register.
        </div>
        <div class='comment-container'></div>
      </div>

      <div class='col-lg-4'>
        <div class='related-event-container'>
          <h3 class='px-3 pt-4'>Related events</h3>
          <?php
          $relatedEvents = $db->getData("SELECT * FROM events WHERE category_id = ? ORDER BY event_date DESC;", [$event['category_id']]);

          if ($relatedEvents !== 0 && count($relatedEvents) > 1) {
            foreach ($relatedEvents as $i => $relEvent) {
              if ($i > 3) break;

              if ($relEvent['id'] !== $event['id']) {
                $imgUrl = substr($relEvent['img_url'], 1);
                $shortSubtitle = strlen($relEvent['subtitle']) > 110 ? substr($relEvent['subtitle'], 0, 107) . '...' : $relEvent['subtitle'];
                echo "
                  <div class='related-event-wrapper position-relative'>
                    <img class='related-event-image' src='$imgUrl'>
                    <div class='related-event-info'>
                      <h5 class='related-event-title'>$relEvent[title]</h5>
                      <div class='related-event-date'><i class='fas fa-calendar-alt mr-1'></i>$relEvent[event_date]</div>
                      <a class='text-primary stretched-link mt-3 d-block' href='our-work-detail.php?id=$relEvent[id]'></a>
                    </div>
                  </div>
                ";
              }
            }
          } else {
            echo "<h6 class='text-secondary p-3'>There is no related event</h6>";
          }
          ?>
        </div>
      </div>

    </div>
  </div>

  <div class='modal-container'></div>
</main>

<?php require 'components/go-to-join.php'; ?>
<?php require 'components/footer.php'; ?>