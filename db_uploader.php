<?php
$host = 'your_host';
$user = 'your_username';
$password = 'your_password';
$database = 'your_database';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$file = fopen("emails.txt", "r");
if ($file) {
    $stmt = $mysqli->prepare("INSERT INTO emails (email, password) VALUES (?, ?)");
    if ($stmt) {
        $values = [];
        while (($line = fgets($file)) !== false) {
            $line = trim($line);

            // Skip empty lines or lines starting with '#'
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Split the line into email and password
            list($email, $password) = explode(":", $line);

            $email = trim($email);
            $password = trim($password);

            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
        }

        $stmt->close();
        fclose($file);
    }
}

$mysqli->close();
?>
