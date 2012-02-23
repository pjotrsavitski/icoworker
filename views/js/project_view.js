// Add tooltip initialization to teke object
teke.project_initialize_tooltips = function() {
	$('.teke-tooltip').qtip({
	    content: {
		    text: function(api) {
			    return $(this).next('.teke-tooltip-content').html();
			}
		},
        position: {
		    my: "bottom center",
			at: "top center"
		},
        show: {
		    event: 'click'
	    },
        hide: {
		    delay: 500,
			fixed: true
		},
        style: {
		    classes: 'ui-tooltip-light ui-tooltip-shadow ui-tooltip-rounded'
		}
	});
};

// Updates message flow, respects filter
teke.project_update_messages_flow = function() {
	$.ajax({
        cache: false,
        dataType: "html",
        type: "GET",
        url: teke.get_site_url()+"ajax/get_project_activity_flow/"+$('#project_id').val()+"/"+$('#project-diary-and-messages-filter > select').val(),
        success: function(data) {
            $('#project-diary-and-messages-flow').html(data);
		},
        error: function() {
            // TODO removeme
            alert("could not bring activity flow");
        }
    });
};

// Update participants
teke.update_project_participants = function() {
    $.ajax({
        cache: false,
        dataType: "html",
        type: "GET",
        url: teke.get_site_url()+"ajax/get_project_participants/"+$('#project_id').val(),
        success: function(data) {
            $('#project-participants').html(data);
		},
        error: function() {
            // TODO removeme
            alert("could not bring participants");
        }
    });
};

// Add new message
teke.add_new_diary_message = function() {
    $('#project-diary-and-messages-add').find('input[name="body"]').removeClass('ui-state-error');
    if ($('#project-diary-and-messages-add').find('input[name="body"]').val() != "") {
        $.ajax({
            cache: false,
            type: "POST",
            url: teke.get_site_url()+"actions/add_message.php",
            data: { project_id: $('#project_id').val(), body: $('#project-diary-and-messages-add').find('input[name="body"]').val() },
            dataType: "json",
            success: function(data) {
                if (data.state == 0) {
                    $('#project-diary-and-messages-add').children('input[name="body"]').val("");
					// Check if there is a reason to update the flow
					if ($('#project-diary-and-messages-filter > select').val() != 'activities') {
				        // Update message flow
						teke.project_update_messages_flow();
					}
				} else {
					$('#project-diary-and-messages-add').find('input[name="body"]').addClass('ui-state-error');
				}
                if (data.messages != "") {
					teke.replace_system_messages(data.messages);
				}
			},
            error: function() {
				// TODO removeme
				alert("add_message failed");
			}
		});
	} else {
		$('#project-diary-and-messages-add').find('input[name="body"]').addClass('ui-state-error');
	}
};

$(document).ready(function() {
	// Initialize tooltips
	teke.project_initialize_tooltips();
	/**
	 * Make aside widgets draggable (use <legend> as handle)
	 */
    $('div#single-project-content > div#aside > ul').sortable({ handle: 'legend' });

    /**
	 * Messages add (enter key pressed)
	 *  o Adds a message (if not empty)
	 *  o In case of success (message flow is reloaded); respects filter being chosen
	 */
    $('#project-diary-and-messages-add > input[name="body"]').keypress(function(event) {
	    if ((event.keyCode ? event.keyCode : event.which) == 13) {
		    teke.add_new_diary_message();
		}
	});
	
	/**
	 * Messages add (button clicked)
	 *  o Adds a message (if not empty)
	 *  o In case of success (message flow is reloaded); respects filter being chosen
	 */
	$('#project-diary-and-messages-add > span.teke-add-button').click(function() {
		teke.add_new_diary_message();
	});

	/**
	 * Message filter
	 *  o Refreshes message flow (if selected filter is changed)
	 */
	$('#project-diary-and-messages-filter > select').on("change", function(e) {
		$.ajax({
            cache: false,
			dataType: "html",
			type: "GET",
			url: teke.get_site_url()+"ajax/get_project_activity_flow/"+$('#project_id').val()+"/"+$(this).val(),
			success: function(data) {
			    $('#project-diary-and-messages-flow').html(data);
			},
            error: function() {
			    // TODO removeme
			    alert("could not bring activity flow");
			}
		});
	});

	/**
	 * Add task functionality
	 *  o Brings in the form
	 *  o Creates a modal dialog
	 *  o Refreshes tasks on success
	 *  o Upon completion or close dialog is being destroyed allong with the form
	 */
	$('#add-task-button').click(function() {
		$.ajax({
		    cache: false,
			dataType: "html",
			type: "GET",
			url: teke.get_site_url()+"ajax/add_task_form",
			success: function(data) {
			    $(data).dialog({
                    autoOpen: true,
					height: 'auto',
					width: 'auto',
					modal: true,
					buttons: [
					    {
					        text: teke.translate('button_add'),
						    click: function() {
						        var current_form = $(this);
						        current_form.find('input:text').removeClass('ui-state-error');
						        $.ajax({
                                    cache: false,
							        type: "POST",
							        url: teke.get_site_url()+"actions/add_task.php",
							        data: { project_id: $('#project_id').val(), title: current_form.find('input[name="title"]').val(), description: current_form.find('input[name="description"]').val() },
							        dataType: "json",
							        success: function(data) {
							            if (data.state == 0) {
                                            $.ajax({
                                                cache: false,
							                    dataType: "html",
							                    type: "GET",
							                    url: teke.get_site_url()+"ajax/get_project_tasks/"+$('#project_id').val(),
							                    success: function(data) {
							                        $('#project-tasks').html(data);
													if ($('#project-diary-and-messages-filter > select').val() != 'messages') {
											            // Update activity flow
											            teke.project_update_messages_flow();
													}
							                    },
                                                error: function() {
										            // TODO removeme
							                        alert("could not bring tasks");
							                   }
						                    });
								            current_form.dialog('close');
								        } else {
								            for (var key in data.errors) {
									            $('#'+data.errors[key]).addClass('ui-state-error');
									        }
								        }
								        if (data.messages != "") {
								            teke.replace_system_messages(data.messages);
								        }
							        },
                                    error: function() {
							            // TODO removeme
							            alert("add_task failed");
							        }
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
            error: function() {
			    // TODO removeme
			    alert("error occured");
			}
		});
	});

    /**
	 * Add resource functionality
	 *  o Brings in the form
	 *  o Creates a moda dialog
	 *  o Refreshes resources on success
	 *  o Upon completion or close dialog is being destroyed allong with the form
	 */
	$('#add-resource-button').click(function() {
		$.ajax({
		    cache: false,
			dataType: "html",
			type: "GET",
			url: teke.get_site_url()+"ajax/add_resource_form",
			success: function(data) {
			    $(data).dialog({
                    autoOpen: true,
					height: 'auto',
					width: 'auto',
					modal: true,
					buttons: [
					    {
					        text: teke.translate('button_add'),
						    click: function() {
						        var current_form = $(this);
						        current_form.find('input:text').removeClass('ui-state-error');
						        $.ajax({
                                    cache: false,
							        type: "POST",
							        url: teke.get_site_url()+"actions/add_resource.php",
							        data: { project_id: $('#project_id').val(), title: current_form.find('input[name="title"]').val(), description: current_form.find('input[name="description"]').val(), url: current_form.find('input[name="url"]').val(), resource_type: current_form.find('select[name="resource_type"]').val() },
							        dataType: "json",
							        success: function(data) {
							            if (data.state == 0) {
                                            $.ajax({
                                                cache: false,
							                    dataType: "html",
							                    type: "GET",
							                    url: teke.get_site_url()+"ajax/get_project_resources/"+$('#project_id').val(),
							                    success: function(data) {
							                        $('#project-resources').html(data);
											        // Reinitialize tooltips
											        teke.project_initialize_tooltips();
													if ($('#project-diary-and-messages-filter > select').val() != 'messages') {
											            // Update activity flow
										                teke.project_update_messages_flow();
													}
							                    },
                                                error: function() {
										            // TODO removeme
							                        alert("could not bring resources");
							                    }
						                    });
								            current_form.dialog('close');
								        } else {
								            for (var key in data.errors) {
									            $('#'+data.errors[key]).addClass('ui-state-error');
									        }
								        }
								        if (data.messages != "") {
								            teke.replace_system_messages(data.messages);
								        }
							        },
                                    error: function() {
							            // TODO removeme
							            alert("add_resource failed");
							        }
						        });
						        }
						},
				        {
						    text: teke.translate('button_return'),
						    click:  function() {
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
            error: function() {
			    // TODO removeme
			    alert("error occured");
			}
		});
	});

    /**
	 * Add participants functionality
	 *  o Brings in the form
	 *  o Creates a modal dialog
	 *  o Refreshes participants on success
	 *  o Upon completion or close dialog is being destroyed allong with the form
	 */
	$('#add-participant-button').click(function() {
		$.ajax({
		    cache: false,
			dataType: "html",
			type: "GET",
			url: teke.get_site_url()+"ajax/add_participant_form",
			success: function(data) {
			    $(data).dialog({
                    autoOpen: true,
					height: 'auto',
					width: 'auto',
					modal: true,
					buttons: [
					    {
					        text: teke.translate('button_search'),
							click: function() {
						        var current_form = $(this);
						        current_form.find('input:text').removeClass('ui-state-error');
							    if (current_form.find('input[name="criteria"]').val() != "") {
							        $.ajax({
								        cache: false,
									    dataType: "html",
									    type: "GET",
									    url: teke.get_site_url()+"ajax/search_for_participants/"+$('#project_id').val()+"/"+current_form.find('input[name="criteria"]').val(),
									    success: function(data) {
									        current_form.find('[name="search_results"]').html(data);
										    current_form.find('.single-participant-result').click(function() {
											    $.ajax({
                                                    cache: false,
												    type: "POST",
												    url: teke.get_site_url()+"actions/add_participant.php",
												    data: { project_id: $('#project_id').val(), user_id: $(this).find('input[name^="single_participant_"]').val() },
												    dataType: "json",
												    success: function(data) {
												        if (data.state == 0) {
													        teke.update_project_participants();
															if ($('#project-diary-and-messages-filter > select').val() != 'messages') {
                                                                // Update activity flow
										                        teke.project_update_messages_flow();
															}
													        current_form.dialog('close');
													    }
													    if (data.messages != "") {
													        teke.replace_system_messages(data.messages);
												        }
												    },
                                                    error: function() {
												        // TODO removeme
													    alert('add_participant failed');
												    }
											    });
										    });
									    },
                                        error: function() {
									        // TODO removeme
										    alert("could not bring participants");
									    }
								    });
							    } else {
							        current_form.find('input[name="criteria"]').addClass('ui-state-error');
							    }
						    }
						},
				        {
						    text: teke.translate('button_return'),
						    click:  function() {
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
            error: function() {
			    // TODO removeme
			    alert("error occured");
			}
		});
	});


});
