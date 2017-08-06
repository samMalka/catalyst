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
    // echo "Database dbname created successfully";
} else {
    echo "Error creating dbname database: " . $conn->error;
}

$conn=create_database_connection();

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
        $file = fopen("users.csv", "r");
        if(check_table_existence($conn)==false){
          create_table();
          if(check_email_validity($conn,$file)==false){
            insert_records($conn,$file);
          }
        }
        else{
          if(check_email_validity($conn,$file)==false){
            insert_records($conn,$file);
          }
        }
        fclose($file);
        break;

      case "--dry_run":
        $file = fopen("users.csv", "r");
        check_email_validity($conn,$file);
        fclose($file);
        echo getUsername();
        echo getPassword();
        echo getserverName();
      break;

      case "--create_table":
        create_table();
        break;

      case "-u":
        echo getUsername();
        break;

      case "-p":
        echo getPassword();
        break;

      case "-h":
        echo getserverName();
        break;

      default:
        echo "Invalid Option Entered. \n";
        break;
    }
}


function create_database_connection(){

  global $servername,$username,$password,$dbname;
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  return $conn;
}

//Create the User Table
function create_table(){

  global $conn;

  $tableExistenceStatus=check_table_existence($conn);
  if($tableExistenceStatus==true){
    //dropping the table inorder to recreate the table
    $sql="drop table users";
    if($conn->query($sql)==true){
      //echo "Table Dropped\n";
    }else{
      //echo "Error, While dropping the table\n";
    }
  }
  $sql = "CREATE TABLE users (
  name VARCHAR(50) NOT NULL,
  surname VARCHAR(50) NOT NULL,
  email VARCHAR(200) PRIMARY KEY
  )";

  if ($conn->query($sql) === TRUE) {
      // echo "Table Users created successfully\n";
  } else {
      echo "Error creating users table: " . $conn->error;
  }
}


function insert_records($conn,$file){

  //start from the second line of the csv file
  fseek($file,1);

  while(($row=fgetcsv($file)) != false)
  {
    $name=mysqli_real_escape_string($conn,trim(ucfirst(strtolower($row[0]))));
    $surname=mysqli_real_escape_string($conn,trim(ucfirst(strtolower($row[1]))));
    $email=trim(strtolower($row[2]));

    // $validateEmail=check_email_validity($email);
    // if($validateEmail==true){

      $email=mysqli_real_escape_string($conn,trim(strtolower($row[2])));

      $checkEmailAlreadyInserted="select email from users where email='".$email."'";
      $result=$conn->query($checkEmailAlreadyInserted);
      if($result->num_rows == 0){
        // echo "Correct Email\n";
        $conn=create_database_connection();
        $sql="insert into users(name,surname,email) values('".$name."','".$surname."','".$email."')";
        if($conn->query($sql)===TRUE){
          // echo "Successfully Inserted\n";
        }else{
          echo "Error Occured while inserting: ". $conn->error;

        }
      }
      else{
        // echo "Email Already Inserted\n";
      }

  }
}

//CHECK IF THE USER TABLE HAS ALREADY CREATED
function check_table_existence($conn){
  $tableExistence=false;
  $sql="show tables like 'users'";
  $result=$conn->query($sql);
  if($result->num_rows==1){
    // echo "Table is already created";
    $tableExistence=true;
  }else{
    // echo "Table is created";
    $tableExistence=false;
  }

  return $tableExistence;
}

function check_email_validity($conn,$file){

  $firstLine=true;
  $emailErrosFound=false;
  while(($row=fgetcsv($file)) != false){
    if($firstLine){
      //Assuming first line of any csv file contains the header(name,surname,email)
      $firstLine=false;
      continue;
    }else{
      $email=trim(strtolower($row[2]));
      if((filter_var($email,FILTER_VALIDATE_EMAIL))==false){
        fwrite(STDOUT,"Invalid Email Found: ".$email."\n");
        $emailErrosFound=true;
        break;
      }
    }
  }
  return $emailErrosFound;
}

function getUsername(){
  global $username;
  return "Username: ".$username."\n";
}

function getPassword(){
  global $password;
  return "Password: ".$password."\n";
}

function getserverName(){
  global $servername;
  return "Host: ".$servername."\n";
}

?>
