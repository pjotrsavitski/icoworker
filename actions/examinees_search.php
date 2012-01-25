<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $group_id = get_input('group');
    
    $input_values = array();
    if ($group_id != 'all') {
        $input_values['group_id'] = $group_id;
    }
    $_SESSION['input_values'] = $input_values;

    forward("examinees/view");
?>

