<?php
class ProjectHandler extends BaseHandler {
    
    public $name = "project";
    
    function __construct($page) {
        if (is_file(dirname(dirname(dirname(dirname(__FILE__)))).'/views/project/'.$this->name."_".$page[0].'.html')) {
            $this->template = $this->name."_".$page[0];
        } else {
            $this->template = "page_not_found";
        }
        parent::__construct($page);
    }
    
    public function setNavigation() {
        if (isset($this->page[1])) $id = $this->page[1];
        $this->navigation []= array('title'=>_('View'), 'url'=>"project/view/{$id}", 'current'=>$this->is_current_page($this->page[0], "view"));
    }
}
?>
