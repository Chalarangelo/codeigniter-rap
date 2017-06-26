<?php
/**
 * Gets the body of the current request.
 */
function get_request_body(){
  $input = file_get_contents('php://input');
  parse_str($input, $request);
  return $request;
}
?>
