<?php
/**
 * Creates a JWT string.
 * @param $payload - The payload of the JSON Web Token.
 * @param $key - The secret key
 * @param $algo - (Optional) The signing algorithm ('HS256' (default),
 *  'HS384' or 'HS512').
 * @return signed JWT
 */
function jwt_encode($payload, $key, $algo = 'HS256'){
  $header = array('typ' => 'JWT', 'alg' => $algo);
  $segments = array();
  $segments[] = urlsafe_base64_encode(json_e_encode($header));
  $segments[] = urlsafe_base64_encode(json_e_encode($payload));
  $signing_input = implode('.', $segments);
  $signature = sign($signing_input, $key, $algo);
  $segments[] = urlsafe_base64_encode($signature);
  return implode('.', $segments);
}

/**
 * Decodes a JWT string.
 * @param $jwt - The JSON Web Token.
 * @param $key - The secret key.
 * @param $verify - (Optional) Toggles verification of token on/off.
 * @return payload of the JWT
 */
function jwt_decode($jwt, $key = null, $verify = true){
  $tks = explode('.', $jwt);
  if (count($tks) != 3)
    throw new UnexpectedValueException('Wrong number of segments');
  list($headb64, $bodyb64, $cryptob64) = $tks;
  if (null === ($header = json_e_decode(urlsafe_base64_decode($headb64))))
    throw new UnexpectedValueException('Invalid segment encoding');
  if (null === $payload = json_e_decode(urlsafe_base64_decode($bodyb64)))
    throw new UnexpectedValueException('Invalid segment encoding');
  $sig = urlsafe_base64_decode($cryptob64);
  if ($verify) {
    if (empty($header->alg))
      throw new DomainException('Empty algorithm');
    if ($sig != sign("$headb64.$bodyb64", $key, $header->alg))
      throw new UnexpectedValueException('Signature verification failed');
  }
  return $payload;
}

/**
 * Signs a string with a given key and algorithm.
 * @param $msg - The message to sign.
 * @param $key - The secret key.
 * @param $algo - (Optional) The signing algorithm ('HS256' (default),
 *  'HS384' or 'HS512').
 * @return encrypted message
 */
function sign($msg, $key, $method = 'HS256'){
  $methods = array('HS256' => 'sha256', 'HS384' => 'sha384', 'HS512' => 'sha512');
  if (empty($methods[$method]))
    throw new DomainException('Algorithm not supported');
  return hash_hmac($methods[$method], $msg, $key, true);
}

/**
 * Encodes into a JSON string (with error handling).
 * @param $input - Object to be encoded.
 * @return JSON representation of the object
 */
function json_e_encode($input){
  $json = json_encode($input);
  if (function_exists('json_last_error') && $errno = json_last_error()) {
    $messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
    throw new DomainException(isset($messages[$errno])?$messages[$errno]:'Unknown JSON error: '.$errno);
  }
  else if ($json === 'null' && $input !== null)
    throw new DomainException('Null result with non-null input');
  return $json;
}

/**
 * Decodes a JSON string (with error handling).)
 * @param $input - JSON string to be decoded.
 * @return object representation of the JSON string
 */
function json_e_decode($input){
  $obj = json_decode($input);
  if (function_exists('json_last_error') && $errno = json_last_error()) {
    $messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
    throw new DomainException(isset($messages[$errno])?$messages[$errno]:'Unknown JSON error: '.$errno);
  }
  else if ($obj === null && $input !== 'null')
    throw new DomainException('Null result with non-null input');
  return $obj;
}

/**
 * Encodes a string with URL-safe Base64.
 * @param $input - A string to be encoded.
 * @return the Base64 encoded string
 */
function urlsafe_base64_encode($input){
  return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
}

/**
 * Decodes a string with URL-safe Base64.
 * @param $input - A Base64 string to be decoded.
 * @return a decoded string
 */
function urlsafe_base64_decode($input){
  $remainder = strlen($input) % 4;
  if ($remainder) {
    $padlen = 4 - $remainder;
    $input .= str_repeat('=', $padlen);
  }
  return base64_decode(strtr($input, '-_', '+/'));
}

?>
