$(document).ready(function() {
	$(".dialog").draggable({ containment: "body", scroll: false});
	$(".dialog .closeDialog").click(function(event) {
		$(".dialog").removeClass("visible");
	});
		
	$("#openDatabaseButton").click(function(event) {
		$("#openDatabaseDialog").addClass("visible");
	});
});
