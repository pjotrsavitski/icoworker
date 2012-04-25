teke.place_timelines = function() {
	if ($('#all-projects .project-timeline').length > 0) {
	    var places_timelines = {
            beginning: 0,
		    end: 0,
			width: 0,
			pixel_value: 0,
			projects: {},
			setBeginning: function(value) {
				this.beginning = value;
			},
            getBeginning: function() {
			    return this.beginning;
			},
			setEnd: function(value) {
			    this.end = value;
			},
			getEnd: function() {
			    return this.end;
			},
			setWith: function(value) {
			    this.width = value;
			},
            getWidth: function() {
				return this.width;
			},
            calculatePixelValue: function() {
			    this.pixel_value = (this.getEnd() - this.getBeginning()) / this.getWidth();
			},
		    getPixelValue: function() {
				return this.pixel_value;
			},
			addProject: function(index, beginning, end, id) {
			    this.projects[index] = { beginning: beginning, end: end, duration: (end - beginning), id: id};
			},
			getProject: function(index) {
			    if (index in this.projects) {
					return this.projects[index];
				}
				return false;
			}
		};

        $('#all-projects .project-timeline').each(function(index) {
	        if ($(this).attr('data-project-start-date') < places_timelines.getBeginning()) {
		        places_timelines.setBeginning($(this).attr('data-project-start-date'));
		    } else if (places_timelines.getBeginning() == 0) {
			    places_timelines.setBeginning($(this).attr('data-project-start-date'));
		    }
		    if ($(this).attr('data-project-end-date') > places_timelines.getEnd()) {
		        places_timelines.setEnd($(this).attr('data-project-end-date'));
		    }
			places_timelines.addProject(index, $(this).attr('data-project-start-date'), $(this).attr('data-project-end-date'), $(this).attr('data-id'));
	    });

		if (places_timelines.getBeginning() > 0 && places_timelines.getEnd() > 0) {
			places_timelines.setWith($('.project-timeline').width());
			places_timelines.calculatePixelValue();

		$('#all-projects .project-timeline').each(function(index) {
			var project = places_timelines.getProject(index);
			if (project) {
			    $('<div class="timeline" style="position:relative; left: '+( (project.beginning - places_timelines.getBeginning()) / places_timelines.getPixelValue() )+'px; width: '+( project.duration / places_timelines.getPixelValue() )+'px"></div>').appendTo($(this)).on('click', function() {
                    window.location = teke.get_site_url()+"project/view/"+project.id;
                });
			}
		});

		}

	}
};

$(document).ready(function () {
    teke.place_timelines();

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
					buttons: [
					    {
					        text: teke.translate('button_add'),
							click: function() {
                                $(this).find('input:text').removeClass('ui-state-error');
                                $(this).find('textarea').removeClass('ui-state-error');
						        $(this).find('.input-datepicker').removeClass('ui-state-error');
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
						    }
						},
				        {
                            text: teke.translate('button_return'),
						    click: function() {
						        $(this).dialog('close');
						    }
					    }
				    ],
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
