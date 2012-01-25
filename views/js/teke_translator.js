var JS_TRANSLATIONS = new function() {
    
    this.translations = {};
    
    this.addTranslation = function (msgid, msgstr) {
        return this.translations[msgid] = msgstr;
    };
    
    this.getTranslation = function (msgid) {
        return this.translations[msgid];
    };
}