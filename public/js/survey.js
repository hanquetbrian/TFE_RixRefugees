$(document).ready(function () {
    // Auto resize the textarea
    $('textarea.survey-form-control').keydown(textAreaAutosize);

    // Add option to the survey
    let optionList = $('.survey-form-check-text');
    optionList.focus(function () {
        $('<div class="survey-form-check-group">')
            .append('<input class="survey-form-check" type="checkbox" disabled>')
            .append('<input class="survey-form-control survey-form-check-text" type="text" value="Option">')
            .append('<span class="pl-3 remove-btn" onclick="removeEquipmentItem(this)"><i class=\'fas fa-times\'></i></span>')
            .appendTo('#listOption');
        $('#listOption').find('input').select();
    });
});

function textAreaAutosize(){
    let el = this;
    setTimeout(function(){
        el.style.cssText = 'height:auto; padding:0';
        el.style.cssText = 'height:' + el.scrollHeight + 'px';
    },0);
}
