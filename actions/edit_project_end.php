<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $project = ProjectManager::getProjectById(get_input('project_id'));

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        // Define fields array
        $fields = array('end_date' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if (in_array($key, array('end_date'))) {
                $inputs[$key] = strtotime($inputs[$key]);
            }
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
        }

        /**
         * Check date
         * 1. More than project start
         * 2. More than latest milestone_date for Milestone
         * 3. More than latest comment_date for Comment
         * 4. More than latest created for DocumentVersion
         * 5. TODO Additional checks may apply when new typed are added to timeline
         */
        $date_allowed = true;
        if (! ($inputs['end_date'] > strtotime($project->getStartDate())) ) {
            $date_allowed = false;
        }
        // Milestone check
        if ($date_allowed) {
            $q = "SELECT COUNT(id) AS count FROM " . DB_PREFIX . "milestones WHERE FROM_UNIXTIME({$inputs['end_date']}) < milestone_date";
            $result = query_row($q);
            if ((int)$result->count > 0) {
                $date_allowed = false;
            }
        }
        // Comment check
        if ($date_allowed) {
            $q = "SELECT COUNT(id) AS count FROM " . DB_PREFIX . "project_comments WHERE FROM_UNIXTIME({$inputs['end_date']}) < comment_date";
            $result = query_row($q);
            if ((int)$result->count > 0) {
                $date_allowed = false;
            }
        }
        // Document Version check
        if ($date_allowed) {
            $q = "SELECT COUNT(id) AS count FROM " . DB_PREFIX . "document_versions WHERE FROM_UNIXTIME({$inputs['end_date']}) < created";
            $result = query_row($q);
            if ((int)$result->count > 0) {
                $date_allowed = false;
            }
        }

        // XXX Send value to console, set to false, MISSING update_end_date method
        error_log((int)$date_allowed);
        $date_allowed = false;

        if (!$date_allowed) {
            $TeKe->add_system_message(_("Chosen date does not suit. It is either lower than the project start date or some of the elements are placed after chosen date."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if (sizeof($response->getErrors()) == 0) {
            if (Project::update_end_date($project, $inputs['end_date'])) {
                $response->setStateSuccess();
                $TeKe->add_system_message(_("Project end date changed."));
            }
        } else {
            $TeKe->add_system_message(_("Project end date could not be changed."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
?>
