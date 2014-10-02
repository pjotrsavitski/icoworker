<?php
    
    class DB {
        
        function __construct() {
            $this->connect();
        }
        
        function connect() {
            $this->link = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, true);
            mysql_set_charset("UTF8");
            
            if (!$this->link) {
                die('Could not connect: ' . mysql_error());
            } else if (!mysql_select_db(DB_NAME, $this->link)) {
                if ($this->query("CREATE DATABASE " . DB_NAME . ";")) {
                    mysql_select_db(DB_NAME, $this->link);
                    $this->execute_sql("teke.sql");
                    forward("index.php");   
                }
            }
        }

        function disconnect() {
            mysql_close();
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
            $ret = mysql_query($query, $this->link) or print(mysql_error()." with query: ".$query);
            return $ret;
        }

        /**
         * Excapes string for database queries.
         * Used database escape function.
         * @param string $string String to be escaped
         * @return string
         */
        function real_escape_string($string) {
            return mysql_real_escape_string($string, $this->link);
        }
    
    }

