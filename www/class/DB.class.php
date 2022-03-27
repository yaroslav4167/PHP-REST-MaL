<?php
/*
 * Работа с базой данных (PDO)
*/

class DB
{

    private $dbh;
    private $error;
    private $stmt;

    public function __construct()
    {
        //Init config
        $cfg = CONFIG;
        // Set DSN
        $dsn = 'mysql:host=' . $cfg['db']['host'] . ';dbname=' . $cfg['db']['db_name'] . ';port=' . $cfg['db']['port'] . ';charset=' . $cfg['db']['charset'];
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try {
            $this->dbh = new PDO ($dsn, $cfg['db']['login'], $cfg['db']['pass'], $options);
        }        // Catch any errors
        catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function __destruct() {
       @$this->dbh = null;
    }

    // Prepare statement with query
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value) :
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value) :
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value) :
                    $type = PDO::PARAM_NULL;
                    break;
                default :
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement

    public function resultset()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get result set as array of objects

    public function execute()
    {
        return $this->stmt->execute();
    }

    // Get single record as object

    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get record row count
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    // Returns the last inserted ID
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    // Returns column of data (for COUNT(*) etc.)
    public function fetchColumn()
    {
        $this->execute();
        return $this->stmt->fetchColumn();
    }

}

?>
