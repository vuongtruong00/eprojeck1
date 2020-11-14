<?php include '../lib/db.php';
$currentPage = 'our-work.php'; ?>
<?php include 'cms.check-logged-in.php'; ?>
<?php include './components/header.php'; ?>

<!-- dataTable -->
<link rel="stylesheet" href="../lib/css/dataTables.bootstrap4.min.css">
<script src="../lib/js/jquery.dataTables.min.js" defer></script>
<script src="../lib/js/dataTables.bootstrap4.min.js" defer></script>

<link rel='stylesheet' href='css/our-work.images.css'>
<script src="js/our-work.images.js" type="module" defer></script>

<title>Our Work images</title>

<?php include './components/navigation.php'; ?>

<div class="container">
  <a id='addBtn' class="btn btn-primary mt-4 px-2 px-4" href="our-work.php"><i class="fas fa-chevron-left mr-2"></i>Back</a>
  <label id='upload-label' for="upload" class="file-upload btn btn-success rounded-pill border-0 shadow-sm d-block mx-auto my-4 px-5 py-2">
    <i class="fa fa-upload mr-2"></i>Upload images
    <input id="upload" type="file" multiple>
  </label>
  <div class="row mt-4" id='item-container'>

  </div>
  <button id="delete-all" data-toggle="modal" data-target="#confirmDelete" class='btn btn-danger d-none mx-auto my-5 px-5'>Delete all</button>

  <!-- Modal -->
  <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabel">Confirm</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include './components/footer.php'; ?>