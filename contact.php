<?php

// Get the form data
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Validate the form data
if (empty($name) || empty($email) || empty($message)) {
  die('Error: Please fill out all the required fields.');
}

// Store the data in a file
$data = "$name|$email|$message\n";
file_put_contents('contacts.txt', $data, FILE_APPEND);

// Send an email notification
$to = 'stephen@mcneilly.dev';
$subject = 'New contact form submission for e404.dev';
$headers = 'From: '.$name.' .
           'Reply-To: '.$email.'\r\n' .
           'CC: admin@dr460n4ir3.io' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

$txt = 'You have received a message from '.$name .' Email: ' .$email .' Message: ' .$message;
mail($to, $subject, $txt, $headers);

// Redirect the user to a thank you page
header('Location: thank-you.html');
exit;

