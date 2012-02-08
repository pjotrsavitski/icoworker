$(document).ready(function() {
    $('article.single-project > aside > ul').sortable();
	
	// Message add
	$('#project-diary-and-messages-add > span.teke-add-button').click(function() {

		$(this).prevAll('input[name="body"]').removeClass('ui-state-error');
		if ($(this).prevAll('input[name="body"]').val() != "") {
		    $.ajax({
                cache: false,
				type: "POST",
				url: teke.get_site_url()+"actions/add_message.php",
				data: { project_id: $('#project_id').val(), body: $(this).prevAll('input[name="body"]').val() },
				dataType: "json",
				success: function(data) {
				    if (data.state == 0) {
						$('#project-diary-and-messages-add').children('input[name="body"]').val("");
						$.ajax({
                            cache: false,
							dataType: "html",
							type: "GET",
							url: teke.get_site_url()+"ajax/get_project_activity_flow/"+$('#project_id').val(),
							success: function(data) {
							    $('#project-diary-and-messages-flow').html(data);
							},
                            error: function() {
							    alert("could not bring activity flow");
							}
						});
					} else {
					    $(this).prevAll('input[name="body"]').addClass('ui-state-error');
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
			$(this).prevAll('input[name="body"]').addClass('ui-state-error');
		}
	});

	// Message filter
	$('#project-diary-and-messages-filter > select').on("change", function(e) {
	    // TODO implement me
		// This should update message flow contrents according to filter being selected
		alert($(this).val());
	});

	// Add task functionality
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
					buttons: {
					    "Add": function() {
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
								    // TODO either add tasks to tasks or just bring in new data and replace it
									// XXX refresh for now, need a non-refresh approach
									window.location.reload(true);
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
						},
						"Return": function() {
						    $(this).dialog('close');
						}
					},
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
