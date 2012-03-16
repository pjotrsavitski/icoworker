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
        $fields = array('title' => true, 'goal' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if (in_array($key, array('goal'))) {
                $inputs[$key] = force_plaintext($inputs[$key]);
            }
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            if (Project::update($project, $inputs['title'], $inputs['goal'])) {
                $project = new Project($project->getId());          
                $response->addData('title', $project->getTitle());
                $response->addData('goal', $project->getGoal());
                $response->setStateSuccess();
                $TeKe->add_system_message(_("Project changed."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("Project could not be changed."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
?>
