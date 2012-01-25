<?php
class Sound {
    public $id;
    public $name;
    public $type;
    public $size;
    public $location;
    public $creator;
    public $created;
    public $sound;
    public $sound_type;
    public $file;
    private $locked = 0;
    
    function __construct($id=false) {
        if ($id && is_numeric($id)) {
            $this->id=$id;
            $this->load();
        }
    }
    
    function listen() {
        return WWW_ROOT . "sounds/sound.php?id=".$this->id;
    }
    
    function load() {
        if (is_numeric($this->id)) {
            $data = query_row("SELECT * FROM " . DB_PREFIX . "sounds WHERE id=".$this->id);
            if ($data) {
                $this->name = $data->name;
                $this->type = $data->type;
                $this->size = $data->size;
                $this->location = $data->location;
                $this->creator = $data->creator;
                $this->created = $data->created;
                $this->setLocked($data->locked);
            } else {
                $this->id = 0;    
            }
        }
    }
    
    function delete() {
        if (is_admin() || $this->creator==get_logged_in_user()->getId()) {
            $deleted = query("DELETE FROM " . DB_PREFIX . "sounds WHERE id=".$this->id);
            if ($deleted) {
                $snds = query_rows("SELECT id FROM " . DB_PREFIX . "sounds WHERE name='".$this->name."' AND size=".$this->size);
                if (!$snds){
                    unlink(DATA_STORE . $this->name);
                    return true;
                }
                return true;
            }
        }
        return false;
    }
    
    function rename() {
        preg_match("/(.*)\.(mp3|wav)/", $this->name, $name);
        $count = 1;
        while (file_exists(DATA_STORE . $name[1] . "_" . $count . "." . $name[2])) {
            $count++;
        }
        $this->name = $name[1] . "_" . $count . "." . $name[2];
    }
    
    function save_from_dir($dir, $name) {
        $this->file = $dir."/".$name;
        $this->name = strtolower($name);
        preg_match("/(.*)\.(mp3|mp2|wav|au|snd|mid|midi|m3u|)/", $this->name, $ext);
        $this->type = 'audio/x-wav';
        if (isset($ext[2]) && in_array($ext[2], array("mp2", "mp3"))) {
            $this->type = 'audio/mpeg';
        } else if (isset($ext[2]) && in_array($ext[2], array("au", "snd"))) {
            $this->type = 'audio/basic';
        } else if (isset($ext[2]) && in_array($ext[2], array("mid", "midi"))) {
            $this->type = 'audio/midi';
        } else if (isset($ext[2]) && in_array($ext[2], array("m3u"))) {
            $this->type = 'audio/x-mpegurl';
        }
        $this->size = filesize($this->file);
        return $this->save();
}
    
    function upload($file) {
        $allowed_types = array("audio/x-wav", "audio/mpeg", "audio/basic", "audio/midi", "audio/x-mpegurl");    
        if (in_array($file["type"], $allowed_types) && $file["size"] < 45000000) {   
            if ($file["error"] > 0) {
                return false;
            } else {
                $this->file = $file["tmp_name"];
                $this->name = strtolower($file["name"]);
                $this->type = $file["type"];
                $this->size = $file["size"];
                return $this->save();
            }
        } else {
            global $TeKe;
            $TeKe->add_system_message(_("Invalid sound. Type: ").$file["type"], 'error');
            return false;
        }
    }
    
    function copy($locked=false) {
        $this->creator = get_logged_in_user()->getId();
        $insert = query_insert("INSERT INTO " . DB_PREFIX . "sounds (name, type, size, location, creator) values ('".mysql_real_escape_string($this->name)."', '".mysql_real_escape_string($this->type)."', ".$this->size.", '".mysql_real_escape_string($this->location)."', ".$this->creator.")");
        if ($insert && $locked) {
            $lock = $this->lock($insert);
        }
        return $insert;
    }
    
    function lock($id=null) {
        if (!$id) $id = $this->getId();
        return query_update("UPDATE " . DB_PREFIX . "sounds SET locked=1 WHERE id=".$id);
    }

    function save() {
        $new_file = true;
        if (file_exists(DATA_STORE . $this->name)) {
            if ($this->size == filesize(DATA_STORE . $this->name)) {
                $this->location = DATA_STORE . $this->name;
                $new_file = false;
            } else {
                $this->rename();
            }
        }
        if ($new_file){
            if (!move_uploaded_file($this->file, DATA_STORE . $this->name)) {
                copy($this->file, DATA_STORE . $this->name);
            }
            $this->location = DATA_STORE . $this->name;
        }
        $filename = DATA_STORE . $this->name;
        if ($this->id > 0) {
            if (query_update("UPDATE " . DB_PREFIX . "sounds SET name='".mysql_real_escape_string($this->name)."', type='".mysql_real_escape_string($this->type)."', size=".$this->size.", location='".mysql_real_escape_string($this->location)."' WHERE id=".$this->id)) return $this->id;
        } else {
            $this->creator = get_logged_in_user()->getId();
            $insert = query_insert("INSERT INTO " . DB_PREFIX . "sounds (name, type, size, location, creator) values ('".mysql_real_escape_string($this->name)."', '".mysql_real_escape_string($this->type)."', ".$this->size.", '".mysql_real_escape_string($this->location)."', ".$this->creator.")");
            return $insert;
        }
    } 
       
    public function getLocked() {
        return $this->locked;
    }
    
    public function setLocked($locked) {
        if ($locked) {
            $this->locked = 1;
        } else {
            $this->locked = 0;
        }
    }
}
?>
