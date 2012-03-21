<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $milestone = ProjectManager::getMilestoneById(get_input('milestone_id'));

        if (!($milestone instanceof Milestone)) {
            $TeKe->add_system_message(_("No such milestone."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = $milestone->getProjectObject();

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        // Define fields array
        $fields = array('title' => true, 'milestone_date' => true, 'flag_color' => 'true', 'notes' => false);
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
            if (in_array($key, array('notes'))) {
                $inputs[$key] = force_plaintext($inputs[$key]);
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            if (Milestone::update($milestone, $inputs['title'], $inputs['milestone_date'], $inputs['flag_color'], $inputs['notes'])) {
                // Get a fresh copy of modified object
                $milestone = new Milestone($milestone->getId());
                $response->addData('id', $milestone->getId());
                $response->addData('title', $milestone->getTitle());
                $response->addData('flag_url', $milestone->getFlagColorURL());
                $response->addData('notes', $milestone->getNotes());
                $response->setStateSuccess();
                $TeKe->add_system_message(_("Milestone changed."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("Milestone could not be changed."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
