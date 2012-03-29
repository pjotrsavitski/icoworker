<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();

        $document = ProjectManager::getDocumentById(get_input('document_id'));

        if (!($document instanceof Document)) {
            $TeKe->add_system_message(_("Document not found."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = ProjectManager::getProjectById($document->getProjectId());

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

        // If document is inactive, adding new versions is not allowed
        if (!$document->isActive()) {
            $TeKe->add_system_message(_("Document is inactive. Adding versions to inactive document is not allowed."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        // Define fields array
        $fields = array('title' => true, 'url' => false, 'notes' => false, 'version_type' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
            if (in_array($key, array('notes'))) {
                $inputs[$key] = force_plaintext($inputs[$key]);
            }
            if ($key == 'version_type') {
                if (!array_key_exists($inputs[$key], $document->getVersionTypes())) {
                    $inputs[$key] = 1;
                }
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            $creator = $TeKe->user->getId();
            if (Document::update($document, $inputs['title'], $inputs['url'], $inputs['notes'], $inputs['version_type'])) {
                // Refresh document instance
                $document = new Document($document->getId());
                $versions = array();
                $response->addData('id', $document->getId());
                $response->addData('title', $document->getTitle());
                $response->addData('url', $document->getUrl());
                $response->addData('notes', $document->getNotes());
                $response->addData('created', format_date_for_js($document->getCreated()));
                $response->addData('is_active', $document->isActive());
                $response->addData('end_date', format_date_for_js($document->getEndDate()));
                $response->addData('versions', $document->getVersions());
                $response->setStateSuccess();
                $TeKe->add_system_message(_("New document version added."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("New document version could not be added."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
