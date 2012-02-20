<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");
        
    if (isset($_SESSION["user"])) {
        unset($_SESSION["user"]);
        if (isset($_SESSION['language'])) {
            $language = $_SESSION['language'];
        }
        session_destroy();
        session_start();
        if (isset($language)) {
            $_SESSION['language'] = $language;
        }
	} else {
		$TeKe->add_system_message(_("No authenticated user found."), 'error');
	}
    
    forward("");
?>
