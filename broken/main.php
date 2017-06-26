<?php 

function getLanguages() {
  $link = mysqli_connect('localhost', 'root', '', 'dury');
  mysqli_set_charset($link,'utf8');

  $result = mysqli_query($link, "select * from languages");
  $langs = array();
  for ($i = 0; $i < mysqli_num_rows($result); $i++) {
    $langs[$i] = mysqli_fetch_object($result);
  }

  mysqli_close($link);
  return $langs;
}

function showMain() {
  ?> 
  <html>
    <head>
      <meta charset="UTF-8">
      <title>Products</title>
      <link rel="stylesheet" href="assets/bootstrap4.min.css">
      <link rel="stylesheet" href="assets/styles.css">

      <!-- Froala external style dependencies -->
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.css">
   
      <!-- Froala Styles -->
      <link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.6.0/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />
      <link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.6.0/css/froala_style.min.css" rel="stylesheet" type="text/css" />

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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#formProductModal" id="newprodbutton">New Product</button>
          </div>
        </div>
      </section>

      <!-- Modal: Delete Confirmation-->
      <div class="modal fade" id="deleteConfirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
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

      <!-- Modal: Form Edit product-->
      <div class="modal fade" id="formProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Product</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="product-data" role="form" data-toggle="validator">
                <div class="form-group">
                  <label for="product-reference">Reference</label>
                  <input type="text" id="product-reference" placeholder="Reference Code" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="product-price">Price</label>
                  <input type="number" id="product-price" placeholder="Price" class="form-control" required>
                </div>
                <?php
                $langs = getLanguages();
                foreach ($langs as $lang) {
                ?>
                <fieldset class="form-group lang-box-wrapper" lang-id="<?php echo $lang->languages_id; ?>">
                  <legend><?php echo $lang->languages_name; ?></legend>
                  <label for="name-product-<?php echo strtolower($lang->languages_name); ?>">Name</label>
                  <input class="form-control" type="text" placeholder="Name" id="name-product-<?php echo strtolower($lang->languages_name); ?>" required>
                  <label for="short-product-<?php echo strtolower($lang->languages_name); ?>">Short description</label>
                  <textarea id="short-product-<?php echo strtolower($lang->languages_name); ?>" class="WYSIWYG-editor form-control" required></textarea>
                  <label for="long-product-<?php echo strtolower($lang->languages_name); ?>">Long description</label>
                  <textarea id="long-product-<?php echo strtolower($lang->languages_name); ?>" class="WYSIWYG-editor form-control" required></textarea>
                </fieldset>
                <?php
                }
                ?>
                
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="yesSaveProduct">Save</button>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>

      <!-- Bootstrap 4 external dependecy -->
      <script type="text/javascript" src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
      <script type="text/javascript" src="js/bootstrap4.min.js"></script>
      <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.js"></script> -->

      <!-- Froala external js dependencies -->
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/mode/xml/xml.min.js"></script>
   
      <!-- Froala js -->
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.6.0/js/froala_editor.pkgd.min.js"></script>

      <script type="text/javascript" src="js/custom.js"></script>
    </body>
  </html>
  <?php 
}

?>
