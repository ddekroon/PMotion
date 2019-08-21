/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function removeTeamFromDashboard(teamID, row) {
	$.ajax({
		type: "POST",
		url: baseUrl + "/remove-team/" + teamID,
		dataType: "json",
		success: function(response) {
			if(response.status != 1) {
				alert("Error deleting team, please refresh the page and try again.");
			} else {
				if(typeof(row) !== "undefined") {
					$(row).detach();
				}
			}
		},
		failure: function() {
			alert("Error connecting to server.");
		}
	});
}
