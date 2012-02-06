<?php

class AJAXResponse {
    public $state = -1;
    public $errors = array();
    public $messages = "";
    public $forward = "";

    public function getState() {
        return $this->state;
    }

    public function setStateError() {
        $this->state = -1;
    }

    public function setStateSuccess() {
        $this->state = 0;
    }

    public function isStateError() {
        return ($this->state == -1) ? true : false;
    }

    public function isStateSuccess() {
        return ($this->state == 0) ? true : false;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function addError($error) {
        $errors = $this->getErrors();
        $errors[] = $error;
        $this->errors = $errors;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function setMessages() {
        global $TeKe;
        $messages = $TeKe->get_system_messages();
        $m_html = "";
        if (is_array($messages) && sizeof($messages)) {
            foreach ($messages as $key => $message) {
                $m_html .= "<div id=\"system_message_$key\" class=\"system_message_{$message['type']}\">{$message['message']} <span class=\"system_message_close\">" . _("Close") . "</span></div>";
            }
        }
        $this->messages = $m_html;
    }

    public function getForward() {
        return $this->forward;
    }

    public function setForward($forward) {
        $this->forward = $forward;
    }

    public function getJSON() {
        return json_encode($this);
    }
}
