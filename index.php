<?php

// Mailer settings.
$GLOBALS["from"] = "no-reply@gmail.com";
$GLOBALS["to"] = "foo@gmail.com";
$GLOBALS["cc"] = "bar@gmail.com";
$GLOBALS["subject"] = "Feedback from website";

function composeMessage($name, $email, $phone, $message) {
    $body = <<<MSG
Feedback from website
- Name: $name
- Email: $email
- Phone: $phone

$message
MSG;

    // We use wordwrap in case any of our lines is longer than 70 characters.
    return wordwrap($body, 70);
}

// -----------------------------------------------------------
// You probably don't need to edit anything beyond this point.
// -----------------------------------------------------------

function isDefined($val) {
    $value = trim($val);

    return !empty($value);
}

function isValidEmail($email) {
    return preg_match(
        "/^[a-z0-9A-Z_\+-]+(\.[a-z0-9A-Z_\+-]+)*@[a-z0-9A-Z-]+(\.[a-z0-9A-Z-]+)*\.([a-z]{2,4})$/",
        $email
    );
}

// Define the success response.
function success($message) {
    return array(
        "success" => true,
        "message" => $message
    );
}

// Define the error response.
function errors($errors) {
    return array(
        "success" => false,
        "errors" => $errors
    );
}

function process() {
    $errors = array();

    $headers = "From: " . $GLOBALS["from"] . " <" . $GLOBALS["from"] . ">\r\n";
    $headers .= "Reply-To: " . $GLOBALS["to"] . "\r\n";
    $headers .= "Cc: " . $GLOBALS["cc"] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (!isDefined($_POST["name"])) {
        array_push(
            $errors,
            Array("field" => "name", "error" => "Name cannot be left blank")
        );
    }

    if(!isValidEmail($_POST["email"])) {
        array_push(
            $errors,
            Array("field" => "email", "error" => "Invalid email address")
        );
    }

    if (!isDefined($_POST["message"])) {
        array_push(
            $errors,
            Array("field" => "message", "error" => "Message cannot be left blank")
        );
    }

    if(empty($errors)) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $phone = isDefined($_POST["phone"]) ? $_POST["phone"] : "N/A";
        $message = $_POST["message"];
        $body = composeMessage($name, $email, $phone, $message);

        // mail($to, $subject, $body, $headers);

        return success("Message successfully sent");
    } else {
        return errors($errors);
    }
}

header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/json");

echo json_encode(process());
