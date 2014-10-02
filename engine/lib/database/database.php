<?php
    
    class DB {
        
        function __construct() {
            $this->connect();
        }
        
        function connect() {
            $this->link = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
            mysqli_set_charset($this->link, "UTF8");
            
            if (!$this->link) {
                die('Could not connect: ' . mysqli_error($this->link));
            } else if (!mysqli_select_db($this->link, DB_NAME)) {
                if ($this->query("CREATE DATABASE " . DB_NAME . ";")) {
                    mysqli_select_db($this->link, DB_NAME);
                    $this->execute_sql("teke.sql");
                    forward("index.php");   
                }
            }
        }

        function disconnect() {
            mysqli_close($this->link);
        }
        
        function execute_sql($sql) {
            $sql_script_file = dirname(__FILE__) . '/'.$sql;
            if (is_file($sql_script_file) AND $script = file_get_contents($sql_script_file)) {
		        $errors = array();
                $script = preg_replace('/\-\-.*\n/', '', $script);
		        $sql_statements =  preg_split('/;[\n\r]+/', $script);
		        foreach($sql_statements as $statement) {
		            $statement = trim($statement);
		            if ($statement[0]!="#") {
    			        $statement = str_replace("prefix_", DB_PREFIX, $statement);
    			        if (!empty($statement)) {
    				        try {
    					        $result = $this->query($statement);
    				        } catch (DatabaseException $e) {
    					        $errors[] = $e->getMessage();
    				        }
    			        }
		            }
		        }
		        if (!empty($errors)) {
			        $errortxt = "";
			        foreach($errors as $error)
				        $errortxt .= " {$error};";
			        // error
		        }
	        } else {
		        // error
	        }
        }

        function query($query)
        {
            $ret = mysqli_query($this->link, $query) or print(mysqli_error($this->link)." with query: ".$query);
            return $ret;
        }

        /**
         * Excapes string for database queries.
         * Used database escape function.
         * @param string $string String to be escaped
         * @return string
         */
        function real_escape_string($string) {
            return mysqli_real_escape_string($this->link, $string);
        }

        /**
         * Returns last inserted id.
         * Uses database function.
         * @return int|string
         */
        function insert_id() {
            return mysqli_insert_id($this->link);
        }

        /**
         * Returns number of affected rows.
         * Uses database function.
         * @return int|string
         */
        function affected_rows() {
            return mysqli_affected_rows($this->link);
        }
    
    }

