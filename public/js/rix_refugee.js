$(document).ready(function () {

    $("#previewFile").hide();
    $("#upload-photo").change(function () {
        previewImage(this, "#previewFile")
    });

    $("#inputListEquipments").keypress(function (event) {
        let keycode = (event.keycode ? event.keycode : event.which);
        if(keycode === 13) {
            val = this.value;
            $('<li class="list-group-item"></li>').text(val).append("<a href='#' class='float-right' onclick='removeEquipmentItem(this)'><i class='fas fa-trash-alt'></i></a>").appendTo("#listEquipments ul");

            $("#inputListEquipments").val("");
        }
    });

    // Action to perform when the add lodging button is pressed.
    $("#addLodgingButton").click(function () {
        let required = false;
        $("#addLodgingForm").find("input[required]").each(function () {
            if(!this.value) {
                this.style.border = "red solid 1px";
                required = true;
            }
        });
        if(!required) {
            let data = {
                name: $('#inputLodgingName').val(),
                date_from: $('#inputLodgingDateFrom').val(),
                date_to: $('#inputLodgingDateTo').val(),
                nb_place: $('#inputMaxPlaces').val(),
                address: $('#inputAddress').val()
            };
            $.ajax( "/api/addLodging.php", {
                type: "POST",
                data: data
            }).done(function (returned_data) {
                alert(returned_data);
            });
        }
    });

});

function removeEquipmentItem(equipment) {
    $(equipment).parent().remove();

    return false;
}

function previewImage(inputFile, idPreviewElement) {
    if(inputFile.files && inputFile.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            let img = $(idPreviewElement);
            img.attr('src', e.target.result);
            img.show();
        };

        reader.readAsDataURL(inputFile.files[0]);
    }
}