<?php
/**
 * Query database with method chaining.
 * Class has an automated close and cleaning functionality.
 */
class Database
{
  /**
   * Current server location. Default: localhost.
   *
   * @var  string  Current server location. Default: localhost.
   */
  private $_location = '';

  /**
   * Database users password.
   *
   * @var  string  Database users password.
   */
  private $_pass = '';

  /**
   * Database to select.
   *
   * @var  string  Database to select.
   */
  private $_db = '';

  /**
   * Database user.
   *
   * @var  string  Database user.
   */
  private $_user = '';

  /**
   * Flag to mark if database connection is ok.
   * Used to determine if methods can be executed or run the 'auto-clean/close'.
   *
   * @var  boolean  Flag to mark if database connection is ok.
   */
  private $_connectionOk = false;

  /**
   * Flag to mark if SQL query is ok.
   * Used to determine if methods can be executed or run the 'auto-clean/close'.
   *
   * @var  boolean  Flag to mark if SQL query is ok.
   */
  private $_queryOk = false;

  /**
   * Database connection object.
   *
   * @var  object  Database connection object.
   */
  private $_Connection = null;

  /**
   * Database selection object.
   *
   * @var  object  Database selection object.
   */
  private $_SelectDb = null;

  /**
   * SQL query result object or error.
   *
   * @var  object  SQL query result object or error.
   */
  private $_Query = null;


  // -------------------------------------------------------------------------------------------------------------------------------- Public

  /**
   * Constructor is unused.
   */
  public function __construct () {}

  /**
   * Set database access.
   * Could be done in constructor but keeps this class independent to use.
   *
   * @param   string  $location  Server location.
   * @param   string  $pass      Database password.
   * @param   string  $db        Database name.
   * @param   string  $user      Database user - If undefined, set this to database name.
   * @return  object  $this      Class instance for chaining.
   */
  public function setDbAccess ($location, $pass, $db, $user = '')
  {
    $this->_location = $location;
    $this->_pass     = $pass;
    $this->_db       = $db;
    $this->_user     = $user ? $user : $db;

    return $this;
  }


  // ------------------------------------------------------------------------------------------------------------------------------- Private
  // ------------------------------------------------------------------------------------------------------- Close, Connect, Query

  /**
  * Global auto-clean/close method.
  * Try to close databse connection, free query results and reset properties.
  *
  * @return  object  $this  Class instance for chaining.
  */
  public function close ()
  {
    // Free query and reset properties
    if ($this->_Query !== null) {
      if ($this->_Query !== false) {
        mysql_free_result($this->_Query);
      }

      $this->_queryOk = false;
      $this->_Query   = null;
    }

    // Reset database selection object
    if ($this->_SelectDb !== null) {
      $this->_SelectDb = null;
    }

    // Close connection
    if ($this->Connection !== null) {
      mysql_close($this->Connection);

      $this->_connectionOk = false;
      $this->Connection    = null;
    }

    return $this;
  }

  /**
   * Try to connect to database and store object.
   * Set 'ok'-flag on success or call 'auto-clean/close'.
   *
   * @return  object  $this  Class instance for chaining.
   */
  public function connect ()
  {
    if ($this->Connection = @mysql_connect($this->_location, $this->_user, $this->_pass)) {

      if ($this->_SelectDb = @mysql_select_db($this->_db)) {

        $this->_connectionOk = true;
        return $this;
      }
    }

    return $this->close();
  }

  /**
   * Query database and return result or error.
   * Set 'ok'-flag on success or call 'auto-clean/close'.
   *
   * @param   string  $query  MySQL query string.
   * @return  object  $this   Class instance for chaining.
   */
  public function query ($query = '')
  {
    if ($this->_connectionOk) {

      if ($this->_Query = @mysql_query($query)) {

        $this->_queryOk = true;
        return $this;
      }
    }

    return $this->close();
  }


  // --------------------------------------------------------------------------------------------------------------- Format result

  /**
   * Write query result to associative array and call 'auto-clean/close' afterwards.
   * This must be the last function executed as it does not allow further method chaining.
   *
   * @param   string  $query  MySQL query string.
   * @return  mixed           Associative array or boolean false.
   */
  public function toArray ()
  {
    if ($this->_queryOk && !empty($this->_Query)) {
      $result = [];

      while ($item = mysql_fetch_array($this->_Query, MYSQL_ASSOC)) {
        $result[] = $item;
      }

      $this->close();
      return $result;
    }

    $this->close();
    return false;
  }
}
