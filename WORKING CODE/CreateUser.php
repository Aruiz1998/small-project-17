<?php

//file just knows that it can find the input using the file_get function here
//json_decode lets us use data that was sent as a jasn object
$data = json_decode(file_get_contents("php://input"));

//establish connection to server
$conn = mysqli_connect("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
//error output if connection fails
if (!$conn) {
  die("Connection failed: ");
}

//example of how data looks. $data holds a jason object like this and then gets made usable with json_decode
#$jsondict = '{"fName":"asdf","lName":"asdfafsdg","email":"asdf@asdf.ads","password":"asdfas"}';
#$data = json_decode($jsondict);

//the sql line to be executed by the data base. it needs to be sent as a string, but since the line has variable data in it theres a lot of string concatenation to make it work
//concatenation is done using . and strings are marked by '' instead of "" so we don't have to worry about escape characters. i.e. 'hello' . 'world'
$sql = 'INSERT INTO Users (FirstName, LastName, Login, Password) VALUES ("'. $data->fName . '","' . $data->lName . '","' . $data->email . '","' . $data->password . '")';

//sends the sql line to the database to be executed
if (mysqli_query($conn, $sql)) {
  echo "Record updated successfully";
} else {
  echo "Error updating record: ";
}

mysqli_close($conn);

?>