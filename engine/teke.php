<?php
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

class TeKe {
    public $title = "TeKe";
    public $translator;
    public $template;
    public $handler;
    public $navigation = array();
    public $action = 'view';
    public $plugin;
    public $plugin_loaded = false;
    public $firephp;
    
    public function __construct($page) {
        
    }
    
    public function getTemplate() {  
        $this->setTranslator();    
        $this->setTemplate();
    }
    
    private function setTemplate() {
        $this->template = new PHPTAL();
        if (defined("PHPTAL_TMP")) {
            $this->template->setphpCodeDestination(PHPTAL_TMP);
        }
        $this->setTemplateRepository();
        $this->template->setTranslator($this->getTranslator());
    }
    
    private function setTemplateRepository() {
        $repository_folders = array("templates", "user", "project", "ajax", "pages", "administrate");
        $repos_path = dirname(dirname(__FILE__));
        if (is_dir(dirname(dirname(__FILE__))."/includes/".PLUGIN)) {
             $this->plugin_loaded = dirname(dirname(__FILE__))."/includes/".PLUGIN."/";
        } 
        foreach ($repository_folders as $repository_folder) {
            if ($this->plugin_loaded && is_dir($this->plugin_loaded.'/views/'.$repository_folder)) {
                $this->template->setTemplateRepository($this->plugin_loaded.'/views/'.$repository_folder.'/');
            }
            $this->template->setTemplateRepository(dirname(dirname(__FILE__)).'/views/'.$repository_folder.'/');
        }
    }
    
    private function setTranslator() {
        $language = DEFAULT_LANGUAGE;
        if (isset($_SESSION['language']) && in_array($_SESSION['language'], array_keys($this->getAvailableLanguages()))) {
            $language = $_SESSION['language'];
        } else if ($this->is_logged_in()) {
             $language = $this->user->language;
        }
        $tr = new PHPTAL_GetTextTranslator();
        $tr->setLanguage(constant('LOCALE_'.$language).DEFAULT_ENCODING, constant('LOCALE_'.$language).DEFAULT_ENCODING);
        $tr->addDomain(DEFAULT_DOMAIN, 'i18n');
        $tr->useDomain(DEFAULT_DOMAIN);
        $this->translator = $tr;
    }
    
    function getTranslator() {
        return $this->translator;
    }

    function setNavigation() {
        if ($this->plugin AND method_exists($this->plugin, "setNavigation")) {
            $this->plugin->setNavigation($this);
        }
        if (is_admin()) {
            $this->navigation['administration'] = array('title'=>_('Administration'), 'url'=>"administrate/teke", 'current'=>$this->is_current_main('administrate'), 'level'=>0);
        }
    }

    function is_current_main($current) {
        if (!$this->handler) return false;
        if ($this->handler->name == $current) {
            return true;
        }
        return false;
    }
    
    /************
    * Page View *
    ************/
    private function view($view="page_not_found") {
         if ($this->execute_page()) {
            $this->template->teke = $this;
            $this->template->request = $_GET;
            $input_values = array();
            if (isset($_SESSION['input_values'])) {
                $input_values = $_SESSION['input_values']; 
                unset($_SESSION['input_values']);
            }
            $this->template->input_values = $input_values;
            $this->template->setTemplate($view.".html");
            echo $this->template->execute();
         }
    }

    public function view_page($page, $handler=NULL) {
        $this->page = $page;
        $template = $page[0];
        if (in_array(end($page), array("add", "edit", "compose", "new", "import", "results", "items", "candidates", "preview", "test", "proctor")) && $this->has_access(ACCESS_CAN_EDIT)) {
            $this->action = end($page);
        }
        // If action file is not defined (then 404 should be given)
        if ($template == 'actions') {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
        if ($handler) {
            if ($handler == "pages") {
                // check if that file exists in teke pages or plugin pages folder
                if (is_file(dirname(dirname(__FILE__))."/views/pages/".$page[0].".html") || is_file(dirname(dirname(__FILE__))."/includes/".PLUGIN."/views/pages/".$page[0].".html")) {
                    $template = $page[0];
                } else {
                    $template = "page_not_found";
                }
            } else if (is_file(dirname(dirname(__FILE__))."/includes/".PLUGIN."/handler/handler_".$handler.".php") || in_array($handler, array("user", "administrate", "project", "ajax"))) {
                $hn = ucfirst($handler."Handler");
                if (class_exists($hn)) {
                    $this->handler = new $hn($page);
                    if (isset($this->handler->template)) {
                        $template = $this->handler->template;
                    }
                }
            } else {
                $template = "page_not_found";
            }
        }
        if ($template) {
            $this->view($template);
        } else {
            $this->view();
        }
    }
    
    public function execute_page() {
        $request_uri = $_SERVER["REQUEST_URI"];
        if (substr_count($request_uri, "/actions/") || substr_count($request_uri, "/includes/".PLUGIN."/actions/")) {
            return false;
        }
        if (substr_count($request_uri, "/images/")) {
            return false;
        }
        if (substr_count($request_uri, "/sounds/")) {
            return false;
        }
        if (substr_count($request_uri, "/views/js/")) {
            return false;
        }
        return true;
	}

    public function get_main_navigation_items() {
        $this->setNavigation();
        $nav = array();
        foreach ($this->navigation as $handelr => $handler_nav) {
            $nav [] = $handler_nav;
            if (isset($this->handler) && $this->handler->name == $handelr) {
                foreach ($this->handler->navigation_main as $item) {
                    $nav [] = $item;
                }
            }
        }
        return $nav;
    }

	public function get_navigation_items() {
        if (is_object($this->handler)) { 
            return $this->handler->navigation;
        }
    }
    
    public function test_to_put() {
		if (isset($_SESSION['test_to_put'])) {
			return $_SESSION['test_to_put'];
		}
		return false;
    }
    
    public function from_clipboard() {
		$cb = array();
		if (isset($_SESSION['clipboard'])) {
			$cb = $_SESSION['clipboard'];
		}
		return $cb;
    }
    
    public function to_clipboard($ids) {
		if (isset($_SESSION['clipboard'])) {
		    unset($_SESSION['clipboard']);
		}
		if (!is_array($ids)) $ids = array($ids);
		$_SESSION['clipboard'] = $ids;
    }
    
    public function clear_clipboard() {
        unset($_SESSION['clipboard']);
    }
	
	public function add_system_message($message, $type='success') {
		$messages = array();
		if (!in_array($type, array('success', 'error'))) {
			$type = "success";
		}
		if (isset($_SESSION['system_messages'])) {
			$messages = $_SESSION['system_messages'];
		}

		$messages[] = array('type' => $type, 'message' => $message);

		$_SESSION['system_messages'] = $messages;
	}
	
	public function get_system_messages() {
		$messages = array();
		if (isset($_SESSION['system_messages'])) {
			$messages = $_SESSION['system_messages'];
			unset($_SESSION['system_messages']);
		}
		return $messages;
    }
    
    public function is_logged_in() {
        if (isset($_SESSION['user'])) {
            return true;
        }
        return false;
    }

    public function is_admin_logged_in() {
        if (!$this->is_logged_in()) {
            return false;
        }
        if ($this->is_admin()) {
            return true;
        }
        return false;
    }
    
    public function is_admin() {
        return $this->has_access(9);
    }

    function has_access($level) {
        if ($this->user->level >= $level) return true;
        return false; 
    }
    
    function get_language() {
        if (isset($_SESSION['language'])) return $_SESSION['language'];
        return DEFAULT_LANGUAGE;
    }

    function send_mail($user, $subject, $message) {
        $to_name = $user->getFullName();
        $to_email = $user->email;
        $to = "{$to_name} <{$to_email}>";
        $headers  = "MIME-Version: 1.0". "\r\n";
        $headers .= "Content-type: text/plain; charset=UTF-8" . "\r\n";
        $headers .= "From:" . SITE_NAME . " <noreply@htk.tlu.ee>" . "\r\n";
        return mail($to, $subject, $message, $headers);
    }

    function generate_random_string($len, $chars = 'abcdefghijklmnopqrstuwxyz0123456789') {
        $string = '';
        for ($i = 0; $i < $len; $i++) {
            $pos = mt_rand(0, strlen($chars)-1);
            $string .= $chars{$pos};
        }
        return $string;
    }

    function get_site_url() {
        return WWW_ROOT;
    }

    function getAvailableRoles() {
        return array(
            '1' => _("Guest"),
            '5' => _("Member"),
            '9' => _("Admin")
        );
    }

    function getRoleNameFromId($role) {
        $roles = $this->getAvailableRoles();
        if (array_key_exists($role, $roles)) {
            return $roles[$role];
        }
        return $role;
    }

    function getAvailableLanguages() {
        return array(
            'et' => _("Estonian"),
            'ru' => _("Russian"),
            'en' => _("English"),
            //'fi' => _('Finnish'),
            //'sv' => _('Swedish'),
        );
    }

    function getLanguageNameFromId($lang) {
        $languages = $this->getAvailableLanguages();
        if (array_key_exists($lang, $languages)) {
            return $languages[$lang];
        }
        return $lang;
    }

    public function getFacebookLoginURL() {
        $helper = new FacebookRedirectLoginHelper(WWW_ROOT . "actions/login.php");
        return $helper->getLoginUrl(array('email'));
    }

    public function getFacebookLogoutURL() {
        $session = FacebookSession::newAppSession();
        $helper = new FacebookRedirectLoginHelper(WWW_ROOT . "actions/login.php");
        return $helper->getLogoutUrl($session, WWW_ROOT . "actions/logout.php");

    }

    public function getTranslatedWelcomeImageURL() {
        $current = $this->get_language();
        if (array_key_exists($current, $this->getAvailableLanguages())) {
            return WWW_ROOT . "views/graphics/welcome_{$current}.png";
        }
        return WWW_ROOT . "views/graphics/welcome_et.png";
    }

}
