$(document).ready(function () {
    // Action to perform when the add host button is pressed.
    $("#addHostButton").click(function () {
        let addHostForm = $("#addHostForm");
        if(!checkAllInput(addHostForm)) {
            let data = {
                name: $('#inputHostName').val(),
                comment: $('#inputComment').val(),
                id_session: $('#lodging_session_id').val()
            };
            $.ajax( "/api/addHost.php", {
                type: "POST",
                data: data
            }).done(function (returned_data) {
                console.log(returned_data);
                let result = JSON.parse(returned_data);
                if(result.error) {
                    $("#hosts_list .alert").remove();
                    $('#hosts_list').prepend("<div class=\"alert alert-danger\" role=\"alert\">\n" +
                        "Erreur lors de l'ajout de la personne: " + data.error['msg'] +
                        "</div>");
                }
                if(result.success){
                    addHostForm.find("input[type=text],textarea").each(function () {
                        this.value = "";
                    });
                    let row = $('<div class="mb-3 row">');
                    row.append($('<div class="col"><span class="host_name">' + result.name + '</span></div>'));
                    row.append($('<div class="col"><span class="comment">' + result.comment + '</span></div>'));
                    $('#hostContent').append(row);
                }
            });
        }
    });
});
