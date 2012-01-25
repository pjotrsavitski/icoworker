
jQuery(document).ready(function() {
    jQuery(document.body).append(jQuery('<div id="fb-root"></div>'));
});

window.fbAsyncInit = function() {
    FB.init({
    appId      : teke.get_facebook_app_id(), // App ID
    // TODO resolve this
    //channelUrl : '//WWW.YOUR_DOMAIN.COM/channel.html', // Channel File
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
});
FB.Event.subscribe('auth.login', function () {
    window.location = teke.get_site_url()+'actions/login.php';
});
FB.Event.subscribe('auth.logout', function () {
    window.location = teke.get_site_url()+'actions/logout.php';
});


// Additional initialization code here
};

// Load the SDK Asynchronously
(function(d){
 var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
 js = d.createElement('script'); js.id = id; js.async = true;
 js.src = "//connect.facebook.net/en_US/all.js";
 d.getElementsByTagName('head')[0].appendChild(js);
 }(document));
