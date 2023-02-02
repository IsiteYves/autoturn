<?php

// Connect to the MySQL database
$servername = "localhost";
$username = "root";
$password = "esyvprog";
$dbname = "oti_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the table exists, and create it if it doesn't
$table_name = "remote_switching_history";
$check_table = "SHOW TABLES LIKE '$table_name'";
$table_exists = mysqli_query($conn, $check_table);

if (mysqli_num_rows($table_exists) == 0) {
    $create_table = "CREATE TABLE $table_name (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        device_name VARCHAR(30) NOT NULL,
        data_status VARCHAR(30) NOT NULL,
        switchedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!mysqli_query($conn, $create_table)) {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

// Get the device name and data_status from the AJAX request
$device_name = $_POST['device_name'];
$data_status = $_POST['data_status'];

// Insert the new data into the database
$sql = "INSERT INTO $table_name (device_name, data_status)
VALUES ('$device_name', '$data_status')";

if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
