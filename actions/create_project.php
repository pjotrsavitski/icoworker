<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        // Define fields array
        $fields = array('title' => true, 'goal' => true, 'start_date' => true, 'end_date' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if (in_array($key, array('start_date', 'end_date'))) {
                $inputs[$key] = strtotime($inputs[$key]);
            }
            if (!$inputs[$key]) {
                $response->addError($key);
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            $creator = $TeKe->user->getId();
            if (Project::create($creator, $inputs['title'], $inputs['goal'], $inputs['start_date'], $inputs['end_date'])) {
                $response->setStateSuccess();
                $TeKe->add_system_message(_("New project created."));
            }
        } else {
            $TeKe->add_system_message(_("New project could not be created."), 'error');
        }
        $response->setMessages();

        echo $response->getJSON();
        exit;
    }
?>
