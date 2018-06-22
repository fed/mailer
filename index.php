<?php

// Mailer settings.
$from = "no-reply@email.com";
$to = "foo@email.com";
$cc = "bar@email.com";
$title = "Feedback from website";

function composeMessage($name, $email, $message) {
    $body = <<<MSG
MENSAJE ENVIADO DESDE EL SITIO WEB

Nombre: $name

Email: $email

$message
MSG;

    return $body;
}


// -----------------------------------------------------------
// You probably don't need to edit anything beyond this point.
// -----------------------------------------------------------

// just making sure that information is there
function isValid($val) {
    $value = trim($val);

    return !($value === "" || $value === null);
}

// Make sure the email is valid.
function isValidEmail($email) {
    return preg_match(
        "/^[a-z0-9A-Z_\+-]+(\.[a-z0-9A-Z_\+-]+)*@[a-z0-9A-Z-]+(\.[a-z0-9A-Z-]+)*\.([a-z]{2,4})$/",
        $email
    );
}

// Define the success response.
function success($num, $msg) {
    return array(
        "status" => "success",
        "confirmationNumber" => $num,
        "message" => $msg
    );
}

// Define the error response.
function errors($err) {
    return array(
        "status" => "errors",
        "message" => $err
    );
}

function process() {
    $success = false;
    $errors = array();

    $headers = "From: " . $from . " <" . $from . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= "Reply-To: " . $to . "\r\n";
    $headers .= "Cc: " . $cc . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if(!empty($_POST["submit"])) {

        // you make conditionals to skip not required fields
        //this skips suffix and title
        foreach($_POST as $key => $val):
            if(!isValid($val) and $key !== "suffix" and $key !== "title" ) {
                array_push($errors, $key);
            }
        }


        if(!isValidEmail($_POST["email"])) {
            array_push($errors, "email");
        }

        //Results
        if(empty($errors)) {
            $success = true;

            $head = "<h2>Email Header</h2>";

            // Pull the variables into this body variable
            $body = "<p>Body of infomation</p>";

            $message = $head . $body; // composeMessage() @TODO

            // In case any of our lines are larger than 70 characters, we should use wordwrap()
            $message = wordwrap($message, 70);

            if (mail($to, $title, $message, $headers)) {
                return success($confirmation_no, $message);
            } else {
                // couldn't send email
            }

        } else {
            return errors($errors, "Please fix errors then resubmit");
        }

    } else {
        array_push($errors, "You have submitted this form in a Bad Method");

        return errors($errors);
    }
}

header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/json");

echo json_encode(process());
