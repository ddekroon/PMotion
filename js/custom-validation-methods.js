
//Opp Team is selected
$.validator.addMethod("oppTeamRequired", function(value, element, params) {
	return value !== null && value > 0;
}, "Please enter your opponent.");

//Comments are required 
$.validator.addMethod("commentRequired", function(value, element, params) {
	return $(element).closest(".game").find(".spiritResult").val() >= 4;
}, "A comment is required when a spirit score of 3.5 or less is given.");

$.validator.addMethod("gameResultRequired", $.validator.methods.required, "Game results are required.");

$.validator.addMethod("spiritRequired", $.validator.methods.required, "Spirit scores are required.");

$.validator.addClassRules("comment", {
	commentRequired: true
});

$.validator.addClassRules("gameResult", {
	gameResultRequired: true
});

$.validator.addClassRules("spiritResult", {
	spiritRequired: true
});

$.validator.addClassRules("oppTeamRequired", {
	oppTeamRequired: true
});