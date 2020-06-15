$(document).ready(function () {

// Preview of the image when adding a new lodging
    $("#upload-photo").change(function () {
        previewImage(this, "previewFile")
    });

// Add the equipment in the list when the user press ENTER
    let inputTextAddEquipment = $('#inputListEquipments');
    inputTextAddEquipment.keypress(function (event) {
        // check if ENTER is pressed
        if(event.which === 13 && this.value) {
            let val = this.value;
            $('<li class="list-group-item equipment"></li>').text(val).append("<span class='float-right remove-btn' onclick='removeEquipmentItem(this)'><i class='fas fa-trash-alt'></i></span>")
                .appendTo(inputTextAddEquipment.parent().parent("ul"));

            inputTextAddEquipment.val("");
        }
    });

// Action to perform when the add lodging button is pressed.
    $("#addLodgingButton").click(function () {
        let addLodgingForm = $("#addLodgingForm");
        if(!checkAllInput(addLodgingForm)) {
            let equipments = [];
            $("#listEquipments ul li.equipment").each(function () {
                equipments.push($(this).text());
            });
            let formData = new FormData();
            formData.append('image', $('#upload-photo')[0].files[0]);
            formData.append('name', $('#inputLodgingName').val());
            formData.append('nb_place', $('#inputMaxPlaces').val());
            formData.append('date_from', $('#inputLodgingDateFrom').val());
            formData.append('date_to', $('#inputLodgingDateTo').val());
            formData.append('address', $('#inputAddress').val());
            formData.append('equipments', equipments.join(','));

            $.ajax( "/api/addLodging.php", {
                type: "POST",
                data: formData,
                processData: false,
                contentType: false
            }).done(function (returned_data) {
                let result = returned_data;
                try {
                    result = JSON.parse(returned_data);
                    if(result.error) {
                        $("#lodgings .alert").remove();
                        $('#lodgings').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                            "Erreur lors de la création de l'hébergement: " + data.error['msg'] +
                            "</div>");
                    }
                    if(result.success){
                        addLodgingForm.find("input").each(function () {
                            this.value = "";
                        });
                        $('#addLodgingForm .btn-image-picker img').remove();
                        $('#addLodging').modal('hide');
                        location.reload();
                    }
                }catch (e) {
                    console.log(returned_data);
                    console.log(e);
                }
            });
        }
    });
});

