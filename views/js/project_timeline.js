function Timeline() {
	this.start = 0;
	this.end = 0;
	this.pixel_value = 0;
	this.width = 0;
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

$(document).ready(function() {
	// TODO Possibly dates should be transcribed into milliseconds (or genearte those from full dates)
	// Create timeline
	var timeline = new Timeline();
	timeline.setStart(parseInt($('#project_start').val()));
	timeline.setEnd(parseInt($('#project_end').val()));
	timeline.setWidth(600);
	timeline.calculatePixesValue();
	// XXX Width should not be hard coded
	$('#project-timeline-project').width(600);
	$('#project-timeline-project').on('click', function(event) {
		// TODO see position() method
		offset = parseInt(event.pageX) - parseInt($(this).offset().left);
		time = timeline.getStart() + ( offset * timeline.getPixelValue());
		// XXX One day seems to be lot from the end
		//alert(new Date(time * 1000));
		//alert(offset);
		$('<div class="event" style="left: '+offset+'px;top:-11px;"><img src="'+teke.get_site_url()+'views/graphics/timeline_event.png" alt="flag" /></div>'). appendTo($('#project-timeline-project'));
		// TODO Needs a general standalone method
		$('#project-timeline-project .event').on('click', function(event) {
			event.stopPropagation();
        });
	});
	// Add now line to the project if applicable
	now_time = new Date().getTime();
	if ( (now_time > timeline.getStart() * 1000) && (now_time < timeline.getEnd() * 1000)) {
		now_offset = (now_time - (timeline.getStart() * 1000)) / (timeline.getPixelValue() * 1000);
	    $('<div class="now" style="left: '+now_offset+'px"></div>').appendTo($('#project-timeline'));
	}
});
