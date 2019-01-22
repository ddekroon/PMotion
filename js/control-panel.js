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

function togglePaid(clicked, teamId) {
    if(teamId == null || teamId <= 0) {
        notify("Invalid team", "danger");
        return;
    }

    $(clicked).attr("disabled", "disabled");
    var paid = $(clicked).hasClass("active");

    $.post(baseUrl + "/api/teams/toggle-paid/" + teamId, function(data) {
        if(paid) {
            notify("Team marked NOT paid", "success");
            $(clicked).removeClass("active");
        } else {
            notify("Team marked paid", "success");
            $(clicked).addClass("active");
        }
        
        $(clicked).removeAttr("disabled");
    }, "text")
    .fail(function(response, textStatus, errorThrown) {
        console.log(response);
        console.log(textStatus);
        console.log(errorThrown);
        notify("Error toggling team paid", "danger");
        $(clicked).removeAttr("disabled");
    });
}

function deregisterTeam(clicked, teamId, callback) {
    if(teamId == null || teamId <= 0) {
        notify("Invalid team", "danger");
        return;
    }

    $(clicked).attr("disabled", "disabled");

    $.post(baseUrl + "/api/teams/deregister/" + teamId, function(data) {
        notify("Team deregistered", "success");
        if(typeof(callback) === "function") {
            callback();
        } else {
            $(clicked).closest(".team").detach();
        }
    }, "text")
    .fail(function(response, textStatus, errorThrown) {
        notify("Error deregistering team", "danger");
        $(clicked).removeAttr("disabled");
    });
}

function registerTeam(clicked, teamId, callback) {
    if(teamId == null || teamId <= 0) {
        notify("Invalid team", "danger");
        return;
    }

    $(clicked).attr("disabled", "disabled");

    $.post(baseUrl + "/api/teams/register/" + teamId, function(data) {
        notify("Team registered", "success");

        if(typeof(callback) === "function") {
            callback();
        } else {
            $(clicked).closest("tr").detach();
        }
    }, "text")
    .fail(function(response, textStatus, errorThrown) {
        notify("Error registering team", "danger");
        $(clicked).removeAttr("disabled");
    });
}

function deleteTeam(clicked, teamId) {
    pmConfirm("Are you sure you want to delete this team? This cannot be undone.",
    function() {
        $(clicked).attr("disabled", "disabled");

        $.ajax(
            baseUrl + "/api/teams/" + teamId, 
            {
                dataType: "text",
                type: "delete"
            }
        )
        .done(function(data, textStatus, response) {
            notify("Team deleted", "success");
            $(clicked).closest(".team").detach();
        })
        .fail(function(response, textStatus, errorThrown) {
            notify("Error deleting team", "danger");
            $(clicked).removeAttr("disabled");
        });
    },
    function() {
        //cancel, do nothing.
    });
}

function addPlayerToTeam(playerId, teamId, callback) {
    if(playerId == null || playerId <= 0) {
        notify("Invalid player", "danger");
        return;
    }

    $.post(baseUrl + "/api/players/addPlayerToTeam/" + playerId + "/" + teamId, function(data) {
        notify("Player " + (teamId > 0 ? "added to" : "removed from") + " team", "success");

        if(typeof(callback) === "function") {
            callback();
        }
    }, "text")
    .fail(function(response, textStatus, errorThrown) {
        notify("Error adding player to team", "danger");
    });
}

function addGroupToTeam(groupId, teamId, callback) {
    if(groupId == null || groupId <= 0 || teamId == null || teamId <= 0) {
        notify("Invalid group", "danger");
        return;
    }

    $.post(baseUrl + "/api/players/addGroupToTeam/" + groupId + "/" + teamId, function(data) {
        notify("Group " + (teamId > 0 ? "added to" : "removed from") + " team", "success");

        if(typeof(callback) === "function") {
            callback();
        }
    }, "text")
    .fail(function(response, textStatus, errorThrown) {
        notify("Error adding group to team", "danger");
    });
}

function deletePlayer(clicked, playerId) {
    pmConfirm("Are you sure you want to delete this player? This cannot be undone.",
    function() {
        $(clicked).attr("disabled", "disabled");

        $.ajax(
            baseUrl + "/api/players/" + playerId, 
            {
                dataType: "text",
                type: "delete"
            }
        )
        .done(function(data, textStatus, response) {
            notify("Player deleted", "success");
            $(clicked).closest(".player").detach();
        })
        .fail(function(response, textStatus, errorThrown) {
            notify("Error deleting player", "danger");
            $(clicked).removeAttr("disabled");
        });
    },
    function() {
        //cancel, do nothing.
    });
}

function removePlayerFromGroup(clicked, playerId, callback) {
    pmConfirm("Are you sure you want to remove this player from their group? This cannot be undone.",
    function() {
        $(clicked).attr("disabled", "disabled");

        $.ajax(
            baseUrl + "/api/players/removePlayerFromGroup/" + playerId, 
            {
                dataType: "text",
                type: "post"
            }
        )
        .done(function(data, textStatus, response) {
            notify("Player removed from group", "success");
            if(typeof(callback) === "function") {
                callback();
            }
        })
        .fail(function(response, textStatus, errorThrown) {
            notify("Error removing player from group", "danger");
            $(clicked).removeAttr("disabled");
        });
    },
    function() {
        //cancel, do nothing.
    });
}

function quickAddTeam(leagueId) {

    var id = "AddTeam";
    var title = "Add Team";
    var buttons = [];

    var save = $('<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Add</button>');
    var cancel = $('<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>');

    save.click(function() {
        $("#" + id).find("form").submit();
    })

    buttons.push(save);
    buttons.push(cancel);

    var onLoaded = function() {

        var form = $("#" + id).find("form");

        form.validate({
			ignore: [], //Important for game results validation
			errorElement: "div",
			rules: {
				teamName: {
					required: true
                }
            }
        });

        form.ajaxForm({
			method:"POST",
            dataType:"text",
            beforeSubmit:function () {
				return form.valid(); // TRUE when form is valid, FALSE will cancel submit
			},
			success:function(resp) {
				notify("Team Created", "success");
                $("#" + id).modal("hide");
                location.reload();
			},
			error:function(resp) {
				notify("Error connecting to server.", "danger");
			}
		});
    }

    pmModal(baseUrl + "/control-panel/registration/team-quick-add/" + leagueId, id, title, buttons, onLoaded);
}