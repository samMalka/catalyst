<?php

$servername="localhost";
$username="root";
$password="1234";
$dbname="dbname";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // echo "Database created successfully";
} else {
    echo "Error creating dbname database: " . $conn->error;
}

while(true){
    echo "Please enter an option: ";
    $userinput = fgets(STDIN);

    switch (trim($userinput)) {
      case "--help":
        echo "--file[csv file name] - this is the name of the CSV to be parsed\n";
        echo "--create_table - this will cause the MYSQL users table to be built
         (and no further action will be taken)\n";
        echo "--dry_run - this will be used with the --file directive in the instance that we want
        to run the script but not insert into the DB. All other functions will be executed,
        but the database won't be altered.\n";
        echo "-u - MySQL username\n";
        echo "-p - MySQL password\n";
        echo "-h - MySQL host\n";
        break;

      case "--file [csv file name]":
        echo "users.csv\n";
        break;

      case "--create_table":
        echo "run create table function\n";
        break;

      case "-u":
        echo $username."\n";
        break;

      case "-p":
        echo $password."\n";
        break;

      case "-h":
        echo $servername."\n";
        break;

      default:
        echo "Invalid Option Entered. \n";
        break;
    }
}

?>
