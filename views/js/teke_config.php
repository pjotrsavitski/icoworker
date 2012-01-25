<?php 
require_once(dirname(dirname(dirname(__FILE__)))."/engine/engine.php");
header("Content-type: application/x-javascript");
?>

jQuery(document).ready(function() {
    teke.config.wwwroot = '<?php echo WWW_ROOT; ?>';
    teke.config.facebook_app_id = '<?php echo FACEBOOK_APP_ID; ?>';
});
