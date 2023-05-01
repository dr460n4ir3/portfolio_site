<?php
// Load environment variables from .env file
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get the form data
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Validate the form data
if (empty($name) || empty($email) || empty($message)) {
  die('Error: Please fill out all the required fields.');
}

// Define the database credentials
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE']; // This is for the database name not table name

$conn = new mysqli($servername, $username, $password, $dbname);
// NOTE REMEMBER TO CHANGE THE ABOVE TO YOUR OWN DATABASE CREDENTIALS

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// define a function to insert data into the database
function insertData($name, $email, $subject, $message) {
    global $conn; // reference the global database connection variable
    
    // prepare the SQL query
    $sql = "INSERT INTO contact_form (name, email, subject, message) VALUES (?, ?, ?, ?)"; // NOTE: contact_form is the table name
    $stmt = mysqli_prepare($conn, $sql);
    
    // bind the parameters to the query
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);
    
    // execute the query
    mysqli_stmt_execute($stmt);
    
    // check if the query was successful
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        return true;
    } else {
        return false;
    }
}

// create a function to store data in a json file
function storeData($name, $email, $subject, $message) {
    $data = array(
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message
    );
    $data = json_encode($data);
    $file = 'contact-form-data.json';
    file_put_contents($file, $data);
}

// Store the form data in a JSON file
storeData($name, $email, $subject, $message);

// Call the insertData function
if (insertData($name, $email, $subject, $message)) {
    // Send an email notification
    $to = 'stephen@mcneilly.dev';
    $subject = 'New contact form submission for e404.dev';
    $headers = 'From: '.$name.' ' .
            'Reply-To: '.$email.'\r\n' .
            'CC: admin@dr460n4ir3.io' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    $txt = 'You have received a message from '.$name .' Email: ' .$email .' Message: ' .$message;
    mail($to, $subject, $txt, $headers);

    // Redirect the user to a thank you page
    header('Location: thank-you.html');
    exit;
} else {
    echo 'Error: Unable to insert data into the database.';
}

// close the database connection
mysqli_close($conn);