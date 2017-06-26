const productsList = $("ul.products-list");
const apiRestUrl = "http://products:8080/api";

const buttonDelete = $('<button>Delete</button>');
buttonDelete.addClass(["btn","btn-primary"]).attr("type","button").attr("data-toggle","modal").attr("data-target","#deleteConfirmation");
const buttonDeleteModal = $('<button type="button" class="btn btn-primary">Yes</button>');

const buttonEdit = $('<button>Edit</button>');
buttonEdit.addClass(["btn","btn-primary"]).attr("type","button").attr("data-toggle","modal").attr("data-target","#formProductModal");
const buttonSaveModal = $('<button type="button" class="btn btn-primary">Save</button>');

$("#newprodbutton").click(editProduct.bind({isnew: true}));

$('.WYSIWYG-editor').froalaEditor({
  toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'fontFamily', 'fontSize', 'color'],
  pluginsEnabled: null
});

/*
, 'inlineStyle', 'paragraphStyle', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote', '-', 'insertLink', 'insertImage', 'insertVideo', 'insertFile', 'insertTable', '|', 'emoticons', 'specialCharacters', 'insertHR', 'selectAll', 'clearFormatting', '|', 'print', 'help', 'html', '|', 'undo', 'redo'
*/

function deleteProduct() {
  const prodId = this.products_id;
  $("#yesDeleteButton").remove();
  let yesDeleteModal = buttonDeleteModal.clone().attr("id", "yesDeleteButton");

  yesDeleteModal.click(function() {
    console.log(prodId);
    $.ajax({
      method: "DELETE",
      url: apiRestUrl + "/product/" + prodId
    })
    .done(function(data) {
      $('#deleteConfirmation').modal("hide");
      updateList();
    });
  });

  $("#deleteConfirmation .modal-footer").append(yesDeleteModal)
}

function editProduct() {
  let binded = this;
  let isUpdate = true;
  let prodId = "";
  if(binded.isnew) {
    isUpdate = false;
    $("#editModalLabel").text("Create Product");
  } 
  else {
    prodId = binded.products_id;
    $("#editModalLabel").text("Edit Product");
  }
  $("#yesSaveProduct").remove();
  let yesSaveProduct = buttonSaveModal.clone().attr("id", "yesSaveProduct");

  yesSaveProduct.click(function() {
    let wysiwygEditorsFilled = true;
    let wysiwygEditors = $('.WYSIWYG-editor');
    let inputsFilled = true;
    let productInputs = $("input.form-control");
    let wysiwygEditor1 = $($('.WYSIWYG-editor')[0]);
    
    for (let i = wysiwygEditors.length - 1; i >= 0; i--) {
      if($(wysiwygEditors[i]).froalaEditor('core.isEmpty')) {
        wysiwygEditorsFilled = false;  
      }
    }
    for (let i = productInputs.length - 1; i >= 0; i--) {
      if($(productInputs[i]).val() === "") {
        inputsFilled = false;
      }
    }
    if(wysiwygEditorsFilled && inputsFilled) {
      if(isUpdate) {
        console.log("put");
      }
      else {
        console.log("post"); 
      }
      $('#formProductModal').modal("hide");
    }
    else {
      alert("All the fields are required");
    }

    /*$.ajax({
      method: "DELETE",
      url: apiRestUrl + "/product/" + prodId
    })
    .done(function(data) {
      $('#deleteConfirmation').modal("hide");
      updateList();
    });*/
  });

  $("#formProductModal .modal-footer").append(yesSaveProduct)
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
      deleteButton.click(deleteProduct.bind(product));
      productElement.append(deleteButton);

      let editButton = buttonEdit.clone();
      editButton.click(editProduct.bind(product));
      productElement.append(editButton);

      productsList.append(productElement);
    });
  });
}

updateList();