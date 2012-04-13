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
    teke.add_translation('button_edit', '<?php echo _('Edit'); ?>');
    teke.add_translation('text_click_to_add_comment', '<?php echo _('Click to add Comment'); ?>');
    teke.add_translation('title_change_project_beginning', '<?php echo _('Change Project Start Date'); ?>');
    teke.add_translation('title_change_project_end', '<?php echo _('Change Project End Date'); ?>');
    teke.add_translation('title_add_task_to_timeline', '<?php echo _('Add Task to Timeline'); ?>');
    teke.add_translation('message_document_is_finished', '<?php echo _('This document is finished and can not be modified!'); ?>');
    teke.add_translation('message_document_is_dropped', '<?php echo _('This document is dropped and can not be modified!'); ?>');
    teke.add_translation('confirmation_remove_task_from_timeline', '<?php echo _('Are you sure you want to remove this task from timeline ? Task itself will not be deleted.'); ?>')
    teke.add_translation('title_remove_from_timeline', '<?php echo _('Remove from timeline'); ?>');
    teke.add_translation('title_slide_to_change_time_scale', '<?php echo _('Slide to change time scale'); ?>');
    teke.add_translation('title_edit', '<?php echo _('Edit'); ?>');
    teke.add_translation('title_delete', '<?php echo _('Delete'); ?>');
});
