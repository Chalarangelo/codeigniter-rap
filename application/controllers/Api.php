<?php
require __DIR__.'/../../db.php';
require __DIR__.'/../../jwt.php';

class Api extends CI_Controller {

  private static $users = array(
    "table" => "`users`",
    "create_fields" => ["`username`", "`password`", "`email`"],
    "create_types" => "sss",
    "read_fields" => "`username`, `user_id` AS `id`",
    "read_key" => "`username`"
  );

  /*
   * Index method for completeness. Returns a JSON response with an
   * error message.
   */
  public function index(){
    header('Content-Type: application/json');
    echo(json_encode(array('message'=>'No resource specified.')));
  }

  /*
   * Method for getting `user` resources.
   * Parameters:
   *  $param - (Optional) The parameter used to uniquely identify the
   *    specific resource. If nothing is specified, all 'user'
   *    resoures will be listed.
   */
  public function users($param=''){
    header('Content-Type: application/json');
    if($this->input->method(true) == 'POST'){
      echo json_encode($this->createResourceElement(
        $this::$users["table"], $this::$users["create_fields"], $this::$users["create_types"],
        [$_POST["username"], $_POST["password"], $_POST["email"]]
      ));
      return;
    }
    if($param == ''){
      echo json_encode($this->readResourceRoot(
        $this::$users["table"], $this::$users["read_fields"]
      ));
      return;
    }
    else{
      $data = $this->readResourceElement(
          $this::$users["table"], $this::$users["read_fields"], $this::$users["read_key"],
          $param
      );
      if($data == false)
        echo(json_encode(array('message'=>'No user with this username.')));
      else
        echo json_encode($data[0]);
    }
  }

  /*
   * Creates a new entry in a collection, using a query to the
   * database.
   * Parameters:
   *  $table - The database table to query.
   *  $input_fields - An array of names for the fields to be filled.
   *  $input_types - A string that contains one or more characters
   *    which specify the types for the corresponding fields.
   *  $input_values - An array of values for the fields to be filled.
   */
  private function createResourceElement($table, $input_fields, $input_types, $input_values){
    $input_fields_count = count($input_fields);
    $input_values_count = count($input_fields);

    if ($input_fields_count != $input_values_count)
      return array("message"=>"The number of values provided does not match the number of fields.");

    $input_fields_squashed = implode(",", $input_fields);
    $input_fields_questionmarks = implode(",",array_fill(0,$input_values_count,"?"));

    return database_query(
      "INSERT INTO ".$table." (".$input_fields_squashed.") VALUES (".$input_fields_questionmarks.")",
      $input_types, $input_values,
      true
    );
  }

  /*
   * Lists the members of a collection, using a query to the
   * database.
   * Parameters:
   *  $table - The database table to query.
   *  $fields - The table's fields to be retrieved.
   */
  private function readResourceRoot($table, $fields){
    return database_no_args_query(
      "SELECT ".$fields." FROM ".$table
    );
  }
  /*
   * Retrieves a specific member of a collection, using a query to
   * the database.
   * Parameters:
   *  $table - The database table to query.
   *  $fields - The table's fields to be retrieved.
   *  $element_key - The table's field that will be used for
   *    the specific resource's indentification.
   *  $key_value - The value to be used for the specific resource's
   *    identification.
   */
  private function readResourceElement($table, $fields, $element_key, $key_value){
    return database_query(
      "SELECT ".$fields." FROM ".$table." WHERE ".$element_key."=?",
      "s", [$key_value]
    );
  }
}

?>
