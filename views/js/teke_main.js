$(document).ready(function() {
    $('span.system_message_close').click(function() {
        $('#system_messages').stop();
        $('#system_messages').fadeOut(400);
    });

    $("div[id^='system_message_']").click(function() {
        $('#system_messages').stop();
        $('#system_messages').fadeOut(400);
    });

    $("div[id^='system_message_']").delay(5000).fadeOut(1600);

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

function setLanguage(lang) {
    window.location = WWW_ROOT+"?set_language=true&language="+lang;
}
