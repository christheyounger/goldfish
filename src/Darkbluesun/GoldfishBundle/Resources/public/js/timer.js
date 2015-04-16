var start;
var timer;
var end;

$(function() {
	$('#timer-panel').bind("DOMSubtreeModified",function(){
		initTimer()
	});
})

function startTimer() {
	clearInterval(timer);
	start = start ? start : moment();
	$('#start-time-field, #end-time-field').hide();
	$('#start-time').html(moment(start).format('h:mm:ss a'));
	$('#start-time-field').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
	$('#start-time-field').data("DateTimePicker").date(moment(start).format('YYYY-MM-DD HH:mm:ss'));
	$('#end-time').html('');
	timer = setInterval(stepTimer,1000);
	stepTimer();
}
function stopTimer() {
	clearInterval(timer);
	end = moment();
	$('#end-time').html(moment().format('h:mm:ss a'));
	$('#end-time-field').val(moment().format('YYYY-MM-DD HH:mm:ss'));
	$('#end-time-field').data("DateTimePicker").date(moment().format('YYYY-MM-DD HH:mm:ss'));
}

function stepTimer() {
	$('span#timer').html(
		moment().subtract(moment(start)).format('HH:mm:ss')
	);
}
function updateTimer() {
	$('span#start-time').html(moment(start).format('h:mm:ss a'));
	$('span#end-time').html(moment(end).format('h:mm:ss a'));
	$('span#timer').html(moment(end).subtract(moment(start)).format('HH:mm:ss'));
}

function clearTimer() {
	start = end = timer = null;
	$('span#start-time, span#end-time, span#timer').html('');
	$('#form-timer input').val('');
}

function saveTimer() {
	var data = {
		'start-time': $('#start-time-field').val(),
		'end-time': $('#end-time-field').val(),
		'description': $('#description-field').val(),
	};
	var url = $('#form-timer').attr('action');
	$.ajax({
		url: url,
		data: data,
		type: "POST",
		success: function(data) {
			$('#timesheet-panel').load($('#timesheet-panel').attr('src'));
			clearTimer();
		}
	})
}

function initTimer() {
	$('#timer #end-time-field')
		.datetimepicker({format:'YYYY-MM-DD HH:mm:ss', sideBySide: true})
		.blur(function() { 
			$(this).hide(); 
			$('#end-time').show();
		});
	$('#timer #start-time-field')
		.datetimepicker({format:'YYYY-MM-DD HH:mm:ss', sideBySide: true})
		.blur(function() { 
			$(this).hide(); 
			$('#start-time').show();
		});
	$("#start-time-field").unbind("dp.change").on("dp.change", function (e) {
        start = $(this).val();
        updateTimer();
    });
    $("#end-time-field").unbind("dp.change").on("dp.change", function (e) {
        end = $(this).val();
        updateTimer();
    });
	$('#timer #start-time').unbind('click').click(function() {
		$('#start-time-field').show().focus();
		$(this).hide();
	});
	$('#timer #end-time').unbind('click').click(function() {
		$('#end-time-field').show().focus();
		$(this).hide();
	});
	$('#timer a#start').unbind('click').click(function() { startTimer(); });
	$('#timer a#end').unbind('click').click(function() { stopTimer(); });

	$('#form-timer').unbind('submit').submit(function(e) { e.preventDefault(); saveTimer(); });
}