<?php
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: home.html");
  exit;
}
 

require "conn.php";//changed to use dynamic db
$link -> select_db("hospital_ms");
 
// Define variables and initialize with empty values
$id = $password = "";
$id_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Check if username is empty
    if(empty(trim($_POST["id"])))
	{
        $id_err = "Please enter id.";
    } else
	{
        $id = trim($_POST["id"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"])))
	{
        $password_err = "Please enter your password.";
    } 
	else
	{
        $password = trim($_POST["password"]);
		$password = hash('sha256', $password);
    }
	
	$userExists = "select COUNT(*) from user where id = '".$id."'";
	
        if ($link->query($userExists) == TRUE) 
		{
			$data = $link->query($userExists);
			$value = mysqli_fetch_assoc($data);
			$value = $value['COUNT(*)'];
			if($value == 0)
			{
				$username_err="Patient doesn't exist !";
			}
			
		}
    
    // Validate credentials
    if(empty($id_err) && empty($password_err))
	{

		
        $sql = "SELECT id, password FROM user WHERE id = ?";//select query for sql password verification
        
        if($stmt = mysqli_prepare($link, $sql))
		{
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $id);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt))
			{
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1)
				{       
                    // Bind result variables
					//add hashing function here
                    mysqli_stmt_bind_result($stmt, $id, $saved_password);
                    
                    if(mysqli_stmt_fetch($stmt))
					{
                        if($password ==  $saved_password)//removed password_verify 
						{
                            // Password is correct, so start a new session
                            session_start();
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;                           
                            $_SESSION["id"] = $id;

                            // Redirect user to welcome page
                            echo "<h1><center><br> Appointment Successful ! <BR> Redirecting to home pafge";
                            header('refresh:2;url=home.html');
                        } 
						else
						{
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } 
            } 
			else
			{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

