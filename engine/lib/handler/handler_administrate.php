<?php
class AdministrateHandler extends BaseHandler {
    
    public $name = "administrate";
    public $db_prefix = DB_PREFIX;
    
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

    public function getUsersCount() {
        $q = "SELECT COUNT(id) AS count FROM {$this->db_prefix}users WHERE 1";
        $result = query_row($q);
        if ($result) {
            return $result->count;
        }
        return 0;
    }

    public function getUsersCountWithRole($roleid) {
        $roleid = (int)$roleid;
        $q = "SELECT COUNT(id) AS count FROM {$this->db_prefix}users WHERE role = {$roleid}";
        $result = query_row($q);
        if ($result) {
            return $result->count;
        }
        return 0;
    }

    public function getAdminsCount() {
        return $this->getUsersCountWithRole(9);
    }

    public function getMembersCount() {
        return $this->getUsersCountWithRole(ACCESS_CAN_EDIT);
    }

    public function getGuestsCount() {
        return $this->getUsersCountWithRole(ACCESS_CAN_VIEW);
    }

    public function getProjectsCount() {
        $q = "SELECT COUNT(id) AS count FROM {$this->db_prefix}projects WHERE 1";
        $result = query_row($q);
        if ($result) {
            return $result->count;
        }
        return 0;
    }
}
