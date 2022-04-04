<?php

require('database/connect.php');

class search {

    private $dbConnection;

    function __construct() {
        $this->dbConnection = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
    }

    function __destruct() {
        $this->dbConnection->close();
    }

    public function query($data) {
        $result = array();
        $dbConnection = $this->dbConnection;
        $statement = $dbConnection->prepare("select id, name, type, parent_id, level FROM files WHERE name LIKE '%".$data."%'");

        $statement->execute();
        $statement->bind_result($id, $name, $type, $parent_id, $level);
        while ($statement->fetch()) {
            $line = new stdClass;
            $line->id = $id;
            $line->name = $name;
            $line->type = $type;
            $line->parent_id = $parent_id;
            $line->level = $level;
            $result[] = $line;
        }
        $statement->close();
        
        $result = $this->makeJoin($result); 
        return $result;
    }

    public function makeJoin($arrayOfObjects)
    {
        $paths = [];
        foreach ($arrayOfObjects as $object) {
            $path = 'C:';
            $path .= $this->performRecursion($object, $path);
            $paths[] = $path . '\\' . $object->name;
        }

        return $paths;
    }
   
    public function performRecursion($object, $path)
    {
        if (is_null($object->parent_id)) {
            return '';
        }

        $dbConnection = $this->dbConnection;
        $statement = $dbConnection->prepare("SELECT id, name, parent_id, level, type FROM files WHERE id = '$object->parent_id'");

        $statement->execute();
        $statement->bind_result($id,$name,$parent_id,$level,$type);
        $statement->fetch();
        $row = new stdClass;
        $row->id = $id;
        $row->name = $name;
        $row->parent_id = $parent_id;
        $row->level = $level;
        $row->type = $type;        
           
        $statement->close();    
        return $this->performRecursion($row, $path). '\\'. $row->name;
    }

    public function save($path)
    {
        

        $check_exist = mysqli_query($this->dbConnection, "SELECT * from files");
        if ($check_exist->num_rows != false || $check_exist->num_rows > 0) { 
            return $check_exist;
        }
                
        $db = "CREATE TABLE `files` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `type` varchar(255) NOT NULL,
            `parent_id` int(11) NULL,
            `level` int(11) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db_create = mysqli_query($this->dbConnection, $db);

            // read data
            $file = fopen($path, "r") or die("Unable to open file!");
            while (!feof($file)) {
                $line = fgets($file);
                $line = trim($line);
                $line = str_replace("C:\\", "", $line);
                $line = str_replace("\\", "/", $line);
                $pathParts = pathinfo("$line");     
                $dirsAndFiles = explode("/", $pathParts['dirname']);                
                $lastID = null;
                $count = 0;
                while (count($dirsAndFiles) > 0) {                    
                    $dirOrFile = array_shift($dirsAndFiles);
                    $lastID = $this->saveToDB($dirOrFile, 'folder', $lastID, $count);
                    $count++;                    
                }
                if (isset($pathParts['extension'])) {
                    $this->saveToDB($pathParts['basename'], 'file', $lastID, $count);
                }
            }
            fclose($file);
    }
		
    /**
     * Saving to database
     **/
	public function saveToDB($dirOrFile, $type, $lastID = null, $level = 0)
    {
        $check_exist = mysqli_query($this->dbConnection, "SELECT * from files WHERE name = '$dirOrFile' and level = '$level'");
        $row = mysqli_fetch_object($check_exist);
        if ($check_exist->num_rows > 0) {
            return $row->id;
        }      
        
        $lastID = $this->dbConnection->query("INSERT INTO files(name,type,parent_id,level) 
        VALUES('$dirOrFile','$type','$lastID','$level')");
        return $lastID;

    }
}



?>