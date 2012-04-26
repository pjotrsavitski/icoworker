<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $task = ProjectManager::getTaskById(get_input('task_id'));

        if (!($task instanceof Task)) {
            $TeKe->add_system_message(_("No such task."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = $task->getProjectObject();

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        // Define fields array
        $fields = array('title' => true, 'description' => false);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            if (Task::update($task, $inputs['title'], $inputs['description'])) {
                // Get a fresh copy of modified object
                $task = new Task($task->getId());
                $task_data = array(
                    'id' => $task->getId(),
                    'title' => $task->getTitle(),
                    'description' => $task->getDescription()
                );
                $response->addData('task', $task_data);
                $response->setStateSuccess();
                $TeKe->add_system_message(_("Task changed."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("Task could not be changed."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
