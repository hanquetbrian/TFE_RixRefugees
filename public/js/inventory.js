$(document).ready(function () {
    $(".delBtn").click(function (e){
        e.preventDefault();
        let item_id = $(e.target).parent().data('item_id');

        $.post("/api/delInventory.php", {
            item_id: item_id
        }).done(function (data) {
            data = JSON.parse(data);
            if(data.success) {
                $('#item'+data.id).remove();
            } else {
                $("#inventoryContainer .alert").remove();
                $('#inventoryContainer').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                    "Erreur lors de la suppresion de l'article: " + data.error['msg'] +
                    "</div>")
            }
        });
        //end ajax
    });
});

function editInventory(e) {
    let item_id = $(e).data('item_id');
    let content = $('#item'+item_id);
    let part_name = content.children('.part_name');
    let location = content.children('.location');
    let quantity = content.children('.quantity');
    let editBtn = content.find('.editBtn').parent();

    let input =  $('<input type="text" value="'+part_name.text()+'">');
    part_name.html(input);
    input =  $('<input type="text" value="'+location.text()+'">');
    location.html(input);
    input =  $('<input type="text" value="'+quantity.text()+'">');
    quantity.html(input);

    editBtn.html('<a href="#" class="valBtn" data-item_id="' + item_id + '" onclick="validateEdit(this)"><i class="fas fa-check" style="color: #03d500"></i></a>');

}

function validateEdit(e) {
    let item_id = $(e).data('item_id');
    let content = $('#item'+item_id);
    let part_name = content.find('.part_name input');
    let location = content.find('.location input');
    let quantity = content.find('.quantity input');
    let editBtn = content.find('.editBtn').parent();

    $.post("/api/editInventory.php", {
        id: item_id,
        part_name: part_name.val(),
        location: location.val(),
        quantity: quantity.val()
    }).done(function (data) {
        data = JSON.parse(data);
        if(data.success) {
            $('#item'+data.id + ' .part_name').html(data.part_name);
            $('#item'+data.id + ' .location').html(data.location);
            $('#item'+data.id + ' .quantity').html(data.quantity);
            $('#item'+data.id + ' .valBtn').parent().html('<a href="#" class="editBtn" data-item_id="' + data.id + '" onclick="editInventory(this)"><i class="fas fa-edit" style="color: #005d88"></i></a>');
        } else {
            $("#inventoryContainer .alert").remove();
            $('#inventoryContainer').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                "Erreur lors de la modification de l'objet: " + data.error['msg'] +
                "</div>")
        }
    });
    //end ajax
}

function addItem(e) {
    let addItemForm = $("#addItemForm");
    if(!checkAllInput(addItemForm)) {
        $.post("/api/addItemInventory.php", {
            part_name: $('#inputItemName').val(),
            location: $('#inputLocation').val(),
            quantity: $('#inputQuantity').val()
        }).done(function (data) {
            $('#inputItemName').val('');
            $('#inputLocation').val('');
            $('#inputQuantity').val('');
            $('#addItem').modal('hide');
            data = JSON.parse(data);
            if(data.success) {
                let tableContent = $('#inventoryContent');

                let newItem = tableContent.children().last().clone();
                let number = newItem.find('.number');
                number.text(parseInt(number.text())+1);

                newItem.find('.part_name').text(data.part_name);
                newItem.find('.location').text(data.location);
                newItem.find('.quantity').text(data.quantity);


                newItem.find('.editBtn').parent().html('<a href="#" class="editBtn" data-item_id="' + data.id + '" onclick="editInventory(this)"><i class="fas fa-edit" style="color: #005d88"></i></a>');
                newItem.find('.delBtn').parent().html('<a href="#" class="delBtn" data-item_id="' + data.id + '"><i class="fas fa-trash-alt" style="color: #cc0013"></i></a>');

                newItem.attr("id","item"+data.id);

                tableContent.append(newItem);
            } else {
                $("#inventoryContainer .alert").remove();
                $('#inventoryContainer').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                    "Erreur lors de l'ajout de l'objet: " + data.error['msg'] +
                    "</div>")
            }
        });
        //end ajax
    }
}