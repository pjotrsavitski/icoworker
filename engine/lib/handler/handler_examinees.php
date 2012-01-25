<?php
class ExamineesHandler extends BaseHandler {
    
    public $name = "examinees";
    
    function __construct($page) {
        parent::__construct($page);
        if (is_file(dirname(dirname(dirname(dirname(__FILE__)))).'/views/examinees/'.$this->name."_".$page[0].'.html')) {
            
            $this->setTemplate($this->name."_".$page[0]);
        }
    }
    
    public function setNavigation() {
        $this->navigation []= array('title'=>_('View'), 'url'=>"examinees/view", 'current'=>$this->is_current_page($this->page[0], "view"));
        $this->navigation []= array('title'=>_('Generate'), 'url'=>"examinees/generate", 'current'=>$this->is_current_page($this->page[0], "generate"));
    }
}
?>
