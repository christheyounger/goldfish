$(function() {
	/**
	 * Lists
	 */
	
	/** Click on a row to edit it **/
    $('table.records_list tbody tr').click(function() {
        window.location.href = $(this).find('a.edit').attr('href');
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
	$('.datetimepicker').datetimepicker({format:'DD/MM/YYYY hh:mm'});

});

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
	console.log(data);
}