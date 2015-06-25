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
        global $TeKe;

        if ($TeKe->is_admin()) {
            $id = "";
            if (isset($this->page[1])) $id = $this->page[1];
            $this->navigation[] = [
                'title' => _('Download Data'),
                'url' => "actions/download_project_data.php?project_id={$id}",
                'current' => 0,
                'target' => '_blank',
            ];
        }
    }

    public function getProjectById() {
        if (isset($this->page[1])) $id = (int)$this->page[1];
        if (isset($id) && $id) {
            return ProjectManager::getProjectById($id);
        }
        return false;
    }
}
?>
