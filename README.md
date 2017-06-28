<h1 align="center">CodeIgniter RAP</h1>
<p align="center">
Simple RESTful API boilerplate for the CodeIgniter framework.
</p>
<p align="center">
<img src="/docs/logo.png"/>
</p>

## Requirements

- **PHP**: 5.3.7 or greater
- **CodeIgniter**: 3.1.5 or greater

## Installation

1. Copy and paste all the files from **application** to the corresponding directory in your application.
2. Copy and paste the `config.example.ini` file outside your **application** folder or create yours, based on the example provided. Remember to name your file `config.ini` or rename the sample provided.
3. Create your own secret key and update `config.ini` to be completely secure.
4. (Optional) Copy and paste the `.example.htaccess` file and tweak it to your liking. Remember to rename it to `.htaccess`.
5. (Recommended) Enable HTTPS for your server for maximum security.

## Contents

The API is made up of a set of helpers, each one serving a different purpose. Click on each helper below to read about its functionality.

- [error_code](#error_code)
- [request](#request)
- [database](#database)
- [jwt](#jwt)
- [auth](#auth)
- [rest_api](#rest_api)

### error_code

Contains values for various error codes. The codes are mostly matched to HTTP status codes, but some of them might not be perfect matches. The error codes are stored as global variables.

| Variable        | Value  | Description                                                       |
| --------------- |:------:| ----------------------------------------------------------------- |
| PROHIBITED      | 405    | The action is not allowed.                                        |
| BAD_DATA        | 400    | The data is malfored or does not match expected input.            |
| BAD_CREDENTIALS | 403    | The credentials provided cannot be successfully authorized.       |
| UNAUTHORIZED    | 403    | The user has not the proper authorization to perform this action. |
| NO_COOKIE       | 409    | The expected cookie was not found.                                |
| SUCCESS         | 200    | The action was successful.                                        |

### request

Contains a single method for retrieving the body of the current request.

- `get_request_body()`: Retrieves the body of the current request.
  * **returns** the body of the current request

### database

Contains multiple methods for connecting and retrieving information from the database.

- `database_connect()`: Establishes a connection to the database. The connection's parameters (host, username, password and database name) are parsed from the `config.ini` file, residing in the CodeIgniter root folder.
  * **returns** a connection to the database
- `database_query($query, $types, $params, $query_type)`: Uses a prepared statement to query the database, returning an associative array or false, based on the query's results.
  * `$query`: The query to the database, as a string.
  * `$types`: A string that contains one or more characters which specify the types for the corresponding bind variables.
  * `$params`: An array of values that will be passed as parameters to the query. The types of the parameters must match the types specified by `$types`.
  * `$query_type`: (Optional) The type of query that will be executed ("SELECT" (default), "INSERT", "UPDATE", "DELETE"). The execution of the prepared statement will return different kinds of results based on the type specified.
  * **returns** an associative array or false
- `database_no_args_query($query)`:
  * Queries the database, returning an associative array or false, based on the query's results. The query must have no arguments (e.g. useful for retrieving all values from a table).
  * `$query`: The query to the database, as a string.
  * **returns** an associative array or false
- `database_error()`: Returns the last connection error, if any.
  * **returns** error description

### jwt

Heavily based on [this implementation](https://github.com/rmcdaniel/angular-codeigniter-seed/blob/master/api/application/helpers/jwt_helper.php), the JWT helper allows for the creation of [JSON Web Tokens](https://jwt.io/). The secret key provided in `config.ini` will be used to sign the token.

- `jwt_encode($payload, $key, $algo)`: Creates a JWT string.
  * `$payload`: The payload of the JSON Web Token.
  * `$key`: The secret key
  * `$algo`: (Optional) The signing algorithm ('HS256' (default), 'HS384' or 'HS512').
  * **returns** a signed JWT
- `jwt_decode($jwt, $key, $verify)`:  Decodes a JWT string.
  * `$jwt`: The JSON Web Token.
  * `$key`: The secret key.
  * `$verify`: (Optional) Toggles verification of token on/off.
  * **returns** the payload of the JWT
- `sign($msg, $key, $method)`:  Signs a string with a given key and algorithm.
  * `$msg`: The message to sign.
  * `$key`: The secret key.
  * `$algo`: (Optional) The signing algorithm ('HS256' (default), 'HS384' or 'HS512').
  * **returns** encrypted message
- `json_e_encode($input)`: Encodes into a JSON string (with error handling).
  * `$input`: Object to be encoded.
  * **returns** the JSON representation of the object
- `json_e_decode($input)`: Decodes a JSON string (with error handling).)
  * `$input`: JSON string to be decoded.
  * **returns** the object representation of the JSON string
- `urlsafe_base64_encode($input)`: Encodes a string with URL-safe Base64.
  * `$input`: A string to be encoded.
  * **returns** the Base64 encoded string
- `urlsafe_base64_decode($input)`: Decodes a string with URL-safe Base64.
  * `$input`: A Base64 string to be decoded.
  * **returns** a decoded string

### auth

Contains multiple methods used for authorization, authorization validation and usage with JSON Web Tokens in cookies.

- `authorize($table, $fields, $username_field, $password_field, $id_field, $username_value, $password_value, $service_name, $cookie_name)`: Provides authorized access to the system for a user, based on the provided credentials, using a query to the database. If the authorization is successful, a unique JSON Web Token is generated and stored in a cookie.
  * `$table`: The database table to query.
  * `$fields`: An array of names for the fields to be requested.
  * `$username_field`: The name of the username field.
  * `$password_field`: The name of the password field.
  * `$id_field`: The name of the id field.
  * `$username_value`: The value of the username field.
  * `$password_value`: The value of the password field.
  * `$service_name`: The name of the service.
  * `$cookie_name`: The name of the cookie used to store the authorization token.
  * **returns** an associative array
- `generate_jwt_cookie($username_value, $id_value, $service_name, $cookie_name)`: Generates a unique JSON Web Token from the values provided.
  * `$username_value`: The user's unique username.
  * `$id_value`: The user's unique id.
  * `$service_name`: The name of the service.
  * `$cookie_name`: The name of the cookie used to store the authorization token.
  * **returns** void
- `regenerate_jwt_cookie($service_name, $cookie_name)`: Regenerates a unique JSON Web Token from the values provided. Will return a message if no existing cookie is found.
  * `$service_name`: The name of the service.
  * `$cookie_name`: The name of the cookie used to store the authorization token.
  * **returns** an associative array
- `check_jwt_cookie($service_name, $cookie_name)`: Checks the validity of a unique JSON Web Token.
  * `$service_name`: The name of the service.
  * `$cookie_name`: The name of the cookie used to store the authorization token.
  * **returns** true if the cookie is found and the JWT is valid, false otherwise
- `get_jwt_data($cookie_name)`: Gets the data stored in a unique JSON Web Token.
  * `$cookie_name`: The name of the cookie used to store the authorization token.
  * **returns** an associative array


### rest-api

Contains multiple methods, implementing generic CRUD methods for a RESTful API. For security reasons, certain methods are not implemented, but rather return an associative array with an error code and a message.

- `createResourceRoot()`: Creates a new collection, using a query to the database.
  * **returns** an associative array with an error message
- `createResourceElement($table, $input_fields, $input_types, $input_values)`: Creates a new entry in a collection, using a query to the database.
  * `$table`: The database table to query.
  * `$input_fields`: An array of names for the fields to be filled.
  * `$input_types`: A string that contains one or more characters
  *  which specify the types for the corresponding fields.
  * `$input_values`: An array of values for the fields to be filled.
  * **returns** the id of the newly-created collection member
- `readResourceRoot($table, $fields)`: Lists the members of a collection, using a query to the database.
  * `$table`: The database table to query.
  * `$fields`: The table's fields to be retrieved.
  * **returns** a collection of elements
- `readResourceElement($table, $fields, $element_key, $key_value)`: Retrieves a specific member of a collection, using a query to the database.
  * `$table`: The database table to query.
  * `$fields`: The table's fields to be retrieved.
  * `$element_key`: The table's field that will be used for the specific
  *  resource's indentification.
  * `$key_value`: The value to be used for the specific resource's
  *  identification.
  * **returns** a list of members or false
- `updateResourceRoot()`: Updates a resource collection, using a query to the database.
  * **returns** an associative array with an error message
- `updateResourceElement($table, $input_fields, $input_types, $input_values, $element_key, $key_value)`: Updates a specific member of a collection, using a query to the database.
  * `$table`: The database table to query.
  * `$input_fields`: An array of names for the fields to be updated.
  * `$input_types`: A string that contains one or more characters
  *  which specify the types for the corresponding fields.
  * `$input_values`: An array of values for the fields to be updated.
  * `$element_key`: The table's field that will be used for the specific
  *  resource's indentification.
  * `$key_value`: The value to be used for the specific resource's
  *  identification.
  * **returns** the number of affected rows
- `deleteResourceRoot()`: Deletes a resource collection, using a query to the database.
  * **returns** an associative array with an error message
- `deleteResourceElement($table, $element_key, $key_value)`: Deletes a specific member of a collection, using a query to the database.
  * `$table`: The database table to query.
  * `$element_key`: The table's field that will be used for the specific
  *  resource's indentification.
  * `$key_value`: The value to be used for the specific resource's
  *  identification.
  * **returns** the number of affected rows

## How to use

The provided helpers are supposed to be used in a `CI_Controller`, but you can use them any way you like. The sample provided (**controllers/Api.php**) is a pretty good starting point for a RESTful API implementation:

- All helpers are loaded in the `__construct()` method of the `Api` class. Certain variables are also instantiated to be used for the API's requests.
- The `index()` method is an empty method returning an error message, when no resource is specified in the request in the form of a URI.
- The `users($param)` method maps the different API methods to HTTP methods (CREATE = POST, READ = GET, UPDATE = PUT, DELETE = DELETE) and uses the various helpers and class variables to provide a sample RESTful API implementation.
- The `login($param)` method allows for the authorization of a user (needed to update or delete a resource matched to his/her username).

To query the API, you should use something like `example.com/index.php/api/resource_name` (or `example.com/api/resource_name`, if you are using the `.htaccess` file provided, configured to your environment), replacing `resource_name` with your resource's name (e.g. `users`). Bear in mind that certain requests will return errors due to implementation specifics.

## License
The project is licensed under the MIT license.
