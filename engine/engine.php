<?php
require_once(dirname(dirname(__FILE__))."/config/config.php");
if (defined("SESSION_SAVE_PATH")) {
    if (file_exists(SESSION_SAVE_PATH)) {
        session_save_path(SESSION_SAVE_PATH);
        ini_set('session.gc_probability', 1);
    }
}
session_name(PLUGIN);
session_start();
require_once(dirname(__FILE__)."/lib/phptal/PHPTAL.php");
require_once(dirname(__FILE__)."/lib/database/database.php");
require_once(dirname(__FILE__)."/lib/user/user.php");
require_once(dirname(__FILE__)."/lib/examinees/examinees.php");
require_once(dirname(__FILE__)."/lib/group/group.php");
require_once(dirname(__FILE__)."/lib/image/Image.php");
require_once(dirname(__FILE__)."/lib/sound/Sound.php");
require_once(dirname(__FILE__)."/lib/video/YoutubeVideo.php");
require_once(dirname(__FILE__)."/lib/handler/handler.php");
require_once(dirname(__FILE__)."/lib/firephp/FirePHP.class.php");

DEFINE("PLUGIN_ROOT", WWW_ROOT.'includes/'.PLUGIN.'/');


/**************
* Page Loader *
***************/
$page = get_input("page");
$page = explode('/',$page);
if ($page[count($page) - 1] === '') {
	array_pop($page);
}

/**************
* TeKe Loader *
***************/
require_once(dirname(__FILE__)."/teke.php");
$TeKe = new TeKe($page);
$TeKe->db = new DB();
$user = new User();
if (isset($_SESSION["user"])) {
    $user->load($_SESSION["user"]);
}
$TeKe->user = $user;
$TeKe->getTemplate();

/*****************
* FirePHP Loader *
******************/
ob_start();
$TeKe->firephp = FirePHP::getInstance(true);
if (!DEV_MODE) {
    $TeKe->firephp->setEnabled(false);    
}

/****************
* Plugin Loader *
****************/
if (is_file(dirname(dirname(__FILE__))."/includes/".PLUGIN."/".PLUGIN.".php")) {
    require_once(dirname(dirname(__FILE__))."/includes/".PLUGIN."/".PLUGIN.".php");
}

/*******************
* Examinees Loader *
*******************/
// Triggers when examinees are turned ON in config
if (EXAMINEES) {
    $examinee = new Examinees();
    if (isset($_SESSION["examinee"])) {
        $examinee->load($_SESSION["examinee"]);
    }
    $TeKe->examinee = $examinee;
}

/*******************
 * FACEBOOK LOADER *
 ******************/

if (FACEBOOK) {
    require_once(dirname(__FILE__)."/lib/facebook/facebook.php");
    require_once(dirname(__FILE__)."/lib/facebook/FacebookUser.php");
    $facebook = new FacebookUser();
    $TeKe->facebook = $facebook;
}

if (isset($_REQUEST["set_language"]) && isset($_REQUEST["language"]) && in_array($_REQUEST["language"], array("et", "ru", "en"))) {
    $_SESSION['language'] = $_REQUEST["language"];
    if (isset($_SERVER['REDIRECT_URL'])) {
        header("location:".$_SERVER['REDIRECT_URL']);
    } else {
        header("location:".WWW_ROOT);
    }
    exit;
}

/**************
* TeKe Plugin *
**************/ 
$plugin = ucfirst(PLUGIN);
if (class_exists($plugin)) {
    $TeKe->plugin = new $plugin($page);
}

/**************
* TeKe Engine *
**************/ 
if ($TeKe->is_logged_in() || $TeKe->is_examinee() || (count($page) > 0 && in_array($page[0], array("facebook_connect", "register", "login", "password_recovery", "password_reset", "assessment")))) {
    $TeKe->group = new Group();
    
    $handler = get_input("handler", "pages");
    
    if (is_array($page) AND count($page)>0) {
        $TeKe->view_page($page, $handler);
    } else if ($handler=="test"){
        $page []= "view";
        $TeKe->view_page($page, "test");
    } else if (is_array($page) AND count($page)<=0){
        $page []= "index";
        $TeKe->view_page($page, "pages");
    } else {
        $TeKe->view_page("page_not_found");
    }
} else {
    $page = array("index");
    $TeKe->view_page($page, "pages");
}

function forward($location) {
	if ((substr_count($location, 'http://') == 0) && (substr_count($location, 'https://') == 0)) {
		$location = WWW_ROOT.$location;
	}
    header("location:".$location);
    exit;
}

function query($sql) {
    global $TeKe;
    return $TeKe->db->query($sql);
}

function query_row($sql) {
    $res = query($sql);
    if ($res) {
        $ret = mysql_fetch_object($res);
        return $ret;
    }
    return false;
}

function query_rows($sql) {
    $res = query($sql);
    $rows = array();
    if(mysql_num_rows($res)) {
        while($row = mysql_fetch_object($res)) {
            $rows []= $row;
        }
    }
    return $rows;
}

function query_insert($sql) {
    $res = query($sql);
    if ($res) {
        return mysql_insert_id();
    }
    return false;
}

function query_update($sql) {
    $res = query($sql);
    if ($res) {
        return true;
    }
    return false;
}

function get_input($variable, $default = "") {
    if (isset($_REQUEST[$variable])) {
        if (is_array($_REQUEST[$variable])) {
            $var = $_REQUEST[$variable];
        } else {
            $var = trim($_REQUEST[$variable]);
        }
        return $var;
    }
    return $default;
}

function get_inputs() {
    if (isset($_REQUEST)) {
        if (is_array($_REQUEST)) {
            return $_REQUEST;
        }
    }
    return array();
}

function get_file($input) {
    if (isset($_FILES[$input])) {
        if (is_array($_FILES[$input])) {
            return $_FILES[$input];
        }
    }
    return false;
}

function get_logged_in_user() {
    global $TeKe;
    return $TeKe->user;
}

function is_admin() {
    global $TeKe;
    return $TeKe->is_admin();
}

/**
 * Can be used at top of actions, for admins only.
 * All others will be forwarded to index.
 *
 * @return void
 */
function admin_gatekeeper() {
    if (!is_admin()) {
        forward("index");
    }
}

function get_examinee() {
    global $TeKe;
    return $TeKe->examinee;
}

function console_log($message, $title=false) {
    global $TeKe;
    $TeKe->firephp->log($message, $title);
}

?>
