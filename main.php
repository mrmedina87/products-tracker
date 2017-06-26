<?php 

function showMain() {
  ?> 
  <html>
    <head>
      <meta charset="UTF-8">
      <title>Products</title>
      <link rel="stylesheet" href="assets/bootstrap4.min.css">
      <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
      <section class="container">
        <div class="row">
          <div class="col col-sm-12">
            <h2>Products List</h2>
            <span class"header-reference">Reference</span>
            <span class"header-price">Price</span>
            <ul class="products-list">
            </ul>
          </div>
        </div>
      </section>

      <!-- Modal: Delete Confirmation-->
      <div class="modal fade" id="deleteConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Delete Confirmation</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              You are about to delete this product, are you sure?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Cancel</button>
              <button type="button" class="btn btn-primary" id="yesDeleteButton">Yes</button>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
      <script type="text/javascript" src="js/bootstrap4.min.js"></script>
      <script type="text/javascript" src="js/custom.js"></script>
    </body>
  </html>
  <?php 
}

?>
