/* Project timeline class */
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

Timeline.prototype.initializeTimeline = function() {
	// TODO Consider creating the whole timeline block on JS side
    $('<div id="project-timeline-documents"></div>').width(this.getWidth()).appendTo($('#project-timeline'));
	$('<div id="project-timeline-project"></div>').width(this.getWidth()).appendTo($('#project-timeline'));
};

// Create global timeline object
var timeline = new Timeline();

/* Format date (XXX Probably need to get that working without a datepicker; Move to main file in that case) */
teke.format_date = function(value, format) {	
	if (format === undefined) {
	    format = "dd.mm.y";
	}
	return $.datepicker.formatDate(format, value);
};

/* Add milestone to timeline */
teke.add_milestone_to_timeline = function(offset, id, milestone_date, title) {
	$('<div id="project-timeline-milestone-'+id+'" class="milestone" style="left: '+offset+'px;"><img src="'+teke.get_site_url()+'views/graphics/timeline_milestone.png" alt="flag" /><div class="timeline-above-date">'+teke.format_date(milestone_date, "dd.mm")+'</div><div class="teke-tooltip-content"><label>'+title+'</label><br />'+teke.format_date(milestone_date)+'</div></div>'). appendTo($('#project-timeline-project'));
    // Bind click
    $('#project-timeline-milestone-'+id).on('click', function(event) {
        // Prevent parent click from happening
        event.stopPropagation();
    });
    // Add tooltip
    $('#project-timeline-milestone-'+id+' img').qtip({
        content: {
            text: function(api) {
                return $(this).parent().find('.teke-tooltip-content').html();
            }
        },
        position: {
            my: "bottom center",
            at: "top center"
        },
        show: {
            event: 'mouseenter'
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

/* Add document to timeline */
teke.add_document_to_timeline = function(id, created, title, url) {
    offset = (created.getTime() - timeline.getStart()) / timeline.getPixelValue();
    now_time = new Date().getTime();
    if ( (now_time > timeline.getStart()) && (now_time < timeline.getEnd())) {
        width = (now_time - created.getTime()) / timeline.getPixelValue();
    } else {
        width = (timeline.getEnd() - created.getTime()) / timeline.getPixelValue();
    }
    $('<div id="project-timeline-document-'+id+'" class="timeline-document" style="left:'+offset+'px;"><img src="'+teke.get_site_url()+'views/graphics/timeline_document.png" alt="document" /><div class="teke-tooltip-content"><label>'+( (url == '') ? title : '<a href="'+url+'" target="_blank">'+title+'</a>' )+'</label><br />'+teke.format_date(created)+'</div></div>').width(width).appendTo('#project-timeline-documents');
    // Add tooltip
    $('#project-timeline-document-'+id+' img').qtip({
        content: {
            text: function(api) {
                return $(this).parent().find('.teke-tooltip-content').html();
            }
        },
        position: {
            my: "bottom center",
            at: "top center"
        },
        show: {
            event: 'mouseenter'
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

/* Add beginning and end pointo to timeline */
teke.add_beginning_end_to_timeline = function() {
    $('<div class="beginning" style="left:0px;"><img src="'+teke.get_site_url()+'views/graphics/grey_circle.png" alt="circle" /><div class="timeline-above-date">'+teke.format_date(new Date(timeline.getTimelineData().beginning))+'</div></div>').appendTo($('#project-timeline-project'));
    $('#project-timeline-project .beginning').on('click', function(event) {
		// Prevent parent click from happening
	    event.stopPropagation();
	});

    $('<div class="end" style="right:0px;"><img src="'+teke.get_site_url()+'views/graphics/grey_circle.png" alt="circle" /><div class="timeline-above-date">'+teke.format_date(new Date(timeline.getTimelineData().end))+'</div></div>').appendTo($('#project-timeline-project'));
	$('#project-timeline-project .end').on('click', function(event) {
		// Prevent parent click from happening
	    event.stopPropagation();
	});
};

/* Initialize timeline related stuff (XXX Some portion should probably be moved to standalone methods onto Timeline class) */
$(document).ready(function() {
	// Add information to timeline
	// Seconds are transformed into milliseconds as JS Date uses those
	timeline.setStart(parseInt($('#project_start').val()) * 1000);
	timeline.setEnd(parseInt($('#project_end').val()) * 1000);
	timeline.setWidth(600);
	timeline.calculatePixesValue();
	timeline.initializeTimeline();
    // Add now line to the project if applicable
	now_time = new Date().getTime();
	if ( (now_time > timeline.getStart()) && (now_time < timeline.getEnd())) {
		now_offset = (now_time - timeline.getStart()) / timeline.getPixelValue();
        // XXX Compensate for padding, a better solution is needed
        now_offset = now_offset + parseInt($('#project-timeline').css('padding-left'), 10);
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
				teke.add_milestone_to_timeline((new Date(data.milestones[key].milestone_date) - timeline.getStart()) / timeline.getPixelValue(), data.milestones[key].id, new Date(data.milestones[key].milestone_date), data.milestones[key].title);
			}
            // Add documents
            for (var key in data.documents) {
                teke.add_document_to_timeline(data.documents[key].id, new Date(data.documents[key].created), data.documents[key].title, data.documents[key].url);
            }
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
											teke.add_milestone_to_timeline(offset, data.data.id, _this.find('div[name="milestone_date"]').datepicker("getDate"), data.data.title);
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

	// Add document
	$('#add-document-button').on('click', function(event) {
        $.ajax({
            cache: false,
            dataType: "html",
            type: "GET",
            url: teke.get_site_url()+"ajax/add_document_form",
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
                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    url: teke.get_site_url()+"actions/add_document.php",
                                    data: { project_id: $('#project_id').val(), title: _this.find('input[name="title"]').val(), url: _this.find('input[name="url"]').val() },
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.state == 0) {
                                            // Add document to timeline
                                            teke.add_document_to_timeline(data.data.id, new Date(data.data.created), data.data.title, data.data.url);
                                            // Update activity flow if needed
                                             if ($('#project-diary-and-messages-filter > select').val() != 'messages') {
                                                 teke.project_update_messages_flow();
                                                 // Close the dialog
                                                 _this.dialog('close');
                                             }
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
                                        alert('error occured');
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
                        // TODO Remove me if I am not needed
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