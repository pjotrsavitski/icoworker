<?php 
// TODO Possibly waiting for ready egvent is not needed
require_once(dirname(dirname(dirname(__FILE__)))."/engine/engine.php");
header("Content-type: application/x-javascript");
?>

jQuery(document).ready(function() {
    teke.config.wwwroot = '<?php echo WWW_ROOT; ?>';
    teke.config.facebook_app_id = '<?php echo FACEBOOK_APP_ID; ?>';

    /* FORMS */
    teke.add_translation('button_add', '<?php echo _('Add'); ?>');
    teke.add_translation('button_create', '<?php echo _('Create'); ?>');
    teke.add_translation('button_change', '<?php echo _('Change'); ?>');
    teke.add_translation('button_search', '<?php echo _('Search'); ?>');
    teke.add_translation('button_return', '<?php echo _('Return'); ?>');
});
