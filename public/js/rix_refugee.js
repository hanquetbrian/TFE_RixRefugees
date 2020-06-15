$(document).ready(function () {
    let surveyUrl = $("#inputSurveyUrl");
    surveyUrl.click(function () {
        /* Select the text field */
        this.select();
        this.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");

        /* Alert the copied text */
        $('#clipboard_return').text('Lien copié');
    });

    surveyUrl.focusout(function () {
        $('#clipboard_return').text('');
    });

    $("#createNewSession").click(function (e) {
        let form = $("#newSessionForm");
        if(checkAllInput(form)) {
            e.preventDefault();
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
