<?php
class Database{
  
    // specify your own database credentials
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $tipo_servidor;
    public  $dsn;
    public $conn;
    public $fecha;
    

    // constructor
    
  
    public function __construct($configname='mysql'){
		require dirname(__DIR__).'/mcript/mcript.php';
		if($configname){
            $filename = dirname(__DIR__)."/config/".$configname.".json";
            try{
                $filecontent = file_get_contents($filename);
                $parsed = json_decode($filecontent);
				
                $this->creaDsn($parsed->host,$strShow($parsed->database),$strShow($parsed->username),$strShow($parsed->password),
                    $parsed->adapter);	
            }catch(PDOException $exception){
                echo "configuration error: " . $exception->getMessage();
            }

        }
        else {
            $this->creaDsn('localhost',$strShow('ftK6znhxVzFVE50/kZxOtQ=='),$strShow('wqDy6qoe+A5iOJwZV5KxRw=='),$strShow('us6MVhKBhtnSPOP509m2zg=='),'mysql');
        }
        
        
        
    }

    public function creaDsn($host,$database,$usuario,$passwd,$tipo){
        $this->host = $host;
        $this->db_name = $database;
        $this->username = $usuario;
        $this->password = $passwd;
        $this->tipo_servidor = $tipo;
	
        if ($this->tipo_servidor == 'sqlsrv' ){
            $this->dsn = "sqlsrv:server=$this->host;database=$this->db_name";
            $this->fecha='GETDATE()';
          }
          else {
              $this->dsn = "mysql:host=$this->host;dbname=$this->db_name"; 
              $this->fecha = 'NOW()';
          }
    }

    // get the database connection
    public function getConnection(){
       
        $this->conn = null;

        try{
            $this->conn = new PDO($this->dsn , $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>