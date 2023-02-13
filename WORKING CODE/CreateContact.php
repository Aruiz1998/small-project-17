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

//CONTACTS TABLE
//'CREATE TABLE Contacts (FName VARCHAR(255) NOT NULL, LName VARCHAR(255), UserEmail VARCHAR(255), ContactID INT, PhoneNumber VARCHAR(255), ContactEmail VARCHAR(255));'

/*PSEUDOCODE
  Retreive which user ID we need make the new Contact.
    SELECT FROM UserEmail
     ContactID = rows+1
  Put in other data
  Attempt - the mysql is set up to not accept a NULL value for the first name field.
*/

if ($data->FName == "" or $data->UserEmail == ""){
  http_response_code(404);
  echo "A field was left blank";
  exit();
}

//See the last contact in the contacts list of that user.
$ask = 'SELECT * FROM Contacts WHERE UserEmail="'.$data->UserEmail.'"';
if($result = mysqli_query($conn, $ask)){
  $CID = mysqli_num_rows($result) + 1; //Contact ID
}else{
  http_response_code(500);
  die("Query Failed");
}

mysqli_free_result($result);


//the sql line to be executed by the data base. it needs to be sent as a string, but since the line has variable data in it theres a lot of string concatenation to make it work
//concatenation is done using . and strings are marked by '' instead of "" so we don't have to worry about escape characters. i.e. 'hello' . 'world'
//$sql = 'INSERT INTO Contacts (FName, LName, UserEmail, ContactID, PhoneNumber, ContactEmail) VALUES ("'.$data->FName.'","'.$data->LName.'","'.$data->UserEmail.'","'.$CID.'","'.$data->PhoneNumber.'","'.$data->ContactEmail.'")';

$sql = 'INSERT INTO Contacts (FName, LName, UserEmail, ContactID, PhoneNumber, ContactEmail) VALUES ("'.$data->FName.'","'.$data->LName.'","'.$data->UserEmail.'","'.$CID.'","'.$data->PhoneNumber.'","'.$data->ContactEmail.'")';

//sends the sql line to the database to be executed
if (mysqli_query($conn, $sql)) {
  http_response_code(201);
  echo "Record updated successfully";
} else {
  http_response_code(409);
  echo "Error updating record";
}

mysqli_close($conn);

?>
