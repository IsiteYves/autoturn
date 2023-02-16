<?php
// Connect to the MySQL database
$servername = "localhost";
$username = "benax_iot_root";
$password = "Td(FAdeZ9xp3";
$dbname = "benax_iot";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
$deviceip = $data['deviceip'];
// SQL query to retrieve the last row from the 'remote_switch_history' table
$sql = "SELECT data_status FROM remote_switching_history WHERE device_ip='$deviceip' ORDER BY id DESC LIMIT 1";
// Execute the query and store the result
$result = mysqli_query($conn, $sql);
// Check if the query was successful
if (mysqli_num_rows($result) > 0) {
    // Fetch the last row from the result
    $row = mysqli_fetch_assoc($result);
    // Retrieve the value of the 'lightstatus' field
    $lightstatus = $row["data_status"];
    http_response_code(200);
    // Return the value of 'lightstatus'
    echo $lightstatus;
} else {
    http_response_code(200);
    echo "No data found";
}
