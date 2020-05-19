

$(function () {

	if (typeof ("getCookie") === "function") {
		$("#ScoreReporterForm input[name='submitterName']").val(getCookie("score-reporter-name"));
		$("#ScoreReporterForm input[name='submitterEmail']").val(getCookie("score-reporter-email"));
	}

	$("#SelectLeague").change(function () {
		getLeagueTeams($("#SelectTeam"), $(this).val(), 0, 1); //Skip team 1 so practice is never an option
	});

	$("#SelectTeam").change(function () {
		loadMatches($(this).val());
	});

	$("#ScoreReporterForm").validate({
		ignore: [], //Important for game results validation
		errorElement: "div",
		rules: {
			leagueID: {
				required: true,
				min: 1
			},
			teamID: {
				required: true,
				min: 2
			},
			submitterEmail: "email"
		},
		messages: {
			leagueID: {
				required: "Please enter your league.",
				min: "Please enter your league."
			},
			teamID: {
				required: "Please enter your team.",
				min: "Please enter your team."
			},
			submitterName: {
				required: "Please enter your name (at least 3 characters)"
			}
		},
		errorPlacement: function (error, element) {
			error.appendTo('#errordiv');
		},
		highlight: function (element, errorClass, validClass) {
			$(element).addClass('error');
			$(element.form).find("label[for=" + element.id + "]")
				.addClass("error");
		},
		unhighlight: function (element, errorClass, validClass) {
			$(element).removeClass('error');
			$(element.form).find("label[for=" + element.id + "]")
				.removeClass("error");
		}
	});

	$("#ScoreReporterForm").ajaxForm({
		method: "post",
		type: "post",
		dataType: "json",
		beforeSubmit: function () {
			return $("#ScoreReporterForm").valid(); // TRUE when form is valid, FALSE will cancel submit
		},
		success: function (resp) {
			if (resp.status == 1) {
				setCookie("score-reporter-name", $("#ScoreReporterForm input[name='submitterName']").val(), 365);
				setCookie("score-reporter-email", $("#ScoreReporterForm input[name='submitterEmail']").val(), 365);

				var cont = $("<div />").html(resp.html);
				$("#ScoreReporterForm").replaceWith(cont);
				$("#ScoreReporterThankYou")[0].scrollIntoView();
			} else {
				alert(resp.errorMessage);
			}
		},
		error: function () {
			alert("Error connecting to the server. Please try again.");
		}
	});
});

function getLeagueTeams(select, leagueId, selectedTeamId, skipTeamId) {
	if (leagueId > 0) {
		$.ajax({
			url: baseUrl + "/get-league-teams/" + leagueId,
			dataType: "json",
			success: function (resp) {
				if (resp.status === 1 && resp.data.hasOwnProperty('teams')) {
					select.html("<option value='-1'></option>");
					for (var i in resp.data.teams) {
						var team = resp.data.teams[i];
						var id = team.id;
						var name = team.name;

						if (skipTeamId === id) {
							continue;
						}

						select.append("<option value='" + id + "' " + (selectedTeamId === id ? "selected='selected'" : "") + ">" + name + "</option>");
					}
				} else {
					alert(resp.errorMessage);
				}
			},
			error: function () {
				alert("Error connecting to server.");
			}
		});
	}
}

function loadMatches(teamId) {
	if (teamId > 0) {

		var loadData = function () {
			$("#Matches .game").each(function () {
				var oppTeamId = $(this).attr("data-opp-team-id");
				getLeagueTeams($(this).find(".teamSelect"), $("#SelectLeague").val(), oppTeamId, teamId);
			});

			$("#Matches").find(".radioButtons").bsFormButtonset('attach');
			$("#Matches").find(".radioButtons").find('.btn').click(function () {
				var clickedRadio = $("#" + $(this).attr("data-input-id"));
				clickedRadio.siblings('.radioButtonsResult').val(clickedRadio.val());
			});

			$("#Matches").find("input[type='number']").spinner();

			/* $("#Matches").find(".spirit").each(function() {
				$(this).bootstrapSlider({
					precision:1,
					step: 0.5,
					min: 1,
					max: 5,
					value:5,
					ticks: [1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5],
					ticks_labels: ['1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5']
				});

				/* $(this).on("change", function(slideEvt) {
					$("#" + $(slideEvt.target).attr("id") + "_Val").text(slideEvt.value.newValue);
				}); 
			}); */

			$("#Matches").find("> .row").animate({ opacity: 1 }, 200);
		};


		$.ajax({
			url: baseUrl + "/score-reporter-matches/" + teamId,
			dataType: "html",
			success: function (resp) {

				if ($("#Matches").find("> .row").length > 0) {
					$("#Matches").find("> .row").animate({ opacity: 0 }, 200, function () {
						$("#Matches").html(resp);
						loadData();
					});
				} else {
					$("#MatchesFieldset").slideDown(300, function () {
						$("#Matches").html(resp);
						loadData();
					});
				}
			},
			error: function () {
				alert("Error connecting to server.");
			}
		});
	} else {
		$("#MatchesFieldset").slideUp();
	}
}
