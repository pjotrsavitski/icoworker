<?php
class AjaxHandler extends BaseHandler {
    
    public $name = "ajax";
    
    function __construct($page) {
        if (is_file(dirname(dirname(dirname(dirname(__FILE__)))).'/views/ajax/'.$this->name."_".$page[0].'.html')) {
            $this->template = $this->name."_".$page[0];
        } else {
            // Fail if there is not page to be loaded
            header("HTTP/1.0 404 Not Found");
            exit;
        }
        parent::__construct($page);
    }
    
    public function setNavigation() {
    }
}
