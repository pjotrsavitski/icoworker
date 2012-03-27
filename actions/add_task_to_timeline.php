<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $task = ProjectManager::getTaskById(get_input('task_id'));

        if (!($task instanceof Task)) {
            $TeKe->add_system_message(_("No such milestone."), 'error');
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
        $fields = array('start_date' => true, 'end_date' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if (in_array($key, array('start_date', 'end_date'))) {
                $inputs[$key] = strtotime($inputs[$key]);
            }
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
        }

        // Check if dates are allowed
        if ( !( (strtotime($project->getStartDate()) <= $inputs['start_date']) && ($inputs['start_date'] <= strtotime($project->getEndDate())) && (strtotime($project->getStartDate()) <= $inputs['end_date']) && ($inputs['end_date'] <= strtotime($project->getEndDate())) && ($inputs['start_date'] < $inputs['end_date']) ) ) {
            $TeKe->add_system_message(_("Provided dates are not suitable."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if (sizeof($response->getErrors()) == 0) {
            $creator = $TeKe->user->getId();
            if ($task->addToTimeline($inputs['start_date'], $inputs['end_date'])) {
                $task_data = array(
                    'id' => $task->getId(),
                    'title' => $task->getTitle(),
                    'description' => $task->getDescription(),
                    'members' => array(),
                    'resources' => array(),
                    'start_date' => format_date_for_js($task->getStartDate()),
                    'end_date' => format_date_for_js($task->getEndDate())
                );
                $task_members = $task->getAssociatedMembers();
                if ($task_members && is_array($task_members) && sizeof($task_members) > 0) {
                    foreach ($task_members as $tmember) {
                        $task_data['members'][] = array(
                            'id' => $tmember->getId(),
                            'url' => $tmember->getURL(),
                            'fullname' => $tmember->getFullname(),
                            'image_url' => $tmember->getImageURL()
                        );
                    }
                }
                $task_resources = $task->getAssociatedResources();
                if ($task_resources && is_array($task_resources) && sizeof($task_resources) > 0) {
                    foreach ($task_resources as $tres) {
                        $task_data['resources'][] = array(
                            'id' => $tres->getId(),
                            'title' => $tres->getTitle(),
                            'resource_type_url' => $tres->getResourceTypeURL(),
                            'url' => $tres->getURL(),
                            'description' => $tres->getDescription()
                        );
                    }
                }
                $response->addData('task', $task_data);
                $response->setStateSuccess();
                $TeKe->add_system_message(_("Task added to timeline."));
            }
        } else {
            $TeKe->add_system_message(_("Task could not be added to timeline."), 'error');
        }

        $response->setMessages();
        echo $response->getJSON();
        exit;
    }
