$(document).ready(function () {

    // Preview of the image when adding a new lodging
    $("#previewFile").hide();
    $("#upload-photo").change(function () {
        previewImage(this, "#previewFile")
    });

    // Add the equipment in the list when the user press ENTER
    $("#inputListEquipments").keypress(function (event) {
        e.preventDefault();
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
        let addLodgingForm = $("#addLodgingForm");
        addLodgingForm.find("input[required]").each(function () {
            if(!this.value) {
                this.style.border = "red solid 1px";
                required = true;
            } else {
                this.style.border = "1px solid #ced4da";
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
                let result = JSON.parse(returned_data);
                if(result.error) {
                    //TODO Change this to a message on top of the page
                    alert('error: ' + result.error.msg);
                }
                if(result.success){
                    addLodgingForm.find("input").each(function () {
                        this.value = "";
                    });
                    $('#addLodging').modal('hide');
                }
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