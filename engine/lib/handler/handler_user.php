<?php
class UserHandler extends BaseHandler {
    
    public $name = "user";
    
    function __construct($page) {
        if (is_file(dirname(dirname(dirname(dirname(__FILE__)))).'/views/user/'.$this->name."_".$page[0].'.html')) {
            $this->template = $this->name."_".$page[0];
        } else {
            $this->template = "page_not_found";
        }
        parent::__construct($page);
    }
    
    public function setNavigation() {
        $uname = "";
        if (isset($this->page[1])) $uname = $this->page[1];
        $this->navigation []= array('title'=>_('View'), 'url'=>"user/view/{$uname}", 'current'=>$this->is_current_page($this->page[0], "view"));
        //$this->navigation []= array('title'=>_('Edit profile'), 'url'=>"user/edit/{$uname}", 'current'=>$this->is_current_page($this->page[0], "edit"));
        $this->navigation []= array('title'=>_('Edit settings'), 'url'=>"user/settings/{$uname}", 'current'=>$this->is_current_page($this->page[0], "settings"));
    }

    public function getUserByUserName() {
        $uname = "";
        if (isset($this->page[1])) $uname = $this->page[1];
        return new User($uname);
    }
}
?>
