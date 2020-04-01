$(document).ready(function () {
    // Auto resize the textarea
    $('textarea.survey-form-control').keydown(textAreaAutosize);

    // Add option to the survey
    let optionList = $('.survey-form-check-text');
    optionList.focus(function () {
        $('<div class="survey-form-check-group">')
            .append('<input class="survey-form-check" type="checkbox" disabled>')
            .append('<input class="survey-form-control survey-form-check-text" type="text" name="survey_options" value="Option">')
            .append('<span class="pl-3 remove-btn" onclick="removeEquipmentItem(this)"><i class=\'fas fa-times\'></i></span>')
            .appendTo('#listOption');
        $('#listOption').find('input').select();
    });

    // Action to do when the form is send
    $('form').submit(function (e) {
        e.preventDefault();
        let options = [];
        $("#listOption input.survey-form-check-text").each(function () {
            options.push($(this).val());
        });
        let title = $("#survey-title").val();
        let description = $("#survey-description").val();

        $.post("/api/addSurvey.php", {
            title: title,
            description: description,
            options: options
        }).done(function (data) {
            data = JSON.parse(data);
            if(data.success) {
                window.location.href = "/info_lodging"
            } else {
                $("#survey.alert").remove();
                $('#survey').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                    "Erreur lors de l'ajout du sondage" +
                    "</div>")
            }
            console.log(data);
        });
        // end ajax
    });
});

function textAreaAutosize(){
    let el = this;
    setTimeout(function(){
        el.style.cssText = 'height:auto; padding:0';
        el.style.cssText = 'height:' + el.scrollHeight + 'px';
    },0);
}
