function Timeline() {
	this.start = 0;
	this.end = 0;
	this.pixel_value = 0;
	this.width = 0;
	this.timeline_data = {};
}

Timeline.prototype.setStart = function(value) {
	this.start = value;
};

Timeline.prototype.getStart = function() {
	return this.start;
};

Timeline.prototype.setEnd = function(value) {
	this.end = value;
};

Timeline.prototype.getEnd = function() {
	return this.end;
};

Timeline.prototype.setWidth = function(value) {
	this.width = value;
};

Timeline.prototype.getWidth = function() {
	return this.width;
};

Timeline.prototype.calculatePixesValue = function() {
	this.pixel_value = ( parseInt(this.getEnd()) - parseInt(this.getStart()) ) / this.getWidth();
};

Timeline.prototype.getPixelValue = function() {
	return this.pixel_value;
};

Timeline.prototype.setTimelineData = function(value) {
	this.timeline_data = value;
};

Timeline.prototype.getTimelineData = function() {
	return this.timeline_data;
};

// Create global timeline object
var timeline = new Timeline();

// Event click initialization
teke.reinitialize_milestone_click = function() {
    $('#project-timeline-project .milestone').each(function() {
		$(this).off('click');
		$(this).on('click', function(event) {
		    event.stopPropagation();
		});
    });
};

teke.format_date = function(value, format) {	
	if (format === undefined) {
	    format = "dd.mm.y";
	}
	return $.datepicker.formatDate(format, value);
};

teke.add_milestone_to_timeline = function(offset, milestone_date) {
	$('<div class="milestone" style="left: '+offset+'px;"><img src="'+teke.get_site_url()+'views/graphics/timeline_milestone.png" alt="flag" /><div class="timeline-above-date">'+teke.format_date(new Date(milestone_date), "dd.mm")+'</div></div>'). appendTo($('#project-timeline-project'));
}

teke.add_beginning_end_to_timeline = function() {
    $('<div class="beginning" style="left:0px;"><img src="'+teke.get_site_url()+'views/graphics/black_circle.png" alt="circle" /><div class="timeline-above-date">'+teke.format_date(new Date(timeline.getTimelineData().beginning))+'</div></div>').appendTo($('#project-timeline-project'));
    $('#project-timeline-project .beginning').on('click', function(event) {
	    event.stopPropagation();
	});

    $('<div class="end" style="right:0px;"><img src="'+teke.get_site_url()+'views/graphics/black_circle.png" alt="circle" /><div class="timeline-above-date">'+teke.format_date(new Date(timeline.getTimelineData().end))+'</div></div>').appendTo($('#project-timeline-project'));
	$('#project-timeline-project .end').on('click', function(event) {
	    event.stopPropagation();
	});
};

$(document).ready(function() {
	// Add information to timeline
	// Seconds are transformed into milliseconds as JS Date uses those
	timeline.setStart(parseInt($('#project_start').val()) * 1000);
	timeline.setEnd(parseInt($('#project_end').val()) * 1000);
	timeline.setWidth(600);
	timeline.calculatePixesValue();
	// XXX Width should not be hard coded
	$('#project-timeline-project').width(600);
    // Add now line to the project if applicable
	now_time = new Date().getTime();
	if ( (now_time > timeline.getStart()) && (now_time < timeline.getEnd())) {
		now_offset = (now_time - timeline.getStart()) / timeline.getPixelValue();
	    $('<div class="now" style="left: '+now_offset+'px"></div>').appendTo($('#project-timeline'));
	}
	// Fill timeline with data (XXX THIS SHOULD USE A STANDALONE METHOD)
	$.ajax({
        cache: false,
		type: "POST",
		url: teke.get_site_url()+"actions/get_timeline_data.php",
		data: { project_id : $('#project_id').val() },
		dataType: "json",
		success: function(data) {
		    // Add dat to timeline object
		    timeline.setTimelineData(data);
		    // Add beginning and end
			teke.add_beginning_end_to_timeline();

		    // Add milestones
		    for (var key in data.milestones) {
				teke.add_milestone_to_timeline((new Date(data.milestones[key].milestone_date) - timeline.getStart()) / timeline.getPixelValue(), data.milestones[key].milestone_date);
			}
			teke.reinitialize_milestone_click();
		},
        error: function() {
		    // TODO removeme
			alert("timeline data could not be loaded");
		}
	});

	// Add milestone
	$('#project-timeline-project').on('click', function(event) {
		// TODO see position() method
		offset = parseInt(event.pageX) - parseInt($(this).offset().left);
		// XXX One day seems to be lot from the end
		time = timeline.getStart() + (offset * timeline.getPixelValue());
		time_date = new Date(time);

		// Show the form
		$.ajax({
            cache: false,
			dataType: "html",
			type: "GET",
			url: teke.get_site_url()+"ajax/add_milestone_form",
			success: function(data) {
			    $(data).dialog({
                    autoOpen: true,
					height: 'auto',
					width: 'auto',
					modal: true,
					buttons: [
					    {
						    text: teke.translate('button_create'),
							click: function() {
							    var _this = $(this);
								_this.find('input:text').removeClass('ui-state-error');
								_this.find('.input-datepicker').removeClass('ui-state-error');
								$.ajax({
                                    cache: false,
									type: "POST",
									url: teke.get_site_url()+"actions/add_milestone.php",
									data: { project_id : $('#project_id').val(), title: _this.find('input[name="title"]').val(), url: _this.find('input[name="url"]').val(), milestone_date: _this.find('div[name="milestone_date"]').datepicker("getDate").toUTCString() },
									dataType: "json",
									success: function(data) {
									    if (data.state == 0) {
										    // Add milestone to timeline
											// Recalculate offset, it might have been changed
											offset = (_this.find('div[name="milestone_date"]').datepicker("getDate").getTime() - timeline.getStart()) / timeline.getPixelValue();
											teke.add_milestone_to_timeline(offset, _this.find('div[name="milestone_date"]').datepicker("getDate"));
		                                    teke.reinitialize_milestone_click();
											// Update activity flow if needed
											if ($('#project-diary-and-messages-filter > select').val() != 'messages') {
											    teke.project_update_messages_flow();
											}
											// Close the dialog
											_this.dialog('close');
									    } else {
									        for (var key in data.errors) {
										        _this.find('[name="'+data.errors[key]+'"]').addClass('ui-state-error');
										    }
									    }
										// Add messages if any provided
                                        if (data.messages != "") {
										    teke.replace_system_messages(data.messages);
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
						$(this).find('div[name="milestone_date"]').datepicker({ minDate: new Date(timeline.getStart()), maxDate: new Date(timeline.getEnd()) }).datepicker('setDate', time_date);
					},
					close: function() {
					    $(this).dialog("destroy");
						$(this).remove();
					}
				});
			},
            error: function() {
			    // TODO removeme
			    alert('error occured');
			}
		});
	});
});
