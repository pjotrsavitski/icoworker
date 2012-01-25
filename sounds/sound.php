<?php

require_once(dirname(dirname(__FILE__))."/engine/engine.php");

$sound_id = get_input("id", false);
if (is_numeric($sound_id)) {
    $sound = new Sound($sound_id);
    $fn = $sound->name;
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");  
    header("Content-size: ".$sound->size);
    header('Content-Disposition: filename='.$fn);
    header("Content-type: ".$sound->type);
    readfile(DATA_STORE . $fn);
} else {
    return "";
}
?>
