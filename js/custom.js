var prodManager = {
  
  init: function() {
    prodManager.config = {
      productsList: $("ul.products-list"),
      apiRestUrl: "http://products:8080/api",
      buttonDelete: $('<button>Delete</button>'),
      buttonDeleteModal: $('<button type="button" class="btn btn-primary">Yes</button>'),
      buttonEdit: $('<button>Edit</button>'),
      buttonSaveModal: $('<button type="button" class="btn btn-primary">Save</button>')
    }
    prodManager.config.buttonDelete.addClass("btn btn-secondary btn-delete").attr("type","button").attr("data-toggle","modal").attr("data-target","#deleteConfirmation");
    prodManager.config.buttonEdit.addClass("btn btn-info btn-edit").attr("type","button").attr("data-toggle","modal").attr("data-target","#formProductModal");
    prodManager.updateList();

    $("#newprodbutton").click(prodManager.editProduct.bind({isnew: true}));

    $('.WYSIWYG-editor').froalaEditor({
      toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'fontFamily', 'fontSize', 'color'],
      pluginsEnabled: null
    });
  },

  updateList: function() {
    $.ajax({
      url: prodManager.config.apiRestUrl + "/product"
    })
    .done(function( data ) {
      let products = JSON.parse(data).products;
      prodManager.config.productsList.empty();
      if(products.length === 0) {
        prodManager.config.productsList.append($("<li class='product-row list-group-item'>No products were added yet</li>"));
      }
      else {
        $.each(products, function( index, product ) {
          let productElement = $("<li class='product-row list-group-item' id='prodNr" + product.products_id + "'></li>")
            .append($("<span class='prod-default-name'>" + product.products_description_name + "</span>"))
            .append($("<span class='prod-ref'>" + product.products_reference + "</span>"))
            .append($("<span class='prod-price'>" + product.products_price + "</span>"));

          let deleteButton = prodManager.config.buttonDelete.clone();
          deleteButton.click(prodManager.deleteProduct.bind(product));
          productElement.append(deleteButton);

          let editButton = prodManager.config.buttonEdit.clone();
          editButton.click(prodManager.editProduct.bind(product));
          productElement.append(editButton);

          prodManager.config.productsList.append(productElement);
        });
      }
    });
  },

  deleteProduct: function() {
    const prodId = this.products_id;
    $("#yesDeleteButton").remove();
    let yesDeleteModal = prodManager.config.buttonDeleteModal.clone().attr("id", "yesDeleteButton");

    yesDeleteModal.click(function() {
      $.ajax({
        method: "DELETE",
        url: prodManager.config.apiRestUrl + "/product/" + prodId
      })
      .done(function(data) {
        $('#deleteConfirmation').modal("hide");
        prodManager.updateList();
      });
    });

    $("#deleteConfirmation .modal-footer").append(yesDeleteModal)
  },

  cleanUpForm: function() {
    $("input.form-control").val("");
    $(".WYSIWYG-editor").froalaEditor('html.set', "");
  },

  setProductDescriptions: function(id) {
    $.ajax({
      url: prodManager.config.apiRestUrl + "/productdesc/" + id
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
  },

  editProduct: function() {
    let binded = this;
    let isUpdate = true;
    let prodId = "";
    let yesSaveProduct;

    prodManager.cleanUpForm();

    if(binded.isnew) {
      isUpdate = false;
      $("#editModalLabel").text("Create Product");
    } 
    else {
      prodId = binded.products_id;
      $("#editModalLabel").text("Edit Product");
      $("#product-reference").val(binded.products_reference);
      $("#product-price").val(binded.products_price);
      prodManager.setProductDescriptions(prodId);
    }
    $("#yesSaveProduct").remove();
    yesSaveProduct = prodManager.config.buttonSaveModal.clone().attr("id", "yesSaveProduct");

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
              url: prodManager.config.apiRestUrl + "/product",
              contentType : 'application/json',
              data: JSON.stringify(productData),
              success: function(resp) {
                $('#formProductModal').modal("hide");
                prodManager.updateList();
              }
            });
          }
          else {
            $.ajax({
              type : 'POST',
              url: prodManager.config.apiRestUrl + "/product",
              contentType : 'application/json',
              data: JSON.stringify(productData),
              success: function(resp) {
                $('#formProductModal').modal("hide");
                prodManager.updateList();
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
};

$(prodManager.init);