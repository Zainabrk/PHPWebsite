<?php
/**
 * A PHP file which magages the MySQL Database functionality of the system
 */
 
/**
 * A class which magages the MySQL Database functionality of the system
 *
 * @category   System_Db
 * @package    System_Db
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
 
// http://www.html-seminar.de/html-css-php-forum/board40-themenbereiche/board18-php/4889-einfache-datenbankklasse-php-data-objects-erweiterung-pdo/

class System_Db extends PDO
{
	
	/**
     * Contains the DB Connection
     *
     * @access private
     * @var boolean
     */
	private $db = false;
	
	/**
     * Contains the SQL Object
     *
     * @access private
     * @var boolean
     */
	private $stmt = false;

	/**
     * Constructor
	 *
	 * Established the MySQL Database Connection
     *
     * @access public
     * @param string $host Hostname
     * @param string $username Username
     * @param string $password Passwort
     * @param string $database Database Name
     * @param boolean $pers_con Persistant Connection
     * @param string $init_cmd Initialization Commands
     * @return boolean true on success
     * @throws PDOException
     */
	public function __construct( $host, $username, $password, $database = null, $pers_con = false, $init_cmd = null )
    {
        try{
            $database = ($database) ? ';dbname=' . $database : ''; 

            $options = array(  
                PDO::ATTR_PERSISTENT => $pers_con,
				PDO::MYSQL_ATTR_INIT_COMMAND => $init_cmd
            );

            $this->db = new PDO('mysql:host=' . $host . $database.';charset=utf8', $username, $password, $options);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			
            return true;
			
        }catch(PDOException $e){
			
            throw new Exception($e->getMessage());
			
        }
    }
	
	/**
     * Closes the DB Connection
	 *
     * @access public
     */
    public function close()
    {
		
        $this->db = null;
		
    }
	
	/**
     * Closes the DB Connection
     * @param string $seqname Sequenz Name
     * @return int last Insert Id
	 *
     * @access public
     */
    public function lastInsertId(string $seqname = NULL ): string
    {
		
        return $this->db->lastInsertId( $seqname );
		
    }
	
	/**
     * Places quotes around the input string
     * @param string $sql SQL Query
     * @return string Returns a quoted string
	 *
     * @access public
     */
    public function escape( $sql )
	{
		
        return $this->db->quote( $sql );
		
    }
        
	/**
     * Executes a prepared statement
	 *
     * @access public
     * @return boolean false on error, true on success
     * @throws PDOException
     */
	public function exe()
	{
		
		if( !isset( $this->stmt ) || !is_object( $this->stmt ) ){

			return false;
			
		}

		try{
			#$this->stmt->debugDumpParams();
			$this->stmt->execute();

		}
		catch(PDOException $e){
			
			throw new Exception($e->getMessage());;
			
		}
		
		return true;

	}
	
	/**
     * Executes an SQL statement
	 *
     * @access public
     * @param string $sql SQL Query
     * @throws PDOException
     */
	public function query(string $sql,  ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement
	{

		$this->stmt = false;

		try{
			
			$this->stmt = $this->db->query( $sql );
			
		}
		catch(PDOException $e){
			
			throw new Exception($e->getMessage());
			
		}
	}
	
	/**
     * Prepares a statement for execution
	 *
     * @access public
     * @param string $sql SQL Query
     * @param string $options SQL Query Options
     * @return boolean true on success
     * @throws PDOException
     */
	public function prepare( $sql, $options = [] ): PDOStatement
	{
		
		$this->stmt = false;
		
		try{
			
			$this->stmt = $this->db->prepare( $sql );
			
		}
		catch(PDOException $e){
				
				throw new Exception($e->getMessage());
		
		}

		return true;
		
	}
	
	/**
     * Binds a parameter to the specified variable name
	 *
     * @access public
     * @param string $param Parameter identifier
     * @param string $var Name of the PHP variable to bind to the SQL statement parameter
     * @param string $data_type Explicit data type for the parameter
     * @param string $length Length of the data type
     * @param string $driver_options Driver Options
     * @throws PDOException
     */
	public function bind_param( $param, &$var, $data_type = NULL, $length = NULL, $driver_options = NULL )
	{
		
		try{
			
			if( $data_type !== NULL ){
				$this->stmt->bindParam($param, $var, $data_type, $length, $driver_options );
			}
			elseif( is_string( $var ) ){
				$this->stmt->bindParam($param, $var, PDO::PARAM_STR, $length, $driver_options );
			}
			elseif( is_bool( $var ) ){
				$this->stmt->bindParam($param, $var, PDO::PARAM_BOOL, $length, $driver_options );
			}
			elseif( is_null( $var ) ){
				$this->stmt->bindParam($param, $var, PDO::PARAM_NULL, $length, $driver_options );
			}
			elseif( is_numeric ($var ) ){
				$this->stmt->bindParam($param, $var, PDO::PARAM_INT, $length, $driver_options );
			}
			else{ // PDO::PARAM_FLOAT does not exist. handle float as string
				$this->stmt->bindParam($param, (string)$var, PDO::PARAM_STR, $length, $driver_options );
			}
		}
		catch(PDOException $e){
				
				throw new Exception($e->getMessage());
		
		}
		
		#$this->stmt->bindParam( $param, $var, $data_type, $length, $driver_options );
		
	}

	/**
     * Binds a parameter to the specified variable name
	 *
     * @access public
     * @param string $param Parameter identifier
     * @param string $var Name of the PHP variable to bind to the SQL statement parameter
     * @param string $data_type Explicit data type for the parameter
     * @param string $length Length of the data type
     * @param string $driver_options Driver Options
     */
	public function bind( $param, &$var, $data_type = PDO::PARAM_STR, $length = NULL, $driver_options = NULL )
	{
		
		$this->bind_param( $param, $var, $data_type, $length, $driver_options );
	
	}
	
	/**
     * Closes the DB Connection
	 *
     * @access public
     * @param string $fetchmode Controls the contents of the returned array
     * @return mixes false on error, array Result
     * @throws PDOException
     */
	public function fa( $fetchmode = "ASSOC" ){
		
		if(strtolower( $fetchmode ) == "num" ){
			
			$fetchmode = PDO::FETCH_NUM;
			
		}
		
		if(strtolower($fetchmode) == "both" ){
			
			$fetchmode = PDO::FETCH_BOTH;
			
		}
		
		else{
			
			$fetchmode = PDO::FETCH_ASSOC;
		
		}

		if( !isset( $this->stmt ) || !is_object( $this->stmt ) ){

			return false;
			
		}

		try{
			
			return $this->stmt->fetchAll( $fetchmode );
			
			unset( $this->stmt );

		}
		catch(PDOException $e){

				throw new Exception($e->getMessage());
		
		}		
	}
	
	/**
     * Fetches the next row from a result set
	 *
     * @access public
     * @return mixes false on error, array Result
     * @throws PDOException
     */
	public function fas()
	{

		if( !isset( $this->stmt ) || !is_object( $this->stmt ) ){

			return false;
			
		}

		try{
			
			return $this->stmt->fetch( PDO::FETCH_ASSOC );
		
			return $result;
			
		}
		catch(PDOException $e){
				
			throw new Exception($e->getMessage());
		
		}		
	}

	/**
     * Returns the number of rows affected by the last SQL statement
	 *
     * @access public
     * @return mixes false on error, int rowCount
     * @throws PDOException
     */
	public function nr( )
	{

		if( !isset( $this->stmt ) || !is_object( $this->stmt ) ){

			return false;
			
		}

		try{
			
			return  $this->stmt->rowCount();
			
		}
		catch(PDOException $e){
				
				throw new Exception($e->getMessage());
		
		}		
	}
}


?>