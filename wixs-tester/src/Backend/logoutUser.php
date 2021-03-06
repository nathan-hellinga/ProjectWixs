#!/usr/bin/php-cgi
<?php
/** 
 * A file for user logout. Connects the navbar to the users table within the database.
 * 
 * -- Additional Notes --
 * - Do not use any upper case for column names or table names within database (will cause error when 
 * binding PDO values)
 * 
 */
header("Access-Control-Allow-Origin: *");

require_once 'dbConfig.php';
 
$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$username;password=$password";

$responseObject = array();
$responseObject['success']=false; // whether the operation executed successfully
$responseObject['message']=""; // the message from the execution, error or success

try {
 // create a PostgreSQL database connection
 $pdo = new PDO($dsn);
 
 // display a message if connected to the PostgreSQL successfully
    if($pdo) { // connected successfully
        if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
            // Using empty test instead of isset function
            $email_post = empty($_POST['email']) ? null : $_POST['email']; // set email to form submission

            if (validInputs() && accountExists() && updateSession()) {
                $responseObject['success']=true; // echoing a response that can be used to redirect page after AJAX call
                $responseObject['message']="{$email_post} has been logged out. ";
            } // otherwise, error, response message is displayed in alert
            
        } else { // request method is not POST
            $responseObject['message']="Invalid request. ";
        }
    }
} catch (PDOException $e) { 
    $responseObject['message']=$e->getMessage(); // report error message
}

function updateSession() {
    global $pdo;
    global $responseObject;
    global $email_post;

    $sql_update = "UPDATE users SET session_id = '' WHERE email = ?";
    $stmt = $pdo->prepare($sql_update);

    // pass and bind values to the statement
    $stmt->bindValue(1, $email_post, PDO::PARAM_STR); // binding to strings
    
    if($stmt->execute()) { // The query has executed successfully
        return true;
    } else {
        $responseObject['message']="Error querying users table: update(remove) session. ";
        return false;
    }
}

/**
 * First checks if any inputs are missing, then validates and sanitizes certain inputs. 4 Rounds of validation.
 * 
 * @return boolean true if no inputs are mssing, and all inputs are valid. Then sanitizes them. False, if any of those
 * criteria are not met.
 * */
function validInputs() {
    global $email_post;
    global $responseObject;

    // Round 1 -- Are inputs empty
    // does not check template count because field may be null
    if(empty($email_post)) {
        $responseObject['message']="One of more fields are missing. Please complete. ";
        return false; // invalid
    }

    // Round 2 -- Trimming inputs of trailing/leading whitespace
    $email_post = trim($email_post);

    // Round 3 -- Checking input lengths and validity
    // checking email length
    if(strlen($email_post) > 100) {
        $responseObject['message']="Invalid email: length too long. ";
        return false;
    }

    // checking if valid user email provided
    if(!filter_var($email_post, FILTER_VALIDATE_EMAIL)){
        $responseObject['message']="Invalid email: formatting.  ";
        return false; // invalid
    }

    // Round 4 -- Sanitize inputs
    // if no inputs are missing or invalid, they are sanitized.
    $email_post = filter_var($email_post,FILTER_SANITIZE_EMAIL);

    return true; // tests passed -> valid
}


/**
 * Checks if the user's account exists within the database with the macthing credentials returns whether true or false.
 * 
 * @return boolean true if the user already exists, false if not
 */
function accountExists() {
    global $pdo;
    global $email_post;
    global $responseObject;

    // prepare statement for insert
    $sql_select = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $pdo->prepare($sql_select);

    // pass and bind values to the statement
    $stmt->bindValue(1, $email_post, PDO::PARAM_STR); // binding to string

    if($stmt->execute()) { // The query has executed successfully
        if ($stmt->rowCount() > 0) { // account with username found
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return true;

        } else {
            $responseObject['message']="User with email {$email_post} does not exist. Please login again or register account. ";
            return false;
        }
    } else {
        $responseObject['message']="Error querying users table. ";
    }
}


echo json_encode($responseObject);
?>