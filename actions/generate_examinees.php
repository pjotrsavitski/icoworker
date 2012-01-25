<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $range_start = get_input('range_start');
    $range_end = get_input('range_end');
    $group_id = get_input('group');
    $group_name = get_input('group_name');
    
    $input_values = array();
    $input_values['range_start'] = $range_start;
    $input_values['range_end'] = $range_end;
    $input_values['group_id'] = $group_id;
    $input_values['group_name'] = $group_name;
    $_SESSION['input_values'] = $input_values;

	if (empty($range_start) || empty($range_end)) {
		$TeKe->add_system_message(_("At least one of the parameters is empty."), 'error');
        forward("examinees/generate");
    } else if ($range_start > $range_end) {
        $TeKe->add_system_message(_("Range start cannot be bigger than range end."), "error");
        forward("examinees/generate");
    /*} else if (!is_int($range_start) || !is_int($range_end)) {
        $TeKe->add_system_message(_("Range start and end has to be numeric values."), "error");
        forward("examinees/view");*/
    } else if ($group_id == "new" && empty($group_name)) {
        $TeKe->add_system_message(_("Please insert group name."), "error");
        forward("examinees/generate");
    } else {
        if ($group_id == "new") {
            $group = new Group();
            $user = $_SESSION['user'];
            $group_id = $group->create($user, $group_name);
        }
        $examinees = new Examinees();
        if ($examinees->create_examinees($range_start, $range_end, $group_id)) {
            $TeKe->add_system_message(_("Success."));
        } else {
            $TeKe->add_system_message(_("Failed."), 'error');
            forward("examinees/generate");
        }
    }

    unset($_SESSION['input_values']);
    forward("examinees/view");
?>

