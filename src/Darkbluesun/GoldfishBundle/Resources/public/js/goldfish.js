$(function() {
	$('#hinclude, #tasks-panel').bind("DOMSubtreeModified",function(){
  		initializeStuff();
	});
	initializeStuff();
	/**
	 * Lists
	 */

	
	$('table#tasks').dataTable({
		"columns": [
	            { "data": "id", "render": function( data, type, row) { 
	            	href = row['url']; 
	            	return "<a class='edit' href='"+href+"'>"+data+"</a>";
	            	} 
	            },
	            { "data": "client" },
	            { "data": "project" },
	            { "data": "name" },
	            { "data": { _: "due.string", sort: "due.sort"} },
	           ],
	});
	$('table.records_list').dataTable();

	/**
	* Comments functions
	**/
	$('#comment_form').submit(function() {
		var form = $(this);
		var data = form.serialize();
		var url = form.attr('action');
		var textarea = form.find('textarea');

		$.ajax({
			data: data,
			type: "POST",
			url: url,
			success: function(data) {
				if (data.status=='ok') {
					textarea.val('');
				}
			}
		});
		return false;
	});

});



function saveQuickTask() {
	var data = {
		'name': $('#form-quick-task #name-field').val(),
		'due': $('#form-quick-task #due-field').val(),
		'time': $('#form-quick-task #time-field').val(),
	};
	var url = $('#form-quick-task').attr('action');
	$.ajax({
		url: url,
		data: data,
		type: "POST",
		success: function(data) {
			$('#tasks-panel').load($('#tasks-panel').attr('src'));
			clearTimer();
		}
	})
}


function initializeStuff() {
	// Datepicker
	$('.datetimepicker').datetimepicker({format:'DD-MM-YYYY hh:mm'});
	$('.timedatepicker').datetimepicker({format:'DD-MM-YYYY hh:mm'});

	// Popups:
	$('a.new').click(function(e) {
		e.preventDefault();
		url = $(this).attr('href');
		$('div#popup').bPopup({loadUrl: url, contentContainer: '.bContainer' },function() {initializeStuff()});
	});

	/**
     * Edit Forms
     */ 
	// Style an input when focused
	$('form.edit input, form.edit textarea').unbind('focus').focus(function() {
		$(this).addClass('form-control');
	}).unbind('blur').blur(function() {
		$(this).removeClass('form-control');
		// Save the form data
		saveForm($(this.form));
	});
	$('form.edit select').unbind('change').change(function(){
		saveForm($(this.form));
	});

	/** Click on a row to edit it **/
    $('table.records_list tbody tr').unbind('click').click(function() {
        if (href = $(this).data('href')) {
        	if ($(this).data('popup'))
        		$('#popup').bPopup({loadUrl: href},function(){initializeStuff()});
        	else
        		window.location.href = href;
        }
    });

    $('#form-quick-task').unbind('submit').on('submit',function(e) { e.preventDefault(); saveQuickTask(); })

}


/**
 * Submit form via ajax
 */
function saveForm(form) {
	$.ajax({
		type: form.attr('method'),
		url: form.attr('action'),
		data: form.serialize(),
		datatype: 'json',
		success: saveSuccess
	});
}

/**
 * Display a success message for ajax form
 */
function saveSuccess(data) {
	$('.include_200').each(function() { $(this).load($(this).attr('src')); } );
	//$('table.records_list').dataTable().api().ajax.reload();
}