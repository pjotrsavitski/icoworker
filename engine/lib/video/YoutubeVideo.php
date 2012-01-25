<?php

class YoutubeVideo {
    private $player = YOUTUBE_DEFAULT_PLAYER;
    private $url = "";
    private $id = "";
    private $width = 640;
    private $height = 390;
    private $autohide = 0;
    private $autoplay = 0;
    private $border = 0;
    private $cc_load_policy = 0;
    private $color1 = 0;
    private $color2 = 0;
    private $controls = 0;
    private $disablekb = 0;
    private $enablejsapi = 0;
    private $egm = 0;
    private $fs = 0;
    private $frameborder = 0;
    private $hd = 0;
    private $iv_load_policy = 3;
    private $loop = 0;
    private $origin = 0;
    private $playerapiid = 0;
    private $playlist = "";
    private $rel = 0;
    private $showinfo = 0;
    private $showsearch = 0;
    private $start = 0;
    private $theme = "dark";
    
    function __construct($url = false) {
        if ($url) {
            $this->url = $url;
            $this->id = $this->parse_url();
        }
    }
    
    function view() {
        if ($this->id) {
            if ($this->player == "as3") {
                return $this->view_as3();
            } else if ($this->player == "html5") {
                return $this->view_html5();
            } else {
                
            }
        }
        return false;
    }
            
    function view_as3() {
        $embed = "<object width=\"".$this->getWidth()."\" height=\"".$this->getHeight()."\">";
        $embed .= "<param name=\"movie\" value=\"http://www.youtube-nocookie.com/v/".$this->getId();
        $params = "?fs=".$this->getFS();
        $params .= "&rel=".$this->getRel();
        $params .= "&autoplay=".$this->getAutoplay();
        $params .= "&border=".$this->getBorder();
        $params .= "&controls=".$this->getControls();
        $params .= "&iv_load_policy=".$this->getIvLoadPolicy();
        $params .= "&loop=".$this->getLoop();
        $params .= "&showinfo".$this->getShowinfo();
        $params .= "&showsearch=".$this->getShowsearch();
        $params .= "&start=".$this->getStart();
        $embed .= $params."&amp;version=3\"></param>";
        if ($this->getFS()) {
            $embed .= "<param name=\"allowFullScreen\" value=\"true\"></param>";
        }
        $embed .= "<param name=\"allowscriptaccess\" value=\"always\"></param>";
        $embed .= "<embed src=\"http://www.youtube-nocookie.com/v/".$this->getId();
        $embed .= $params."&amp;version=3\"";
        $embed .= " type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\"";
        if ($this->getFS()) {
            $embed .= " allowfullscreen=\"true\"";
        }
        $embed .= " width=\"".$this->getWidth()."\" height=\"".$this->getHeight()."\"></embed>";
        $embed .= "</object>";
        return $embed;
    }
    
    function view_html5() {
        $params = "rel=".$this->getRel();
        $params .= "&autohide=".$this->getAutohide();
        $params .= "&autoplay=".$this->getAutoplay();
        $params .= "&controls=".$this->getControls();
        $params .= "&enablejsapi=".$this->getEnablejsapi();
        $params .= "&loop=".$this->getLoop();
        $params .= "&origin=".$this->getOrigin();
        $params .= "&playlist=".$this->getPlaylist();
        $params .= "&start=".$this->getStart();
        $params .= "&theme=".$this->getTheme();       
        $embed = "<iframe width=\"".$this->getWidth()."\" height=\"".$this->getHeight()."\" src=\"http://www.youtube.com/embed/".$this->getId()."?".$params."\" frameborder=\"".$this->getFrameborder()."\" ";
        if ($this->getFS()) {
            $embed .= " allowFullScreen ";
        }
        $embed .= "></iframe>";
        return $embed;
    }
    
    function resize($percent) {
        if (is_numeric($percent)) {
            $this->setWidth(round(($this->getWidth()*$percent)/100));
            $this->setHeight(round(($this->getHeight()*$percent)/100));
        }
    }
    
    function parse_url() {
        preg_match('/((http:\/\/)?(?:youtu\.be\/|(?:[a-z]{2,3}\.)?youtube\.com\/v\/)([\w-]{11}).*|http:\/\/(?:youtu\.be\/|(?:[a-z]{2,3}\.)?youtube\.com\/watch(?:\?|#\!)v=)([\w-]{11}).*)/i', $this->url, $u);
        if (count($u)>3) {
            return $u[3];
        }
        return "";
    }
    
    function getId() {
        return $this->id;
    }
    
    function setId($id) {
        $this->id = $id;
    }

    function getWidth() {
        return $this->width;
    }
    
    function setWidth($width) {
        $this->width = $width;
    }
    
    function getHeight() {
        return $this->height;
    }
    
    function setHeight($height) {
        $this->height = $height;
    }
    
    function getTheme() {
        return $this->theme;
    }
    
    function setTheme($theme) {
        $this->theme = $theme;
    }
    
    /*
    autohide
        Values: 0, 1, and 2 (default). This parameter indicates whether the video controls will automatically hide after a video begins playing. The default behavior is for the video progress bar to fade out while the player controls (play button, volume control, etc.) remain visible.
    
            * If this parameter is set to 0, the video progress bar and the video player controls will be visible throughout the video.
            * If this parameter is set to 1, then the video progress bar and the player controls will slide out of view a couple of seconds after the video starts playing. They will only reappear if the user moves her mouse over the video player or presses a key on her keyboard.
    */
    function getAutohide() {
        return $this->autohide;
    }
    
    function setAutohide($autohide) {
        $this->autohide = $autohide;
    }
    /*
    autoplay
        Values: 0 or 1. Default is 0. Sets whether or not the initial video will autoplay when the player loads.
    */
    function getAutoplay() {
        return $this->autoplay;
    }
    
    function setAutoplay($autoplay) {
        $this->autoplay = $autoplay;
    }
    /*
    border
        Values: 0 or 1. Default is 0. Setting to 1 enables a border around the entire video player. The border's primary color can be set via the color1 parameter, and a secondary color can be set by the color2 parameter.
    */
    function getBorder() {
        return $this->border;
    }
    
    function setBorder($border) {
        $this->border = $border;
    }
    
    function getFrameborder() {
        return $this->frameborder;
    }
    
    function setFrameborder($frameborder) {
        $this->frameborder = $frameborder;
    }
    
    /*
    cc_load_policy
        Values: 1. Default is based on user preference. Setting to 1 will cause closed captions to be shown by default, even if the user has turned captions off.
    */
    function getCcLoadPolicy() {
        return $this->cc_load_policy;
    }
    
    function setCcLoadPolicy($cc_load_policy) {
        $this->cc_load_policy = $cc_load_policy;
    }
    /*
    color1, color2
        Values: Any RGB value in hexadecimal format. color1 is the primary border color, and color2 is the video control bar background color and secondary border color.
    */
    function getColor1() {
        return $this->color1;
    }
    
    function setColor1($color1) {
        $this->color1 = $color1;
    }
    
    function getColor2() {
        return $this->color2;
    }
    
    function setColor2($color2) {
        $this->color2 = $color2;
    }
    /*
    controls
        Values: 0 or 1. Default is 1. This parameter indicates whether the video player controls will display. If this parameter is set to 0, then the player controls will not display, causing the player to look like the chromeless player.
    */
    function getControls() {
        return $this-> controls;
    }
    
    function setControls($controls) {
        $this->controls = $controls;
    }
    /*
    disablekb
        Values: 0 or 1. Default is 0. Setting to 1 will disable the player keyboard controls. Keyboard controls are as follows:
             Spacebar: Play / Pause
             Arrow Left: Jump back 10% in the current video
             Arrow Right: Jump ahead 10% in the current video
             Arrow Up: Volume up
             Arrow Down: Volume Down 
    */
    function getDisablekb() {
        return $this->disablekb;
    }
    
    function setDisablekb($disablekb) {
        $this->disablekb = $disablekb;
    }
    /*
    egm
        Values: 0 or 1. Default is 0. Setting to 1 enables the "Enhanced Genie Menu". This behavior causes the genie menu (if present) to appear when the user's mouse enters the video display area, as opposed to only appearing when the menu button is pressed.
    
        Note: This parameter is not supported in the AS3 embedded player.
    */
    function getEgm() {
        return $this->egm;
    }
    
    function setEgm($egm) {
        $this->egm = $egm;
    }
    /*
    enablejsapi
        Values: 0 or 1. Default is 0. Setting this to 1 will enable the Javascript API. For more information on the Javascript API and how to use it, see the JavaScript API documentation.
    */
    function getEnablejsapi() {
        return $this->enablejsapi;
    }
    
    function setEnablejsapi($enablejsapi) {
        $this->enablejsapi = $enablejsapi;
    }
    /*
    fs
        Values: 0 or 1. Default is 0. Setting to 1 enables the fullscreen button in the embedded player and does not affect the chromeless player. The fullscreen option will not work if you load the YouTube player into another SWF. Note that you must include some extra arguments to your embed code for this to work. The bold text in the following example is required to enable fullscreen functionality:
    
        <object width="425" height="344">
        <param name="movie" value="http://www.youtube.com/v/u1zgFlCw8Aw?fs=1"</param>
        <param name="allowFullScreen" value="true"></param>
        <param name="allowScriptAccess" value="always"></param>
        <embed src="http://www.youtube.com/v/u1zgFlCw8Aw?fs=1"
          type="application/x-shockwave-flash"
          allowfullscreen="true"
          allowscriptaccess="always"
          width="425" height="344">
        </embed>
        </object>
    */
    function getFS() {
        return $this->fs;
    }
    
    function setFS($fs) {
        $this->fs = $fs;
    }
    
    /*
    hd
        Values: 0 or 1. Default is 0. Setting to 1 enables HD playback by default. This has no effect on the Chromeless Player. This also has no effect if an HD version of the video is not available. If you enable this option, keep in mind that users with a slower connection may have an sub-optimal experience unless they turn off HD. You should ensure your player is large enough to display the video in its native resolution.
    
        Note: The AS3 player will automatically play the version of the video that is appropriate for your player's size. If an HD version of a video is available in the AS3 player and that is the appropriate version for your player, then that is the version that will play.
    */
    function getHD() {
        return $this->hd;
    }
    
    function setHD($hd) {
        $this->hd = $hd;
    }
    /*
    iv_load_policy
        Values: 1 or 3. Default is 1. Setting to 1 will cause video annotations to be shown by default, whereas setting to 3 will cause video annotation to not be shown by default.
    */
    function getIvLoadPolicy() {
        return $this->iv_load_policy;
    }
    
    function setIvLoadPolicy($iv_load_policy) {
        $this->iv_load_policy = $iv_load_policy;
    }
    /*
    loop
        Values: 0 or 1. Default is 0. In the case of a single video player, a setting of 1 will cause the player to play the initial video again and again. In the case of a playlist player (or custom player), the player will play the entire playlist and then start again at the first video.
    
        Note: This parameter has limited support in the AS3 player and in IFrame embeds, which could load either the AS3 or HTML5 player. Currently, the loop parameter only works in the AS3 player when used in conjunction with the playlist parameter. To loop a single video, set the loop parameter value to 1 and set the playlist parameter value to the same video ID already specified in the Player API URL:
    
        http://www.youtube.com/v/VIDEO_ID?version=3&loop=1&playlist=VIDEO_ID
    
    */
    function getLoop() {
        return $this->loop;
    }
    
    function setLoop($loop) {
        $this->loop = $loop;
    }
    /*
    origin
        This parameter provides an extra security measure for the IFrame API and is only supported for IFrame embeds. If you are using the IFrame API, which means you are setting the enablejsapi parameter value to 1, you should always specify your domain as the origin parameter value.
    */
    function getOrigin() {
        return $this->origin;
    }
    
    function setOrigin($origin) {
        $this->origin = $origin;
    }
    /*
    playerapiid
        Value can be any alphanumeric string. This setting is used in conjunction with the JavaScript API. See the JavaScript API documentation for details.
    */
    function getPlayerapiid() {
        return $this-> playerapiid;
    }
    
    function setPlayerapiid($playerapiid) {
        $this->playerapiid = $playerapiid;
    }
    /*
    playlist
        Value is a comma-separated list of video IDs to play. If you specify a value, the first video that plays will be the VIDEO_ID specified in the URL path, and the videos specified in the playlist parameter will play thereafter.
    */
    function getPlaylist() {
        return $this-> playlist;
    }
    
    function setPlaylist($playlist) {
        $this->playlist = $playlist;
    }
    /*rel
        Values: 0 or 1. Default is 1. Sets whether the player should load related videos once playback of the initial video starts. Related videos are displayed in the "genie menu" when the menu button is pressed. The player search functionality will be disabled if rel is set to 0.
    */
    function getRel() {
        return $this->rel;
    }
    
    function setRel($rel) {
        $this->rel = $rel;
    }
    /*
    showinfo
        Values: 0 or 1. Default is 1. Setting to 0 causes the player to not display information like the video title and uploader before the video starts playing.
    */
    function getShowinfo() {
        return $this->showinfo;
    }
    
    function setShowinfo($showinfo) {
        $this->showinfo = $showinfo;
    }
    /*
    showsearch
        Values: 0 or 1. Default is 1. Setting to 0 disables the search box from displaying when the video is minimized. Note that if the rel parameter is set to 0 then the search box will also be disabled, regardless of the value of showsearch.
    */
    function getShowsearch() {
        return $this->showsearch;
    }
    
    function setShowsearch($showsearch) {
        $this->showsearch = $showsearch;
    }
    /*
    start
        Values: A positive integer. This parameter causes the player to begin playing the video at the given number of seconds from the start of the video. Note that similar to the seekTo function, the player will look for the closest keyframe to the time you specify. This means sometimes the play head may seek to just before the requested time, usually no more than ~2 seconds. 
     */
    function getStart() {
        return $this->start;
    }
    
    function setStart($start) {
        $this->start = $start;
    }
    
}  
?>