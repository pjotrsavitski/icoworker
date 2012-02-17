$(document).ready(function() {
    /**
	 * Change roles functionality
	 *  o Brings in the form
	 *  o Creates a modal dialog
	 *  o Upon completion or close dialog is being destroyed allong with the form
	 */
	$('.user-role').click(function() {
		var current_user = $(this).find('input[name^="user_role_"]').val();
		$.ajax({
		    cache: false,
			dataType: "html",
			type: "GET",
			url: teke.get_site_url()+"ajax/change_role_form/"+current_user,
			success: function(data) {
			    $(data).dialog({
                    autoOpen: true,
					height: 'auto',
					width: 'auto',
					modal: true,
					buttons: [
					    {
                            text: teke.translate('button_change'),
							click: function() {
						        var current_form = $(this);
                                $.ajax({
                                    cache: false,
						            type: "POST",
							        url: teke.get_site_url()+"actions/change_role.php",
								    data: { role: current_form.find('select[name="role"]').val(), user_id: current_user },
								    dataType: "json",
								    success: function(data) {
								        if (data.state == 0) {
									        current_form.dialog('close');
										    window.location.reload(true);
									    } else {
									        if (data.messages != "") {
									            teke.replace_system_messages(data.messages);
									        }
									    }
								    },
                                    error: function() {}
							    });
						    }
						},
				        {
						    text: teke.translate('button_return'),
						    click: function() {
						        $(this).dialog('close');
						    }
					    }
				    ],
					close: function() {
					    $(this).dialog("destroy");
						$(this).remove();
					}
				});
			},
            error: function() {}
		});
	});
});
