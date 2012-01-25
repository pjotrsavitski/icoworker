<?php 
require_once(dirname(dirname(dirname(__FILE__)))."/engine/engine.php");
textdomain(DEFAULT_DOMAIN);
header("Content-type: application/x-javascript");
?>

var JS_TRANSLATIONS = new function() {
    
    this.translations = {};
    
    this.addTranslation = function (msgid, msgstr) {
        return this.translations[msgid] = msgstr;
    };
    
    this.getTranslation = function (msgid) {
        return this.translations[msgid];
    };
}
// Default translations
// Translations for DELETE confirmation
JS_TRANSLATIONS.addTranslation('delete_confirm_msg','<?php echo _("Deleting! Are you sure?");?>');
JS_TRANSLATIONS.addTranslation('delete_confirm_ok','<?php echo _("Yes");?>');
JS_TRANSLATIONS.addTranslation('delete_confirm_cancel','<?php echo _("No");?>');

// Translations for password strength plugin
JS_TRANSLATIONS.addTranslation('too_short','<?php echo _("Too short");?>');
JS_TRANSLATIONS.addTranslation('weak','<?php echo _("Weak");?>');
JS_TRANSLATIONS.addTranslation('good','<?php echo _("Good");?>');
JS_TRANSLATIONS.addTranslation('strong','<?php echo _("Strong");?>');
JS_TRANSLATIONS.addTranslation('username_password_identical','<?php echo _("Username and password are identical");?>');

