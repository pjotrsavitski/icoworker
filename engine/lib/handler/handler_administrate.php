<?php
class AdministrateHandler extends BaseHandler {
    
    public $name = "administrate";
    
    function __construct($page) {
        parent::__construct($page);
        if (is_file(dirname(dirname(dirname(dirname(__FILE__)))).'/views/administrate/'.$this->name."_".$page[0].'.html')) {
            
            $this->setTemplate($this->name."_".$page[0]);
        } else {
            $this->template = "page_not_found";
        }
    }
    
    public function setNavigation() {
        $this->navigation []= array('title'=>_('Administrate TeKe'), 'url'=>"administrate/teke", 'current'=>$this->is_current_page($this->page[0], "teke"));
        $this->navigation []= array('title'=>_('Administrate users'), 'url'=>"administrate/user", 'current'=>$this->is_current_page($this->page[0], "user"));
    }
}
?>
