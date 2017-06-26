<?php
/**
 * Restful API helper, implementing generic CRUD methods.
 * The helper relies heavily upon the Database and Error Code helpers
 * provided with it.
 * ---------------------------------------------------------------------
 */

/**
 * Creates a new collection, using a query to the database.
 * @return a JSON object with an error message
 */
function createResourceRoot(){
  $ci =& get_instance(); $ci->load->helper('error_code');
  return array(
    "code" => PROHIBITED,
    "message"=>"Creating a new resource collection is prohibited."
  );
}

/**
 * Creates a new entry in a collection, using a query to the database.
 * @param $table - The database table to query.
 * @param $input_fields - An array of names for the fields to be filled.
 * @param $input_types - A string that contains one or more characters
 *  which specify the types for the corresponding fields.
 * @param $input_values - An array of values for the fields to be filled.
 * @return the id of the newly-created collection member
 */
function createResourceElement($table, $input_fields, $input_types, $input_values){
  // Load the appropriate helpers
  $ci =& get_instance(); $ci->load->helper('database'); $ci->load->helper('error_code');
  $input_fields_count = count($input_fields);
  $input_values_count = count($input_fields);

  if ($input_fields_count != $input_values_count)
    return array(
      "code" => BAD_DATA,
      "message"=>"The number of values provided does not match the number of fields."
    );

  $input_fields_squashed = implode(",", $input_fields);
  $input_fields_questionmarks = implode(",",array_fill(0,$input_values_count,"?"));

  return database_query(
    "INSERT INTO ".$table." (".$input_fields_squashed.") VALUES (".$input_fields_questionmarks.")",
    $input_types, $input_values,
    "INSERT"
  );
}

/**
 * Lists the members of a collection, using a query to the database.
 * @param $table - The database table to query.
 * @param $fields - The table's fields to be retrieved.
 * @return a collection of elements
 */
function readResourceRoot($table, $fields){
  // Load the appropriate helpers
  $ci =& get_instance(); $ci->load->helper('database');
  return database_no_args_query(
    "SELECT ".$fields." FROM ".$table
  );
}

/**
 * Retrieves a specific member of a collection, using a query to the database.
 * @param  $table - The database table to query.
 * @param $fields - The table's fields to be retrieved.
 * @param $element_key - The table's field that will be used for the specific
 *  resource's indentification.
 * @param $key_value - The value to be used for the specific resource's
 *  identification.
 * @return a list of members or false
 */
function readResourceElement($table, $fields, $element_key, $key_value){
  // Load the appropriate helpers
  $ci =& get_instance(); $ci->load->helper('database');
  return database_query(
    "SELECT ".$fields." FROM ".$table." WHERE ".$element_key."=?",
    "s", [$key_value]
  );
}

/**
 * Updates a resource collection, using a query to the database.
 * @return a JSON object with an error message
 */
function updateResourceRoot(){
  // Load the appropriate helpers
  $ci =& get_instance(); $ci->load->helper('error_code');
  return array(
    "code" => PROHIBITED,
    "message"=>"Updating a resource collection is prohibited."
  );
}

/**
 * Updates a specific member of a collection, using a query to the database.
 * @param  $table - The database table to query.
 * @param $input_fields - An array of names for the fields to be updated.
 * @param $input_types - A string that contains one or more characters
 *  which specify the types for the corresponding fields.
 * @param $input_values - An array of values for the fields to be updated.
 * @param $element_key - The table's field that will be used for the specific
 *  resource's indentification.
 * @param $key_value - The value to be used for the specific resource's
 *  identification.
 * @return the number of affected rows
 */
function updateResourceElement($table, $input_fields, $input_types, $input_values, $element_key, $key_value){
  // Load the appropriate helpers
  $ci =& get_instance(); $ci->load->helper('database'); $ci->load->helper('error_code');
  $input_fields_count = count($input_fields);
  $input_values_count = count($input_fields);

  if ($input_fields_count != $input_values_count)
  return array(
    "code" => BAD_DATA,
    "message"=>"The number of values provided does not match the number of fields."
  );

  $input_fields_squashed = implode("=?, ", $input_fields)."=?";
  $input_values[] = $key_value;

  return database_query(
    "UPDATE ".$table." SET ".$input_fields_squashed."  WHERE ".$element_key."=?",
    $input_types."s", $input_values,
    "UPDATE"
  );
}

/**
 * Deletes a resource collection, using a query to the database.
 * @return a JSON object with an error message
 */
function deleteResourceRoot(){
  // Load the appropriate helpers
  $ci =& get_instance(); $ci->load->helper('error_code');
  return array(
    "code" => PROHIBITED,
    "message"=>"Deleting a resource collection is prohibited."
  );
}

/**
 * Deletes a specific member of a collection, using a query to the database.
 * @param  $table - The database table to query.
 * @param $element_key - The table's field that will be used for the specific
 *  resource's indentification.
 * @param $key_value - The value to be used for the specific resource's
 *  identification.
 * @return the number of affected rows
 */
function deleteResourceElement($table, $element_key, $key_value){
  // Load the appropriate helpers
  $ci =& get_instance(); $ci->load->helper('database');
  return database_query(
    "DELETE FROM ".$table." WHERE ".$element_key."=?",
    "s", [$key_value],
    "DELETE"
  );
}
?>
