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
    $('<div id="project-timeline-tasks"></div>').width(this.getWidth()).appendTo($('#project-timeline'));
    $('<div id="project-timeline-resources"></div>').width(this.getWidth()).appendTo($('#project-timeline'));
    // Initialize project-timeline-tasks droppable
    teke.initialize_tasks_timeline_droppable();
};

// Create global timeline object
var timeline = new Timeline();

/* Extend teke with additional methods */

/* Add milestone to timeline */
teke.add_milestone_to_timeline = function(offset, id, milestone_date, title, flag_url, notes) {
	$('<div id="project-timeline-milestone-'+id+'" class="milestone" style="left: '+offset+'px;"><img src="'+flag_url+'" alt="flag" /><div class="timeline-above-date">'+teke.format_date(milestone_date, "dd.mm")+'</div><div class="teke-tooltip-content"><label>'+title+'</label><br />'+teke.format_date(milestone_date)+'<div class="milestone-notes">'+notes+'</div></div></div>').appendTo($('#project-timeline-project'));
    // Bind click
    $('#project-timeline-milestone-'+id).on('click', function(event) {
        // Prevent parent click from happening
        event.stopPropagation();
        teke.edit_milestone(id);
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

/* Edit milestone */
teke.edit_milestone = function(id) {
    // Show the form
    $.ajax({
        cache: false,
        dataType: "html",
        type: "GET",
        url: teke.get_site_url()+"ajax/edit_milestone_form/"+id,
        success: function(data) {
            $(data).dialog({
                autoOpen: true,
                height: 'auto',
                width: 'auto',
                modal: true,
                buttons: [
                    {
                        text: teke.translate('button_edit'),
                        click: function() {
                            var _this = $(this);
                            _this.find('.ui-state-error').removeClass('ui-state-error');
                            $.ajax({
                                cache: false,
                                type: "POST",
                                url: teke.get_site_url()+"actions/edit_milestone.php",
                                data: { milestone_id: _this.find('input[name="id"]').val(), title: _this.find('input[name="title"]').val(), url: _this.find('input[name="url"]').val(), milestone_date: _this.find('input[name="milestone_date"]').datepicker("getDate").toUTCString(), flag_color: _this.find('select[name="flag_color"]').val(), notes: _this.find('textarea[name="notes"]').val() },
                                dataType: "json",
                                success: function(data) {
                                    if (data.state == 0) {
                                        // Replace milestone on timeline
                                        // Recalculate offset, it might have been changed
                                        offset = (_this.find('input[name="milestone_date"]').datepicker("getDate").getTime() - timeline.getStart()) / timeline.getPixelValue();
                                        // Remove old milestone object
                                        $('#project-timeline-milestone-'+id).remove();
                                        // Add new one instead
                                        teke.add_milestone_to_timeline(offset, data.data.id, _this.find('input[name="milestone_date"]').datepicker("getDate"), data.data.title, data.data.flag_url, data.data.notes);
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
                    $(this).find('input[name="milestone_date"]').datepicker({ minDate: new Date(timeline.getStart()), maxDate: new Date(timeline.getEnd()), dateFormat: 'dd.mm.yy' }).datepicker('setDate', new Date($(this).find('input[name="milestone_date"]').val()));
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
};

/* Add comment to timeline */
teke.add_comment_to_timeline = function(offset, id, comment_date, content) {
	$('<div id="project-timeline-comment-'+id+'" class="project-comment" style="left: '+offset+'px;"><img src="'+teke.get_site_url()+'views/graphics/timeline_comment.png" alt="comment" /><div class="teke-tooltip-content"><label>'+content+'</label><br />'+teke.format_date(comment_date)+'</div></div>'). appendTo($('#project-timeline-project-comments'));
    // Bind click
    $('#project-timeline-comment-'+id).on('click', function(event) {
        // Prevent parent click from happening
        event.stopPropagation();
        teke.edit_comment(id);
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

/* Edit comment */
teke.edit_comment = function(id) {
    // Show the form
    $.ajax({
        cache: false,
        dataType: "html",
        type: "GET",
        url: teke.get_site_url()+"ajax/edit_project_comment_form/"+id,
        success: function(data) {
            $(data).dialog({
                autoOpen: true,
                height: 'auto',
                width: 'auto',
                modal: true,
                buttons: [
                    {
                        text: teke.translate('button_edit'),
                        click: function() {
                            var _this = $(this);
                            _this.find('.ui-state-error').removeClass('ui-state-error');
                            $.ajax({
                                cache: false,
                                type: "POST",
                                url: teke.get_site_url()+"actions/edit_project_comment.php",
                                data: { comment_id: _this.find('input[name="id"]').val(), content: _this.find('textarea[name="content"]').val(), comment_date: _this.find('input[name="comment_date"]').datepicker("getDate").toUTCString() },
                                dataType: "json",
                                success: function(data) {
                                    if (data.state == 0) {
                                        // Replace comment on timeline
                                        // Recalculate offset, it might have been changed
                                        offset = (_this.find('input[name="comment_date"]').datepicker("getDate").getTime() - timeline.getStart()) / timeline.getPixelValue();
                                        // Remove old comment object
                                        $('#project-timeline-comment-'+id).remove();
                                        // Add new one instead
                                        teke.add_comment_to_timeline(offset, data.data.id, _this.find('input[name="comment_date"]').datepicker("getDate"), data.data.content);
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
                    $(this).find('input[name="comment_date"]').datepicker({ minDate: new Date(timeline.getStart()), maxDate: new Date(timeline.getEnd()), dateFormat: 'dd.mm.yy' }).datepicker('setDate', new Date($(this).find('input[name="comment_date"]').val()));
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

/**
 * Add task to timeline
 * Full data is provided, metod is calculationg and doing all the needed initializations
 */
teke.add_task_to_timeline = function(data) {
    /**
     * 1. Data includes
     *   * title
     *   * description
     *   * start_date
     *   * end_date
     *   * members
     *   * resources
     * 2. Register droppable
     * 3. Register togglable 
     */
    tmp_task = $('<div id="" class="single-task ui-corner-all" data-id=""><span class="task-title" title=""></span><span class="teke-toggler teke-slide-toggler project-side-widget-control"></span><div class="teke-togglable"><div class="task-members"></div><div class="task-resources"></div></div></div>');
    tmp_task.attr('id', 'project-timeline-task-'+data.id).attr('data-id', data.id);
    tmp_task.find('.task-title').html(data.title).attr('title', data.description);
    // Add members
    if (data.members.length > 0) {
        for (var i= 0; i < data.members.length; i++) {
            tmp_task_member = $('<div class="project-member" data-id=""><a href="" title=""><img src="" alt="profile_image" /></a></div>');
            tmp_task_member.attr('data-id', data.members[i].id);
            tmp_task_member.find('a').attr('href', data.members[i].url).attr('title', data.members[i].fullname);
            tmp_task_member.find('img').attr('src', data.members[i].image_url);
            tmp_task_member.appendTo(tmp_task.find('.task-members'));
        }
    }
    // Add resources
    if (data.resources.length > 0) {
        for (var i=0; i < data.resources.length; i++) {
            tmp_task_resource = $('<div class="project-resource" data-id=""><img src="" title="" alt="resource" class="teke-tooltip" /><div class="teke-tooltip-content"><label></label><br /></div></div>');
            tmp_task_resource.attr('data-id', data.resources[i].id);
            tmp_task_resource.find('img').attr('src', data.resources[i].resource_type_url).attr('title', data.resources[i].title);
            if (data.resources[i].url.length == 0) {
                tmp_task_resource.find('.teke-tooltip-content label').html(data.resources[i].title);
            } else {
                tmp_task_resource.find('.teke-tooltip-content label').html($('<a href="" target="_blank"></a>').attr('href', data.resources[i].url).html(data.resources[i].title));
            }
            tmp_task_resource.find('.teke-tooltip-content').append(data.resources[i].description);
            teke.initialize_element_tooltip(tmp_task_resource);
            tmp_task_resource.appendTo(tmp_task.find('.task-resources'));
        }
    }
    start_date = new Date(data.start_date);
    end_date = new Date(data.end_date);
    tmp_task.width((end_date.getTime() - start_date.getTime()) / timeline.getPixelValue()).css('left', ( (start_date.getTime() - new Date(timeline.getStart()).getTime()) / timeline.getPixelValue() )+'px');
    teke.initialize_element_toggler(tmp_task);
    // Initialize task as droppable
    teke.initialize_tasks_droppables(tmp_task);
    tmp_task.appendTo('#project-timeline-tasks');
};

/* Add beginning and end pointo to timeline */
teke.add_beginning_end_to_timeline = function() {
    $('<div id="project-timeline-beginning" class="beginning" style="left:0px;"><img src="'+teke.get_site_url()+'views/graphics/grey_circle.png" alt="circle" /><div class="timeline-above-date">'+teke.format_date(new Date(timeline.getTimelineData().beginning))+'</div></div>').appendTo($('#project-timeline-project'));
    $('#project-timeline-beginning').on('click', function(event) {
		// Prevent parent click from happening
	    event.stopPropagation();
        // Add chnage possibility
        $('<div id="change-project-beginning" title="'+teke.translate('title_change_project_beginning')+'"><div name="project-beginning-date"></div></div>').dialog({
            autoOpen: true,
            height: 'auto',
            width: 'auto',
            modal: true,
            buttons: [
                {
                    text: teke.translate('button_change'),
                    click: function() {
                        var _this = $(this);
                        $.ajax({
                            cache: false,
                            type: "POST",
                            url: teke.get_site_url()+"actions/edit_project_start.php",
                            data: { project_id: teke.get_project_id(), start_date: _this.find('div[name="project-beginning-date"]').datepicker("getDate").toUTCString() },
                            dataType: "json",
                            success: function(data) {
                                if (data.state == 0) {
                                    // Make a refresh, as all timeline parameters need to be recalculated
                                    window.location.reload();
                                } else {
                                    // Add messages if any provided
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
                $(this).find('div[name="project-beginning-date"]').datepicker({ maxDate: new Date(timeline.getEnd()) }).datepicker('setDate', new Date(timeline.getStart()));
            },
            close: function() {
                $(this).dialog('destroy');
                $(this).remove();
            }
        });
	});

    $('<div id="project-timeline-end" class="end" style="right:0px;"><img src="'+teke.get_site_url()+'views/graphics/grey_circle.png" alt="circle" /><div class="timeline-above-date">'+teke.format_date(new Date(timeline.getTimelineData().end))+'</div></div>').appendTo($('#project-timeline-project'));
	$('#project-timeline-end').on('click', function(event) {
		// Prevent parent click from happening
	    event.stopPropagation();
        // Add chnage possibility
        $('<div id="change-project-end" title="'+teke.translate('title_change_project_end')+'"><div name="project-end-date"></div></div>').dialog({
            autoOpen: true,
            height: 'auto',
            width: 'auto',
            modal: true,
            buttons: [
                {
                    text: teke.translate('button_change'),
                    click: function() {
                        var _this = $(this);
                        $.ajax({
                            cache: false,
                            type: "POST",
                            url: teke.get_site_url()+"actions/edit_project_end.php",
                            data: { project_id: teke.get_project_id(), end_date: _this.find('div[name="project-end-date"]').datepicker("getDate").toUTCString() },
                            dataType: "json",
                            success: function(data) {
                                if (data.state == 0) {
                                    // Make a refresh, as all timeline parameters need to be recalculated
                                    window.location.reload();
                                } else {
                                    // Add messages if any provided
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
                $(this).find('div[name="project-end-date"]').datepicker({ minDate: new Date(timeline.getStart()) }).datepicker('setDate', new Date(timeline.getEnd()));
            },
            close: function() {
                $(this).dialog('destroy');
                $(this).remove();
            }
        });

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

// Initialization for timeline tasks holder droppables
teke.initialize_tasks_timeline_droppable = function() {
    $('#project-timeline-tasks').droppable({
        accept: '[id^="project-task-"]',
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function(event, ui) {
            $('<div id="add-task-to-timeline" title="'+teke.translate('title_add_task_to_timeline')+'"><div name="task-start-date"></div><div name="task-end-date"></div></div>').dialog({
                autoOpen: true,
                height: 'auto',
                width: 'auto',
                modal: true,
                buttons : [
                    {
                        text: teke.translate('button_add'),
                        click: function() {
                            _this = $(this);
                            $.ajax({
                                cache: false,
                                type: "POST",
                                url: teke.get_site_url()+"actions/add_task_to_timeline.php",
                                data: { task_id: ui.draggable.attr('data-id'), start_date: _this.find('div[name="task-start-date"]').datepicker('getDate').toUTCString(), end_date: _this.find('div[name="task-end-date"]').datepicker('getDate').toUTCString() },
                                dataType: "json",
                                success: function(data) {
                                    if (data.state == 0) {
                                        // Add task to timeline
                                        teke.add_task_to_timeline(data.data.task);
                                        // XXX Need to sort tasks by their positioning, so that order would remain the same
                                        // Remove original element
                                        $('#'+ui.draggable.attr('id')).remove();
                                        // Update activity flow if needed
                                        if ($('#project-diary-and-messages-filter > select').val() != 'messages') {
                                            teke.project_update_messages_flow();
                                        }
                                        _this.dialog('close');
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
                    // XXX Dates should depend on each other
                    $(this).find('div[name="task-start-date"]').datepicker({ minDate: new Date(timeline.getStart()), maxDate: new Date(timeline.getEnd()) });
                    $(this).find('div[name="task-end-date"]').datepicker({ minDate: new Date(timeline.getStart()), maxDate: new Date(timeline.getEnd()) });
                },
                close: function() {
                    $(this).dialog('destroy');
                    $(this).remove();
                }
            });
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

            for (var key in data.tasks) {
                teke.add_task_to_timeline(data.tasks[key]);
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
