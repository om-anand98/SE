<?php
if(isset($_POST["name"])){
    
    $server= "localhost";
    $user= "root";
    $pass= "";
    $con= mysqli_connect($server, $user, $pass);
    
    if(!$con){
    die("Connection Unsuccessful :: Cause ::" . mysqli_connect_error());
    }

    // Bind variables to the prepared statement as parameters
    $id= "PTT" . date("YmdHis");
    $name=  $_POST["name"];
    $password= hash('sha256', $_POST["password"]);
    $age= $_POST["age"];
    $mob= $_POST["mob"];
    $gender= $_POST["gender"];
    $doc= date("d-m-Y") . " " . date("h:i:s") . " " . strtoupper(date("a"));
    
    $sql_query = "INSERT INTO `hospital_ms`.`user` (`id`, `name`, `age`, `gender`, `phone`, `password`, `d_of_c`) VALUES ('$id', '$name', $age, '$gender', '$mob', '$password', '$doc')";
    
    // Attempt to execute the prepared statement
    if($con->query($sql_query)== true){
        //header("location: login.php");
        echo "<h1><center><br> Your ID : " . $id;
        echo "<br> Date of Creation : " . $doc;
        echo "<br> You'll be automatically redirected to login page";
        header('refresh:5;url=login.html');
    }else{
        echo "Something went wrong. Please try again later.";
        echo "ERROR : $sql_query <br> $con->error";
    }
    // Close connection
    $con->close();
}
?>
 
