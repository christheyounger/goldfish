$(function() {
	$('#hinclude').bind("DOMSubtreeModified",function(){
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

function initializeStuff() {
	// Datepicker
	$('.datetimepicker').datetimepicker({format:'DD/MM/YYYY hh:mm'});
	
	// Popups:
	$('a.new').click(function(e) {
		e.preventDefault();
		url = $(this).attr('href');
		$('div#popup').bPopup({loadUrl: url },function() {initializeStuff()});
	});

	/**
     * Edit Forms
     */ 
	// Style an input when focused
	$('form.edit input').focus(function() {
		$(this).addClass('form-control');
	}).blur(function() {
		$(this).removeClass('form-control');
		// Save the form data
		saveForm($(this.form));
	});
	$('form.edit select').change(function(){
		saveForm($(this.form));
	});

	/** Click on a row to edit it **/
    $('table.records_list tbody tr').click(function() {
        if (href = $(this).data('href')) {
        	if ($(this).data('popup'))
        		$('#popup').bPopup({loadUrl: href},function(){initializeStuff()});
        	else
        		window.location.href = href;
        }
    });
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
	$('table.records_list').dataTable().api().ajax.reload();
}