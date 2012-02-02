<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        // Initialize response (think about using custom class)
        $response = array('state' => 0, 'errors' => array());
        $errors = array();
        // Defile fields array
        $fields = array('title' => true, 'goal' => true, 'start_date' => true, 'end_date' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if (in_array($key, array('start_date', 'end_date'))) {
                $inputs[$key] = strtotime($inputs[$key]);
            }
            if (!$inputs[$key]) {
                $errors[] = $key;
            }
        }

        if (sizeof($errors) == 0) {
            $creator = $TeKe->user->getId();
            $project = new Project();
            if ($project->create($creator, $inputs['title'], $inputs['goal'], $inputs['start_date'], $inputs['end_date'])) {
                $response['state'] = 1;
            }
        } else {
            $response['errors'] = $errors;
        }

        echo json_encode($response);
        exit;

    }
?>
