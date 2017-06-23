<?php

  /*
   * Establishes a connection to the database.
   * The connection's parameters (host, username, password and database name)
   * are parsed from the `config.ini` file, residing in the same folder.
   */
  function database_connect(){
    static $connection;   // Avoids multiple connections
    if(!isset($connection)) {
      $config = parse_ini_file('config.ini');
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

  /*
   * Uses a prepared statement to query the database, returning an associative
   * array or false, based on the query's results.
   * Parameters:
   *  $query - The query to the database, as a string.
   *  $types - A string that contains one or more characters which specify the
   *    types for the corresponding bind variables.
   *  $params - An array of values that will be passed as parameters to the
   *    query. The types of the parameters must match the types specified by
   *    $types.
   */
  function database_query($query, $types, $params){
    $connection = database_connect();
    $statement = mysqli_prepare($connection, $query);
    $refs = array();
    foreach($params as $key => $value)
      $refs[$key] = &$params[$key];
    call_user_func_array("mysqli_stmt_bind_param",array_merge(array($statement, $types),$refs));
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = array();

    if ($result === false)
      return false;
    while ($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    return $rows;
    }
  }

  function database_error() {
    $connection = db_connect();
    return mysqli_connect_error($connection);
  }

?>
