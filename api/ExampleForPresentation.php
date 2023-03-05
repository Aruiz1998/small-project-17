<?php

$data = json_decode(file_get_contents("php://input")); //Receive data

$conn = mysqli_connect("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); //Establish connection to database

//Error: catch if connection fails
if (!$conn) { 
  die("Connection failed: ");
}

//Required fields
if ($data->valToSearch == "" or $data->UserEmail == ""){
  http_response_code(404);
  echo "A field was left blank";
  exit();
}

//Prepare command
$sql = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND (FName LIKE "%'.$data->valToSearch.'%" OR LName LIKE "%'.$data->valToSearch.'%" OR PhoneNumber LIKE "%'.$data->valToSearch.'%" OR ContactEmail LIKE "%'.$data->valToSearch.'%") ORDER BY CASE WHEN FName LIKE "'.$data->valToSearch.'%" THEN 1 WHEN LName LIKE "'.$data->valToSearch.'%" THEN 2 WHEN PhoneNumber LIKE "'.$data->valToSearch.'%" THEN 3 WHEN ContactEmail LIKE "'.$data->valToSearch.'%" THEN 4 ELSE 5 END';

//Package into JSON
if($result2 = mysqli_query($conn, $sql)){
  $rows = mysqli_num_rows($result2);
  $return = array();
  for($x = 0; $x < $rows; $x++){
  
    $ContactRow = mysqli_fetch_assoc($result2);
    $FName = $ContactRow["FName"];
    $LName = $ContactRow["LName"];
    $UserEmail = $ContactRow["UserEmail"];
    $ContactID = $ContactRow["ContactID"];
    $PhoneNumber = $ContactRow["PhoneNumber"];
    $ContactEmail = $ContactRow["ContactEmail"];
    
    
    $return[] = array('FName' => $FName, 'LName' => $LName, 'UserEmail' => $UserEmail, 'ContactID' => $ContactID, 'PhoneNumber' => $PhoneNumber, 'ContactEmail' => $ContactEmail,);
  }
}else{ //Error: catch if mysli command failed.
  http_response_code(409);
  die("Query Failed");
}

//Return JSON information
header("Content-Type:application/json");
http_response_code(201);
echo json_encode($return);

mysqli_close($conn); //Close connection

?>
