<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $resource = ProjectManager::getResourceById(get_input('resource_id'));

        if (!($resource instanceof Resource)) {
            $TeKe->add_system_message(_("No such resource."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = $resource->getProjectObject();

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        // Define fields array
        $fields = array('title' => true, 'description' => false, 'url' => false, 'resource_type' => false);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
            // Fall back to default value if bad resource_type is provided
            if ($key == 'resource_type') {
                if (!array_key_exists($inputs[$key], Resource::getResourceTypes())) {
                    $inputs[$key] = 1;
                }
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            if (Resource::update($resource, $inputs['title'], $inputs['description'], $inputs['url'], $inputs['resource_type'])) {
                // Get a fresh copy of modified object
                $resource = new Resource($resource->getId());
                $resource_data = array(
                    'id' => $resource->getId(),
                    'title' => $resource->getTitle(),
                    'resource_type_url' => $resource->getResourceTypeURL(),
                    'url' => $resource->getURL(),
                    'description' => $resource->getDescription()
                );
                $response->addData('resource', $resource_data);
                $response->setStateSuccess();
                $TeKe->add_system_message(_("Resource changed."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("Resource could not be changed."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
