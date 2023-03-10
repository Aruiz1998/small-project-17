<?php
//Example of how data looks. $data holds a json object and then gets made usable with json_decode
#$jsondict = '{"fName":"asdf","lName":"asdfafsdg","email":"asdf@asdf.ads","password":"asdfas"}'; -What it looks like it .js
#$data = json_decode($jsondict); -How to use it in .php

//file just knows that it can find the input using the file_get function here
//json_decode lets us use data that was sent as a json object
$data = json_decode(file_get_contents("php://input"));

//establish connection to server
$conn = mysqli_connect("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

//Output type
header("Content-Type:application/json");

//error output if connection fails
if (!$conn) {
  die("Connection failed: ");
}

//Required fields
if ($data->valToSearch == "" or $data->UserEmail == ""){
  http_response_code(404);
  echo "A field was left blank";
  exit();
}

//Package all contacts for a user into JSON
if ($data->type == "FName"){
$sql = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND FName LIKE "%'.$data->valToSearch.'%" ORDER BY CASE WHEN FName LIKE "'.$data->valToSearch.'" THEN 1 WHEN FName LIKE "'.$data->valToSearch.'%" THEN 2 WHEN FName LIKE "%'.$data->valToSearch.'" THEN 4 ELSE 3 END';
}
else if ($data->type == "LName"){
$sql = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND LName LIKE "%'.$data->valToSearch.'%" ORDER BY CASE WHEN LName LIKE "'.$data->valToSearch.'" THEN 1 WHEN LName LIKE "'.$data->valToSearch.'%" THEN 2 WHEN LName LIKE "%'.$data->valToSearch.'" THEN 4 ELSE 3 END';
}
else if ($data->type == "PhoneNumber"){
$sql = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND PhoneNumber LIKE "%'.$data->valToSearch.'%" ORDER BY CASE WHEN PhoneNumber LIKE "'.$data->valToSearch.'" THEN 1 WHEN PhoneNumber LIKE "'.$data->valToSearch.'%" THEN 2 WHEN PhoneNumber LIKE "%'.$data->valToSearch.'" THEN 4 ELSE 3 END';
}
else if ($data->type == "ContactEmail"){
$sql = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND ContactEmail LIKE "%'.$data->valToSearch.'%" ORDER BY CASE WHEN ContactEmail LIKE "'.$data->valToSearch.'" THEN 1 WHEN ContactEmail LIKE "'.$data->valToSearch.'%" THEN 2 WHEN ContactEmail LIKE "%'.$data->valToSearch.'" THEN 4 ELSE 3 END';
}
else {
$sql = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND (FName LIKE "%'.$data->valToSearch.'%" OR LName LIKE "%'.$data->valToSearch.'%" OR PhoneNumber LIKE "%'.$data->valToSearch.'%" OR ContactEmail LIKE "%'.$data->valToSearch.'%") ORDER BY CASE WHEN FName LIKE "'.$data->valToSearch.'%" THEN 1 WHEN LName LIKE "'.$data->valToSearch.'%" THEN 2 WHEN PhoneNumber LIKE "'.$data->valToSearch.'%" THEN 3 WHEN ContactEmail LIKE "'.$data->valToSearch.'%" THEN 4 ELSE 5 END';
}


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
}else{
  http_response_code(409);
  die("Query Failed");
}

//If all that works then...
header("Content-Type:application/json");
http_response_code(201);
echo json_encode($return);

mysqli_close($conn);

?>
