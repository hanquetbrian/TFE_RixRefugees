$(document).ready(function () {
    // Auto resize the textarea
    let textArea = $('textarea.survey-form-control');
    if(textArea.length) {
        textArea.keydown(textAreaAutosize(textArea.get(0)));
    }


    // Add option to the survey
    let optionList = $('.survey-form-check-text');
    optionList.focus(function () {
        $('<div class="survey-form-check-group">')
            .append('<input class="survey-form-check" type="checkbox" disabled>')
            .append('<input class="survey-form-control option" type="text" name="survey_options" value="Option">')
            .append('<span class="pl-3 remove-btn" onclick="removeEquipmentItem(this)"><i class=\'fas fa-times\'></i></span>')
            .appendTo('#listOption');
        $('#listOption').find('input').select();
    });

    // Action to do when a new form is created
    $('#btn_add_survey').click(function (e) {
        e.preventDefault();
        let options = [];

        $("#listOption input.option").each(function () {
            let id = -1;
            if(typeof $(this).data('option_id') != 'undefined') {
                id = $(this).data('option_id');
            }

            options.push({
                id: id,
                name: $(this).val()
            });
        });
        let sessionId = $('#lodging_session_id').val();
        let description = $("#survey-description").val();

        $.post("/api/addSurvey.php", {
            sessionId: sessionId,
            description: description,
            options: options
        }).done(function (data) {
            data = JSON.parse(data);
            if(data.success) {
                window.location.href = "/info_lodging?lodging_session_id=" + sessionId + '#volunteer_request';
            } else {
                $("#survey .alert").remove();
                $('#survey').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                    "Erreur lors de l'ajout du sondage: " + data.error['msg'] +
                    "</div>")
            }
        });
        //end ajax
    });

    $('form[action*="saveSurveyResult.php"]').submit(function (e) {
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
