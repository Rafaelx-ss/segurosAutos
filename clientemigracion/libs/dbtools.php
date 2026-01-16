<?php


class Database{
  
    // specify your own database credentials
    private $host;
    private $db_name;
    private $username;
    private $password;
	private $charset;
    public $type;
    public  $dsn;
    public $conn;
    public $fecha;
	public $msg;
	public $resultado;
    

    // constructor
    
  
    public function __construct($configname,$rutaconfig = '../config',$conexionId='')
    {
		$this->conn=NULL;
        $filename = $rutaconfig . "/$configname.php";
        if(file_exists($filename))
        {
            try{
                $dataparam = include $filename;
                
                $this->dsn = $dataparam['dsn'];
				$this->username = $dataparam['username'];
				$this->password = $dataparam['password'];
				$this->charset = $dataparam['charset'];
				$this->type = substr($this->dsn,0,strpos($this->dsn,":"));
				$this->db_name = substr($this->dsn,strpos($this->dsn,"dbname=")+7); 
				
				$this->fecha = 'NOW()';
				if($this->type == 'sqlsrv')
				{
					$this->fecha = 'GETDATE()';
				}
				$this->resultado = $this->creaConexion();

            }catch(Exception $exception){
                $this->$msg = "error en archivo de conexion: " . $filename . "," . $exception->getMessage();
				$this->resultado = false;
            }

        } 
        else
        {
            $this->$msg = "archivo de conexion no encontrado:" . $filename;
			$this->resultado=false;
        }
		
        
    }

    public function creaDsn($host,$database,$usuario,$passwd,$tipo){
       
		
    }
	
	public function creaConexion(){
		try{
            $this->conn = new PDO($this->dsn , $this->username, $this->password,
				 array(
					PDO::ATTR_TIMEOUT => 60, // in seconds
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $this->charset
				));
            //$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->resultado = true;
				return true;
        }catch(PDOException $exception){
            $this->msg = "Error de conexión: " . $exception->getMessage();
			$this->resultado = false;
			return false;
        }
  
        
		
	}
		
	public function cierraConexion(){
		$this->conn = null;
	}

    // get the database connection
    public function getConnection(){
        
        return $this->conn;
    }
	
	function __destruct(){
		$this->cierraConexion();
	}
}
?>