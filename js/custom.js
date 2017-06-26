const productsList = $("ul.products-list");
const apiRestUrl = "http://products:8080/api";

const buttonDelete = $('<button>Delete</button>');
buttonDelete.addClass(["btn","btn-primary"]).attr("type","button").attr("data-toggle","modal").attr("data-target","#deleteConfirmation");

const buttonDeleteModal = $('<button type="button" class="btn btn-primary">Yes</button>');

function deleteProduct() {
  const prodId = this.products_id;
  $.ajax({
    method: "DELETE",
    url: apiRestUrl + "/product/" + prodId
  })
  .done(function(data) {
    $('#deleteConfirmation').modal("hide");
    updateList();
  });
}

function updateList() {
  $.ajax({
    url: apiRestUrl + "/product"
  })
  .done(function( data ) {
    let products = JSON.parse(data).products;
    productsList.empty();
    $.each(products, function( index, product ) {
      let productElement = $("<li class='product-row' id='prodNr" + product.products_id + "'></li>")
        .append($("<span class='prod-ref'>" + product.products_reference + "</span>"))
        .append($("<span class='prod-price'>" + product.products_price + "</span>"));

      let deleteButton = buttonDelete.clone();

      $("#yesDeleteButton").remove();
      let yesDeleteModal = buttonDeleteModal.clone().attr("id", "yesDeleteButton");
      yesDeleteModal.click(deleteProduct.bind(product));
      $("#deleteConfirmation .modal-footer").append(yesDeleteModal);
      productElement.append(deleteButton);
      productsList.append(productElement);
    });
  });
}

updateList();
