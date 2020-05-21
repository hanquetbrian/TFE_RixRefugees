$(document).ready(function () {
    // Auto resize the textarea
    let textArea = $('textarea.survey-form-control');
    textAreaAutosize(textArea.get(0));
    textArea.keydown(textAreaAutosize(this));

    // Add option to the survey
    let optionList = $('.survey-form-check-text');
    optionList.focus(function () {
        $('<div class="survey-form-check-group">')
            .append('<input class="survey-form-check" type="checkbox" disabled>')
            .append('<input class="survey-form-control" type="text" name="survey_options" value="Option">')
            .append('<span class="pl-3 remove-btn" onclick="removeEquipmentItem(this)"><i class=\'fas fa-times\'></i></span>')
            .appendTo('#listOption');
        $('#listOption').find('input').select();
    });

    // Action to do when a new form is created
    $('#btn_add_survey').click(function (e) {
        e.preventDefault();
        let options = [];
        $("#listOption input.survey-form-check-text").each(function () {
            options.push($(this).val());
        });
        let lodging_id = $('#id_lodging').val();
        let id_survey = $('#id_survey').val();
        let title = $("#survey-title").val();
        let description = $("#survey-description").val();

        $.post("/api/addSurvey.php", {
            id_survey: id_survey,
            lodging_id: lodging_id,
            title: title,
            description: description,
            options: options
        }).done(function (data) {
            data = JSON.parse(data);
            if(data.success) {
                window.location.href = "/survey?id_survey=" + data.lastInsertId;
            } else {
                $("#survey.alert").remove();
                $('#survey').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                    "Erreur lors de l'ajout du sondage" +
                    "</div>")
            }
        });
        // end ajax
    });

    $('form[action$="saveSurveyResult.php"]').submit(function (e) {
        e.preventDefault();

        let optionChecked = $('.form-check-input:checked');

        if(optionChecked.length === 0) {
            $("#error_message").html("veillez à cocher au moins une case avant d'envoyer le formulaire");
            $(".alert").show();
        } else {
            // let results = [];
            // optionChecked.each(function () {
            //     results.push($(this).next().text().trim());
            // });
            e.target.submit();
        }
    });
});

function textAreaAutosize(text){
    let el = text;
    setTimeout(function(){
        el.style.cssText = 'height:auto; padding:0';
        el.style.cssText = 'height:' + el.scrollHeight + 'px';
    },0);
}
