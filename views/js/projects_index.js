$(document).ready(function () {
    $('#add-new-project').click(function(e) {
		e.preventDefault();
		$.ajax({
            cache: false,
			type: "GET",
			dataType: "html",
			url: teke.get_site_url()+"ajax/add_project_form",
			success: function(data) {
			    $(data).dialog({
                    autoOpen: true,
					height: 'auto',
					width: 'auto',
					modal: true,
					buttons: {
					    "Add": function() {
                            $(this).find('input:text').removeClass('ui-state-error');
                            $(this).find('textarea').removeClass('ui-state-error');
						    $(this).find('.input_datepicker').removeClass('ui-state-error');
						    var current_form = $(this);
						    $.ajax({
                                cache: false,
								type: "POST",
								url: teke.get_site_url()+"actions/create_project.php",
								data: {'title': current_form.find('input[name="title"]').val(), 'goal': current_form.find('textarea[name="goal"]').val(), 'start_date': $('#start_date').datepicker("getDate").toUTCString(), 'end_date': $('#end_date').datepicker('getDate').toUTCString()},
								dataType: "json",
								success: function(data) {
								    if (data.state == 0) {
									    if (data.forward != "") {
										    window.location = data.forward;
										}
										if (data.messages != "") {
										    teke.replace_system_messages(data.messages);
										}
										// Dialog is not being closed, as we will be forwarded
									} else {
									    for (var key in data.errors) {
										    $('#'+data.errors[key]).addClass('ui-state-error');
										}
										if (data.messages != "") {
										    teke.replace_system_messages(data.messages);
										}
									}
								},
                                error: function() {
								    // TODO removeme 
								    alert("error occured");
								}
							});
						},
						"Return": function() {
						    $(this).dialog('close');
						}
					},
                    open: function() {
                        // Force dates to depend on each other
                        var dates = $('#start_date, #end_date').datepicker({
                            onSelect: function( selectedDate ) {
                                var option = this.id == "start_date" ? "minDate" : "maxDate",
                                instance = $(this).data("datepicker"),
                                date = $.datepicker.parseDate(
                                    instance.settings.dateFormat ||
                                    $.datepicker._defaults.dateFormat,
                                    selectedDate, instance.settings
                                );
                                dates.not(this).datepicker("option", option, date);
                            }
                        });
					},
                    close: function() {
					    $(this).dialog('destroy');
						$(this).remove();
					}
				});
			},
            error: function() {
				// TODO removeme
			    alert("error occured");
			}
	    });
	});
});
