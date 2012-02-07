$(document).ready(function() {
    $('article.single-project > aside > ul').sortable();

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
			    alert("error occured");
			}
		});
	});
});
