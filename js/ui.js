$(document).ready(function() {
	$(".dialog").draggable({ containment: "body", scroll: false});
		
	$("#openDatabaseButton").click(function(event) {
		$("#openDatabaseDialog").addClass("visible");
	});
});
