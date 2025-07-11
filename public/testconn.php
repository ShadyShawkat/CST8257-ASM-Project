<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'CST8257';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn -> connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

echo "Connected successfully to $dbname!<br>";

// Run a SQL query
$table = 'User';

$sql = "SELECT * FROM $table";
$result = mysqli_query($conn, $sql);

// Fetch the result data
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        // echo "id: " . $row["id"]. " - Name: " . $row["name"]. "";
        echo $row['UserId'] .' : ' . $row['Name'] . '<br>';
    }
} else {
    echo "0 results";
}

// Close the connection
$conn -> close();