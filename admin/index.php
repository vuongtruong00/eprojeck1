<?php include '../lib/db.php';
$currentPage = 'index.php'; ?>
<?php include 'cms.check-logged-in.php'; ?>
<?php include './components/header.php'; ?>

<!-- dataTable -->
<link rel="stylesheet" href="../lib/css/dataTables.bootstrap4.min.css">
<script src="../lib/js/jquery.dataTables.min.js" defer></script>
<script src="../lib/js/dataTables.bootstrap4.min.js" defer></script>

<script src="js/index.js" type="module" defer></script>
<title>Home</title>

<?php include './components/navigation.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-sm-12 mt-4">
      <a class="btn btn-success" href="home.slideshow.add.php">Add image</a>
      <div class="card rounded shadow-sm border-0 mt-4">
        <div class="card-body p-4 bg-white rounded">
          <div class="table-responsive">
            <table id="table" style="width:100%" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Title</th>
                  <th>Caption</th>
                  <th>Order</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="item-container">
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

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