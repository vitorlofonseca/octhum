function formatDate(dateObject) {
    var d = new Date(dateObject);
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    var hour = d.getHours();
    var minutes = d.getMinutes();
    if (day < 10) {
        day = "0" + day;
    }
    if (month < 10) {
        month = "0" + month;
    }
    var date = day + "/" + month + "/" + year + " " + hour + ":" + minutes;

    return date;
}

/**
 * show output message
 *
 * @param msg
 * @param type
 */
function showMessage(msg, type){

    var divClass;

    //detecting the type error
    switch(type){
        case "success":
            divClass = "alert alert-success";
            break;

        case "error":
            divClass = "alert alert-danger";
            break;

        case "warning":
            divClass = "alert alert-warning";
            break;
    }

    $("#feedback").html("<button type='button' class='close' data-dismiss='alert'>x</button>"+msg);

    $("#feedback").attr('class',divClass);

    $("#feedback").fadeTo(2000, 500).slideUp(500, function(){
        $("#success-alert").slideUp(500);
    });

}