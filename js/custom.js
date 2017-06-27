$(function() {
  const productsList = $("ul.products-list");
  const apiRestUrl = "http://products:8080/api";
  const buttonDelete = $('<button>Delete</button>');
  const buttonDeleteModal = $('<button type="button" class="btn btn-primary">Yes</button>');
  const buttonEdit = $('<button>Edit</button>');
  const buttonSaveModal = $('<button type="button" class="btn btn-primary">Save</button>');

  buttonDelete.addClass("btn btn-secondary btn-delete").attr("type","button").attr("data-toggle","modal").attr("data-target","#deleteConfirmation");
  buttonEdit.addClass("btn btn-info btn-edit").attr("type","button").attr("data-toggle","modal").attr("data-target","#formProductModal");

  $("#newprodbutton").click(editProduct.bind({isnew: true}));
  $('.WYSIWYG-editor').froalaEditor({
    toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'fontFamily', 'fontSize', 'color'],
    pluginsEnabled: null
  });

  function deleteProduct() {
    const prodId = this.products_id;
    $("#yesDeleteButton").remove();
    let yesDeleteModal = buttonDeleteModal.clone().attr("id", "yesDeleteButton");

    yesDeleteModal.click(function() {
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

  function setProductDescriptions(id) {
    $.ajax({
      url: apiRestUrl + "/productdesc/" + id
    }).done(function(data) {
      const descs = JSON.parse(data).products_description;
      for (var i = descs.length - 1; i >= 0; i--) {
        descrElement = $('[lang-id="' + descs[i].languages_id + '"]');
        descrElement.attr("id", descs[i].products_description_id);
        descrElement.find("input.name-product").val(descs[i].products_description_name);
        descrElement.find(".short-desc").froalaEditor('html.set', descs[i].products_description_short_description);
        descrElement.find(".long-desc").froalaEditor('html.set', descs[i].products_description_description);
      }
    });
  }

  function cleanUpForm() {
    $("input.form-control").val("");
    $(".WYSIWYG-editor").froalaEditor('html.set', "");
  }

  function editProduct() {
    let binded = this;
    let isUpdate = true;
    let prodId = "";
    let yesSaveProduct;

    cleanUpForm();

    if(binded.isnew) {
      isUpdate = false;
      $("#editModalLabel").text("Create Product");
    } 
    else {
      prodId = binded.products_id;
      $("#editModalLabel").text("Edit Product");
      $("#product-reference").val(binded.products_reference);
      $("#product-price").val(binded.products_price);
      setProductDescriptions(prodId);
    }
    $("#yesSaveProduct").remove();
    yesSaveProduct = buttonSaveModal.clone().attr("id", "yesSaveProduct");

    yesSaveProduct.click(function() {
      let validDescriptions = true;
      let productData = {};
      let langs = [];
      productData.products_price = $("#product-price").val();
      productData.products_reference = $("#product-reference").val();

      if (productData.products_price && productData.products_reference) {
        productData.descriptions = [];
        langs = $("fieldset.lang-box-wrapper");

        $.each(langs, function(index, val) {
          langElement = $(val);
          langId = langElement.attr("lang-id");
          lang = {};
          lang.languages_id = langId;
          lang.products_description_name = langElement.find(".name-product").val();
          
          lang.products_description_short_description = langElement.find(".short-desc").froalaEditor('html.get');
          lang.products_description_description = langElement.find(".long-desc").froalaEditor('html.get');
          if(isUpdate) {
            lang.products_description_id = langElement.attr("id");
          }

          if(!(lang.languages_id &&
              lang.products_description_name &&
              lang.products_description_short_description &&
              lang.products_description_description)) {
            validDescriptions = false;
          }

          productData.descriptions.push(lang);
        });

        if(validDescriptions) {
          if(isUpdate) {
            productData.products_id = prodId;
            $.ajax({
              type : 'PUT',
              url: apiRestUrl + "/product",
              contentType : 'application/json',
              data: JSON.stringify(productData),
              success: function(resp) {
                $('#formProductModal').modal("hide");
                updateList();
              }
            });
          }
          else {
            $.ajax({
              type : 'POST',
              url: apiRestUrl + "/product",
              contentType : 'application/json',
              data: JSON.stringify(productData),
              success: function(resp) {
                $('#formProductModal').modal("hide");
                updateList();
              }
            });
          }
        }
        else {
          alert("All fields are required");
        }
      }
      else {
        alert("All fields are required");
      }

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
      if(products.length === 0) {
        productsList.append($("<li class='product-row list-group-item'>No products were added yet</li>"));
      }
      else {
        $.each(products, function( index, product ) {
          let productElement = $("<li class='product-row list-group-item' id='prodNr" + product.products_id + "'></li>")
            .append($("<span class='prod-default-name'>" + product.products_description_name + "</span>"))
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
      }
    });
  }

  updateList();
});