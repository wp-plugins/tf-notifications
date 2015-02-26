jQuery(document).ready(function($) {
	$(function() {
		$(".showDateTime").datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss'
		});
	});
	
	$(function() {
		$(".showDateTime.startdate").datetimepicker("option", {
			onClose: function(dateText, inst) {
				if(dateText != "" || $(".startdate").val() != "") {
					$(".displaydate").removeAttr("disabled");
					var theDate = $(".startdate").datetimepicker("getDate");
					
					//$(".showDateTime.displaydate").datetimepicker('option', 'minDate', new Date());
					//$(".showDateTime.displaydate").datetimepicker('option', 'maxDate', theDate);
					//$(".showDateTime.displaydate").datetimepicker('option', 'minDateTime', new Date());
					//$(".showDateTime.displaydate").datetimepicker('option', 'maxDateTime', theDate);
					
					$(".showDateTime.enddate").datetimepicker('option', 'minDate', theDate);
					$(".showDateTime.enddate").datetimepicker('option', 'minDateTime', theDate);
				} else {
					$(".displaydate").attr("disabled","disabled");
					$(".displaydate").val("");
				}
			}
		});
	});
	
	if($(".showDateTime.startdate") && $(".showDateTime.startdate").val() != "") {
		$(".displaydate").removeAttr("disabled");
	}

});