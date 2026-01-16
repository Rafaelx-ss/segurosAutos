<?php
class Database{
  
    // specify your own database credentials
    private $host;
    private $db_name;
    private $username;
    private $password;
	private $charset;
    public $tipo_servidor;
    public  $dsn;
    public $conn;
    public $fecha;
    

    // constructor
    
  
    public function __construct($configname='db'){
        if($configname){
            $filename = "../config/$configname.php";
            try{
                $parsed = include $filename;
                
				$dataparam = $parsed;
				$this->dsn = $dataparam['dsn'];
				$this->username = $dataparam['username'];
				$this->password = $dataparam['password'];
				$this->charset = $dataparam['charset'];
                /*$this->creaDsn($dataparam['host'],$dataparam['database'],$dataparam['username'],$dataparam['password'],
                    $dataparam['adapter']);	*/
            }catch(PDOException $exception){
                echo "configuration error: " . $exception->getMessage();
            }

        }
        else {
            $this->creaDsn('localhost','bonobolugayl','root','','mysql');
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
              echo $this->dsn = "mysql:host=$this->host;dbname=$this->db_name"; 
              $this->fecha = 'NOW()';
          }
    }

    // get the database connection
    public function getConnection(){
        
        $this->conn = null;

        try{
            $this->conn = new PDO($this->dsn , $this->username, $this->password,
			array(
					PDO::ATTR_TIMEOUT => 60, // in seconds
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->charset
				));
           // $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>