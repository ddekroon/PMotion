/**
 * Sends a notification to the screen auto formatted - https://github.com/mouse0270/bootstrap-notify
 * msg: the message you want in the notification
 * type: matches bootstrap alert types: success, info, warning, danger
 */
function notify(msg, type) {
    type = type || "info";

    var icon = "glyphicon glyphicon-info-sign";

    if(type === "success") {
        icon = "glyphicon glyphicon-check";
    } else if(type === "warning") {
        icon = "glyphicon glyphicon-warning-sign";
    } else if(type === "danger") {
        icon = "	glyphicon glyphicon-remove";
    }

    $.notify({
        // options
        message: msg,
        icon: icon
    },{
        // settings
        type: type,
        mouse_over: "pause"
    });

}