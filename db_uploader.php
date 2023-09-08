<?php
$host = '';
$user = '';
$password = '';
$database = '';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$file = fopen("emails.txt", "r");
if ($file) {
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

        // Escape special characters and surround values with single quotes
        $email = $mysqli->real_escape_string($email);
        $password = $mysqli->real_escape_string($password);

        $values[] = "('$email', '$password')";

        // Limit the number of values to insert at once
        if (count($values) >= 1000) {
            $query = "INSERT INTO emails (email, password) VALUES " . implode(",", $values);
            $mysqli->query($query);
            $values = [];
        }
    }

    // Insert any remaining values
    if (count($values) > 0) {
        $query = "INSERT INTO emails (email, password) VALUES " . implode(",", $values);
        $mysqli->query($query);
    }

    fclose($file);
}

$mysqli->close();
?>
