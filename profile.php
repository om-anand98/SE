<?php
session_start();

$server= "localhost";
$user= "root";
$pass= "";
$database="hospital_ms";
$con= mysqli_connect($server, $user, $pass, $database);

if(!$con){//shows error if connection is unsuccessful
die("Connection Unsuccessful :: Cause ::" . mysqli_connect_error());
}

$id= isset($_SESSION["id"]) ? $_SESSION["id"] : '';
$sql_query= "select name, age, gender, phone, d_of_c from user where id= '" . $id . "'";
$field= ["Name", "Age", "Gender", "Phone", "Date of Creation"];

$result= $con->query($sql_query);
while($row= $result->fetch_array()){
    for($i =0; $i< 5; $i++){
        echo '<br>'. $field[$i]." : ".$row[$i];
    }
}
?>