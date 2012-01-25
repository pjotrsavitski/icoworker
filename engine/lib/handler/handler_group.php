<?php
class GroupHandler extends BaseHandler {
    
    public $name = "group";
    
    function __construct($page) {
        if (is_file(dirname(dirname(dirname(dirname(__FILE__)))).'/views/group/'.$this->name."_".$page[0].'.html')) {
            $this->template = $this->name."_".$page[0];
        }
        parent::__construct($page);
    }
    
    public function setNavigation() {
        $id = "";
        if (isset($this->page[1])) $id = $this->page[1];
        $this->navigation []= array('title'=>_('View'), 'url'=>"group/view/{$id}", 'current'=>$this->is_current_page($this->page[0], "view"));
        $this->navigation []= array('title'=>_('Edit'), 'url'=>"group/edit/{$id}", 'current'=>$this->is_current_page($this->page[0], "edit"));
    }
}
?>