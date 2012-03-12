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
    $('<div id="project-timeline-project-comments" title="'+teke.translate('text_click_to_add_comment')+'"></div>').width(this.getWidth()).appendTo($('#project-timeline'));
};

// Create global timeline object
var timeline = new Timeline();

/* Extend teke with additional methods */

/* Add milestone to timeline */
teke.add_milestone_to_timeline = function(offset, id, milestone_date, title, flag_url, notes) {
	$('<div id="project-timeline-milestone-'+id+'" class="milestone" style="left: '+offset+'px;"><img src="'+flag_url+'" alt="flag" /><div class="timeline-above-date">'+teke.format_date(milestone_date, "dd.mm")+'</div><div class="teke-tooltip-content"><label>'+title+'</label><br />'+teke.format_date(milestone_date)+'<div class="milestone-notes">'+notes+'</div></div></div>'). appendTo($('#project-timeline-project'));
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

/* Add comment to timeline */
teke.add_comment_to_timeline = function(offset, id, comment_date, content) {
	$('<div id="project-timeline-comment-'+id+'" class="project-comment" style="left: '+offset+'px;"><img src="'+teke.get_site_url()+'views/graphics/timeline_comment.png" alt="comment" /><div class="teke-tooltip-content"><label>'+content+'</label><br />'+teke.format_date(comment_date)+'</div></div>'). appendTo($('#project-timeline-project-comments'));
    // Bind click
    $('#project-timeline-comment-'+id).on('click', function(event) {
        // Prevent parent click from happening
        event.stopPropagation();
    });
    // Add tooltip
    $('#project-timeline-comment-'+id+' img').qtip({
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

/* Remove all versions of a specific document from timeline */
teke.remove_document_versions = function(id) {
    $('#project-timeline-document-'+id).find('[id^="project-timeline-document-version-"]').remove();
};

/* Add a version to a document on timeline */
teke.add_document_version_to_document = function(document_id, document_created, version) {
    $('<div id="project-timeline-document-version-'+version.id+'" class="timeline-document-version" style="left:'+( ( (new Date(version.created).getTime() - document_created.getTime()) / timeline.getPixelValue() ) - 2 )+'px;"><img src="'+teke.get_site_url()+'views/graphics/timeline_document.png" alt="document" /><div class="teke-tooltip-content"><label>'+( (version.url == '') ? version.title : '<a href="'+version.url+'" target="_blank">'+version.title+'</a>' )+'</label><br />'+teke.format_date(new Date(version.created))+'<div class="document-version-note">'+version.notes+'</div></div></div>').appendTo('#project-timeline-document-'+document_id);
    // Add tooltip
    $('#project-timeline-document-version-'+version.id+' img').qtip({
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
teke.add_document_to_timeline = function(id, created, title, url, notes, versions) {
    offset = (created.getTime() - timeline.getStart()) / timeline.getPixelValue();
    now_time = new Date().getTime();
    if ( (now_time > timeline.getStart()) && (now_time < timeline.getEnd())) {
        width = (now_time - created.getTime()) / timeline.getPixelValue();
    } else {
        width = (timeline.getEnd() - created.getTime()) / timeline.getPixelValue();
    }
    $('<div id="project-timeline-document-'+id+'" class="timeline-document" style="left:'+offset+'px;"></div>').width(width).appendTo('#project-timeline-documents');
    // Add click event
    $('#project-timeline-document-'+id).on('click', function() {
        teke.add_new_document_version(id);
    });
    // Add versions
    for (var key in versions) {
        teke.add_document_version_to_document(id, created, versions[key]);
    }
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

/*
 * Add new versin to a document, update document on timeline
 * @param int id Document identifier
 */
teke.add_new_document_version = function(id) {
    $.ajax({
        cache: false,
        dataType: "html",
        type: "GET",
        url: teke.get_site_url()+"ajax/add_document_version_form",
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
                            _this.find('.ui-state-error').removeClass('ui-state-error');
                            $.ajax({
                                cache: false,
                                type: "POST",
                                url: teke.get_site_url()+"actions/add_document_version.php",
                                data: { project_id: $('#project_id').val(), document_id: id, title: _this.find('input[name="title"]').val(), url: _this.find('input[name="url"]').val(), notes: _this.find('textarea[name="notes"]').val() },
                                dataType: "json",
                                success: function(data) {
                                    if (data.state == 0) {
                                        // Add versions
                                        teke.remove_document_versions(data.data.id);
                                        for (var key in data.data.versions) {
                                            teke.add_document_version_to_document(data.data.id, new Date(data.data.created), data.data.versions[key]);
                                        }

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
};

/* Initialize timeline related stuff (XXX Some portion should probably be moved to standalone methods onto Timeline class) */
$(document).ready(function() {
	// Add information to timeline
	// Seconds are transformed into milliseconds as JS Date uses those
	timeline.setStart(parseInt($('#project_start').val()) * 1000);
	timeline.setEnd(parseInt($('#project_end').val()) * 1000);
	timeline.setWidth(600);
    // XXX CHNAGEME START
    tcase = 'farthest';
    if (tcase == 'closest') {
        timeline.setWidth(parseInt((timeline.getEnd() - timeline.getStart()) / (86400000)) * 50);
        //timeline.setWidth( parseInt((timeline.getEnd() - timeline.getStart()) / (3600 * 1000)) );
    } else {
        timeline.setWidth(600);
    }
    // XXX CHANGEME END
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
                // XXX Possibly .getTime() needs to be used
				teke.add_milestone_to_timeline((new Date(data.milestones[key].milestone_date) - timeline.getStart()) / timeline.getPixelValue(), data.milestones[key].id, new Date(data.milestones[key].milestone_date), data.milestones[key].title, data.milestones[key].flag_url, data.milestones[key].notes);
			}
            // Add documents
            for (var key in data.documents) {
                teke.add_document_to_timeline(data.documents[key].id, new Date(data.documents[key].created), data.documents[key].title, data.documents[key].url, data.documents[key].notes, data.documents[key].versions);
            }
            // Add comments
            for (var key in data.comments) {
                // XXX Possibly .getTime() needs to be used
                teke.add_comment_to_timeline((new Date(data.comments[key].comment_date) - timeline.getStart()) / timeline.getPixelValue(), data.comments[key].id, new Date(data.comments[key].comment_date), data.comments[key].content);
            }
		},
        error: function() {
		    // TODO removeme
			alert("timeline data could not be loaded");
		}
	});

	// Add milestone when projct timeline is clicked
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
                                _this.find('.ui-state-error').removeClass('ui-state-error');
								$.ajax({
                                    cache: false,
									type: "POST",
									url: teke.get_site_url()+"actions/add_milestone.php",
									data: { project_id : $('#project_id').val(), title: _this.find('input[name="title"]').val(), url: _this.find('input[name="url"]').val(), milestone_date: _this.find('div[name="milestone_date"]').datepicker("getDate").toUTCString(), flag_color: _this.find('select[name="flag_color"]').val(), notes: _this.find('textarea[name="notes"]').val() },
									dataType: "json",
									success: function(data) {
									    if (data.state == 0) {
										    // Add milestone to timeline
											// Recalculate offset, it might have been changed
											offset = (_this.find('div[name="milestone_date"]').datepicker("getDate").getTime() - timeline.getStart()) / timeline.getPixelValue();
											teke.add_milestone_to_timeline(offset, data.data.id, _this.find('div[name="milestone_date"]').datepicker("getDate"), data.data.title, data.data.flag_url, data.data.notes);
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

    // Add comment when project-comments timeline is clicked
	$('#project-timeline-project-comments').on('click', function(event) {
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
			url: teke.get_site_url()+"ajax/add_project_comment_form",
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
								_this.find('.ui-state-error').removeClass('ui-state-error');
								$.ajax({
                                    cache: false,
									type: "POST",
									url: teke.get_site_url()+"actions/add_project_comment.php",
									data: { project_id : $('#project_id').val(), content: _this.find('textarea[name="content"]').val(), comment_date: _this.find('div[name="comment_date"]').datepicker("getDate").toUTCString() },
									dataType: "json",
									success: function(data) {
									    if (data.state == 0) {
										    // Add project comment to timeline
											// Recalculate offset, it might have been changed
											offset = (_this.find('div[name="comment_date"]').datepicker("getDate").getTime() - timeline.getStart()) / timeline.getPixelValue();
											teke.add_comment_to_timeline(offset, data.data.id, _this.find('div[name="comment_date"]').datepicker("getDate"), data.data.content);
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
						$(this).find('div[name="comment_date"]').datepicker({ minDate: new Date(timeline.getStart()), maxDate: new Date(timeline.getEnd()) }).datepicker('setDate', time_date);
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


	// Add document when add button is clicked
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
                                _this.find('.ui-state-error').removeClass('ui-state-error');
                                $.ajax({
                                    cache: false,
                                    type: "POST",
                                    url: teke.get_site_url()+"actions/add_document.php",
                                    data: { project_id: $('#project_id').val(), title: _this.find('input[name="title"]').val(), url: _this.find('input[name="url"]').val(), notes: _this.find('textarea[name="notes"]').val() },
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.state == 0) {
                                            // Add document to timeline
                                            teke.add_document_to_timeline(data.data.id, new Date(data.data.created), data.data.title, data.data.url, data.data.notes, data.data.versions);
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
