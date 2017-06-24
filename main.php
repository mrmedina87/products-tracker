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
      <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="js/custom.js"></script>
    </body>
  </html>
  <?php 
}

?>
