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
        $fields = array('title' => true, 'milestone_date' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
            if ($key == 'milestone_date') {
                $inputs[$key] = strtotime($inputs[$key]);
                // Check if date chosen fits into the project time frame
                if (!($inputs[$key] >= strtotime($project->getStartDate()) && $inputs[$key] <= strtotime($project->getEndDate()))) {
                    $response->addError($key);
                }
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            $creator = $TeKe->user->getId();
            if ($created_id = Milestone::create($creator, $project->getId(), $inputs['title'], $inputs['milestone_date'])) {
                $response->setStateSuccess();
                $TeKe->add_system_message(_("New milestone added."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("New milestone could not be added."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
