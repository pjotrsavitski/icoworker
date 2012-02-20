var teke = teke || {};

teke.config = {};
teke.translations = {};

teke.get_site_url = function() {
	return teke.config.wwwroot;
};

teke.get_facebook_app_id = function() {
	return teke.config.facebook_app_id;
};

/* SYSTEM MESAGES */
teke.animate_system_messages = function() {
    $('span.system_message_close').click(function() {
        $('#system_messages').stop();
        $('#system_messages').fadeOut(400);
    });

    $("div[id^='system_message_']").click(function() {
        $('#system_messages').stop();
        $('#system_messages').fadeOut(400);
    });

    $("div[id^='system_message_']").delay(5000).fadeOut(1600);
};

teke.replace_system_messages = function(messages) {
	$('#system_messages').show();
	$('#system_messages').html(messages);
	teke.animate_system_messages();
};

/* TRANSLATIONS */
teke.add_translation = function(msgid, msgstr) {
	return this.translations[msgid] = msgstr;
};

teke.translate = function(msgid) {
	if (msgid in this.translations) {
		return this.translations[msgid];
	}
	return msgid;
};

/* HELPERS */
teke.set_language = function(lang) {
	window.location = this.get_site_url()+"?set_language=true&language="+lang;
};

teke.initialize_language_selection = function() {
    $('#language-selection').find('a').click(function(event) {
	    event.preventDefault();
		teke.set_language($(this).find('input:hidden[name="language_selection_language"]').val());
    });
};

teke.initialize_togglers = function() {
    $('.teke-toggler').click(function (e) {
		e.preventDefault();
		if ($(this).hasClass('teke-slide-toggler')) {
		    $(this).nextAll('.teke-togglable').slideToggle("slow");
		} else {
		    $(this).nextAll('.teke-togglable').toggle();
		}
		$(this).toggleClass('teke-toggler-toggled');
	});
};

$(document).ready(function() {
	teke.animate_system_messages();

	teke.initialize_language_selection();

	teke.initialize_togglers();
});

function toggleSelect(elem, name) {
    if ($(elem).is(":checked")) {
       $("input[type=checkbox], input[name="+name+"]").attr("checked", "checked"); 
    } else {
       $("input[type=checkbox], input[name="+name+"]").attr("checked", ""); 
    }
}

// register onLoad event with anonymous function
window.onload = function (e) {
    var evt = e || window.event,// define event (cross browser)
        imgs,                   // images collection
        i;                      // used in local loop
    // if preventDefault exists, then define onmousedown event handlers
    if (evt) {
        if (evt.preventDefault) {
            // collect all images on the page
            imgs = document.getElementsByTagName('img');
            // loop through fetched images
            for (i = 0; i < imgs.length; i++) {
                // and define onmousedown event handler
                imgs[i].onmousedown = disableDragging;
            }
        }
    }
};
 
// disable image dragging
function disableDragging(e) {
    e.preventDefault();
}
