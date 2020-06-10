$(document).ready(function () {
    // Preview of the image when adding a new lodging
    $("#upload-photo").change(function () {
        previewImage(this, "previewFile")
    });

    // Add the equipment in the list when the user press ENTER
    let inputTextAddEquipment = $('#inputListEquipments');
    inputTextAddEquipment.keypress(function (event) {
        // check if ENTER is pressed
        if(event.which === 13) {
            let val = this.value;
            $('<li class="list-group-item equipment"></li>').text(val).append("<span class='float-right remove-btn' onclick='removeEquipmentItem(this)'><i class='fas fa-trash-alt'></i></span>")
                .appendTo(inputTextAddEquipment.parent().parent("ul"));

            inputTextAddEquipment.val("");
        }
    });

    // Action to perform when the add lodging button is pressed.
    $("#addLodgingButton").click(function () {
        if(!checkAllInput($("#addLodgingForm"))) {
            let equipments = [];
            $("#listEquipments ul li.equipment").each(function () {
                equipments.push($(this).text());
            });

            let data = {
                name: $('#inputLodgingName').val(),
                date_from: $('#inputLodgingDateFrom').val(),
                date_to: $('#inputLodgingDateTo').val(),
                nb_place: $('#inputMaxPlaces').val(),
                address: $('#inputAddress').val(),
                equipments: equipments
            };
            $.ajax( "/api/addLodging.php", {
                type: "POST",
                data: data
            }).done(function (returned_data) {
                console.log(returned_data);
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

    // Action to perform when the add host button is pressed.
    $("#addHostgButton").click(function () {
        if(!checkAllInput($("#addHostForm"))) {
            let data = {
                name: $('#inputHostName').val(),
                comment: $('#inputComment').val()
            };
            $.ajax( "/api/addLodging.php", {
                type: "POST",
                data: data
            }).done(function (returned_data) {
                console.log(returned_data);
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

function checkAllInput(form) {
    let required = false;
    form.find("input[required]").each(function () {
        if(!this.value) {
            this.style.border = "red solid 1px";
            required = true;
        } else {
            this.style.border = "1px solid #ced4da";
        }
    });
    return required;
}

function removeEquipmentItem(equipment) {
    $(equipment).parent().remove();

    equipment.preventDefault();
    return false;
}

function previewImage(inputFile, idPreviewElement) {
    if(inputFile.files && inputFile.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            let img = $("#" + idPreviewElement);
            if(!img.length) {
                $('#addLodgingForm .btn-image-picker').prepend($('<img src="'+e.target.result+'" alt="preview_file" id="'+idPreviewElement+'" class="thumbnail">'));
            } else {
                img.attr('src', e.target.result);
            }
        };
        reader.readAsDataURL(inputFile.files[0]);
    }
}


function authorize(id) {
    $.post("/api/validateCoordinator.php", {
        id_coord: id
    }).done(function (data) {
        data = JSON.parse(data);
        if(!data.success) {
            alert(data["error"]["msg"]);
        }
        location.reload();
    });
}

function removeCoord(id) {
    $.post("/api/removeCoordinator.php", {
        id_coord: id
    }).done(function (data) {
        data = JSON.parse(data);
        if(!data.success) {
            alert(data["error"]["msg"]);
        }
        location.reload();
    });
}
