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
header("Content-Type:text/html");

//error output if connection fails
if (!$conn) {
  die("Connection failed: ");
}

//Required fields
if ($data->ContactID == "" or $data->UserEmail == ""){
  http_response_code(404);
  echo "A field was left blank";
  exit();
}

//Find last Contact
$findID = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'"';
if($result1 = mysqli_query($conn, $findID)){
  $lastRowID = mysqli_num_rows($result1); //Contact ID
  //echo $lastRowID;
}else{
  http_response_code(500);
  die("Query 2 Failed");
}


//Delete current contact
$ask = 'DELETE FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND ContactID="'.$data->ContactID.'"';
if($result2 = mysqli_query($conn, $ask)){
}else{
  http_response_code(500);
  die("Query 1 Failed");
}


//If the contact removed was the last in the list there is nothing more to do.
if( ($lastRowID == $data->ContactID) OR ($data->ContactID == 0) OR ($lastRowID < $data->ContactID) ){
}else{
  
  //Record Last Contact
  $findContact = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND ContactID="'.$lastRowID.'"';
  if($result3 = mysqli_query($conn, $findContact)){
    $ContactRow = mysqli_fetch_assoc($result3);
    //echo $ContactRow["FName"];
  }else{
    http_response_code(500);
    die("Query 3 Failed");
  }
  
  
  //Insert new contact. (With same ID so it essentially updates that contact) and delete the old one.
  $sql1 = 'INSERT INTO Contacts (FName, LName, UserEmail, ContactID, PhoneNumber, ContactEmail) VALUES ("'.$ContactRow["FName"].'","'.$ContactRow["LName"].'","'.$data->UserEmail.'","'.$data->ContactID.'","'.$ContactRow["PhoneNumber"].'","'.$ContactRow["ContactEmail"].'")';

  $sql2 = 'DELETE FROM Contacts WHERE UserEmail="'.$data->UserEmail.'" AND ContactID="'.$lastRowID.'"';

  //sends the sql line to the database to be executed
  if ( (mysqli_query($conn, $sql1)) AND (mysqli_query($conn, $sql2)) ) {
  }else{
  http_response_code(409);
  echo "Error updating record";
  }
  
}



//If all that works then...
http_response_code(201);
echo "Record updated successfully";

mysqli_close($conn);

?>

