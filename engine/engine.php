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
require_once(dirname(__FILE__)."/lib/ajax/AJAXResponse.php");
// TODO Consider making UserManager as with Projectand ProjectManager
require_once(dirname(__FILE__)."/lib/user/user.php");
require_once(dirname(__FILE__)."/lib/project/ProjectManager.php");
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
 * FACEBOOK LOADER *
 ******************/

if (FACEBOOK) {
    require_once(dirname(__FILE__)."/lib/facebook/facebook.php");
    $facebook = new Facebook(array(
        'appId' => FACEBOOK_APP_ID,
        'secret' => FACEBOOK_APP_SECRET,
        'fileUpload' => false,
        'cookie' => true
    ));
    $TeKe->facebook = $facebook;
}

if (isset($_REQUEST["set_language"]) && isset($_REQUEST["language"]) && in_array($_REQUEST["language"], array_keys($TeKe->getAvailableLanguages()))) {
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
if ($TeKe->is_logged_in() || (count($page) > 0 && in_array($page[0], array()))) {
    
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

function query_row($sql, $classname = NULL) {
    if (!$classname) {
        $classname = 'stdClass';
    }
    $res = query($sql);
    if ($res) {
        $ret = mysql_fetch_object($res, $classname);
        return $ret;
    }
    return false;
}

function query_rows($sql, $classname = NULL) {
    if (!$classname) {
        $classname = 'stdClass';
    }
    $res = query($sql);
    $rows = array();
    if(mysql_num_rows($res)) {
        while($row = mysql_fetch_object($res, $classname)) {
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

function query_delete($sql) {
    $res = query($sql);
    if ($res) {
        return mysql_affected_rows();
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

function get_logged_in_user_id() {
    global $TeKe;
    if ($TeKe->is_logged_in()) {
        return $TeKe->user->getId();
    }
    return false;
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

function console_log($message, $title=false) {
    global $TeKe;
    $TeKe->firephp->log($message, $title);
}

function format_date_for_js($date_string) {
    return date('r', strtotime($date_string));
}

function force_plaintext($string) {
    // TODO Check if htmlentities need to be used
    return strip_tags($string);
}

?>
