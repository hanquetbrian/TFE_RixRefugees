$(document).ready(function () {
    // Auto resize the textarea
    $('textarea.survey-form-control').keydown(textAreaAutosize);

});


function textAreaAutosize(){
    let el = this;
    setTimeout(function(){
        el.style.cssText = 'height:auto; padding:0';
        el.style.cssText = 'height:' + el.scrollHeight + 'px';
    },0);
}