<?php
require_once("handler_user.php");
require_once("handler_project.php");
require_once("handler_administrate.php");
require_once("handler_ajax.php");

class BaseHandler {
    public $page;
    public $navigation = array();
    public $navigation_main = array();
    public $template;
    
    public function __construct($page) {
        $this->page = $page;
        $this->setNavigation();
    }
    
    public function is_current_page($current, $page) {
        if ($current==$page) return 1;
        return 0;
    }
    
    public function setTemplate($templ) {
         $this->template = $templ;
    }

}
?>
