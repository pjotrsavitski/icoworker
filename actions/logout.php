<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");
        
    if (isset($_SESSION["user"])) {
        unset($_SESSION["user"]);
        session_destroy();
        session_start();
	} else if (isset($_SESSION["examinee"])) {
        unset($_SESSION["examinee"]);
        session_destroy();
        session_start();
	} else {
		$TeKe->add_system_message(_("No authenticated user found."), 'error');
	}
    
    forward("");
?>
