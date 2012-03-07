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

        // Allowed during project active period
        if (!($project->isActiveProject())) {
            $TeKe->add_system_message(_("Project is either not active yet or already finished."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        // Define fields array
        $fields = array('title' => true, 'url' => false);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            $creator = $TeKe->user->getId();
            if ($created_id = Document::create($creator, $project->getId(), $inputs['title'], $inputs['url'])) {
                $document = new Document($created_id);
                $versions = array();
                $response->addData('id', $document->getId());
                $response->addData('title', $document->getTitle());
                $response->addData('url', $document->getUrl());
                $response->addData('created', format_date_for_js($document->getCreated()));
                $response->addData('versions', $document->getVersions());
                $response->setStateSuccess();
                $TeKe->add_system_message(_("New document added."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("New document could not be added."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
