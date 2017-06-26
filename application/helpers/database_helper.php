<?php

/**
 * Establishes a connection to the database.
 * The connection's parameters (host, username, password and database name)
 * are parsed from the `config.ini` file, residing in the CodeIgniter root
 * folder.
 * @return connection to the database
 */
function database_connect(){
  static $connection;   // Avoids multiple connections
  if(!isset($connection)) {
    $config = parse_ini_file(__DIR__.'/../../config.ini');
    $connection = mysqli_connect(
      $config['host'],
      $config['username'],
      $config['password'],
      $config['db_name']
    );
  }
  // Return error or connection
  if($connection === false) return mysqli_connect_error();
  return $connection;
}

/**
 * Uses a prepared statement to query the database, returning an associative
 * array or false, based on the query's results.
 * @param $query - The query to the database, as a string.
 * @param $types - A string that contains one or more characters which specify
 *  the types for the corresponding bind variables.
 * @param $params - An array of values that will be passed as parameters to
 *  the query. The types of the parameters must match the types specified by
 *  $types.
 * @param $query_type - (Optional) The type of query that will be executed
 *  ("SELECT" (default), "INSERT", "UPDATE", "DELETE"). The execution of the
 *  prepared statement will return different kinds of results based on the
 *  type specified.
 * @return associative array or false
 */
function database_query($query, $types, $params, $query_type = "SELECT"){
  $connection = database_connect();
  $statement = mysqli_prepare($connection, $query);
  $refs = array();
  foreach($params as $key => $value)
    $refs[$key] = &$params[$key];
  call_user_func_array("mysqli_stmt_bind_param",array_merge(array($statement, $types),$refs));
  mysqli_stmt_execute($statement);
  switch ($query_type) {
    case "INSERT":
      $result = mysqli_stmt_insert_id($statement);
      return array("id"=>$result);
      break;
    case "UPDATE":
    case "DELETE":
      $result = mysqli_stmt_affected_rows($statement);
      return array("rows"=>$result);
      break;
    case "SELECT":
    default:
      $result = mysqli_stmt_get_result($statement);
      $rows = array();
      if ($result === false)
        return false;
      while ($row = mysqli_fetch_assoc($result))
        $rows[] = $row;
      return $rows;
      break;
  }
}

/**
 * Queries the database, returning an associative array or false, based on
 * the query's results. The query must have no arguments (i.e. useful for
 * retrieving all values from a table).
 * @param $query - The query to the database, as a string.
 * @return associative array or false
 */
function database_no_args_query($query) {
  $connection = database_connect();
  $result = mysqli_query($connection, $query);

  if($result === false)
    return false;
  while ($row = mysqli_fetch_assoc($result))
    $rows[] = $row;
  return $rows;
}

/**
 * Returns the last connection error, if any.
 * @return error description
 */
function database_error() {
  $connection = db_connect();
  return mysqli_connect_error($connection);
}

?>
