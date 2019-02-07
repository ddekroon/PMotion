function quickEditPlayer(playerId) {

    var id = "EditPlayerModal";
    var title = "Edit Player";
    var buttons = [];

    var save = $('<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Save</button>');
    var cancel = $('<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>');

    save.click(function() {
        $("#" + id).find("form").submit();
    })

    buttons.push(save);
    buttons.push(cancel);

    var onLoaded = function() {

        var form = $("#" + id).find("form");

        form.validate({
			ignore: [],
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
				return form.valid();
			},
			success:function(resp) {
                notify("Player Updated", "success");
                $("#" + id).modal("hide");
                location.reload();
			},
			error:function(resp) {
				notify("Error connecting to server.", "danger");
			}
		});
    }

    pmModal(baseUrl + "/control-panel/registration/player/" + playerId, id, title, buttons, onLoaded);
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

function quickAddFreeAgentToLeague(leagueId) {

    var id = "AddFreeAgentModal";
    var title = "Add Free Agent";
    var buttons = [];

    var save = $('<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Save</button>');
    var cancel = $('<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>');

    save.click(function() {
        $("#" + id).find("form").submit();
    })

    buttons.push(save);
    buttons.push(cancel);

    var onLoaded = function() {

        var form = $("#" + id).find("form");

        form.validate({
			ignore: [],
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
				return form.valid();
			},
			success:function(resp) {
                notify("Free Agent Added", "success");
                $("#" + id).modal("hide");
                //location.reload();
			},
			error:function(resp) {
				notify("Error connecting to server.", "danger");
			}
		});
    }

    pmModal(baseUrl + "/control-panel/registration/league-add-free-agent/" + leagueId, id, title, buttons, onLoaded);
}