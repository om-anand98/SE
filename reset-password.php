<?php
// Initialize the session
session_start();
 
// Include config file
require "conn.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = $username = "";
$new_password_err = $confirm_password_err = $username_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    //Validate username
    if(empty(trim($_POST["username"])))
	{
        $username_err = "Please enter username.";
    } else
	{
        // Prepare a select statement
        $sql = "SELECT name FROM user WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql))
		{
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) != 1){
                    $username_err = "No account exists with the given id.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate new password
    if(empty(trim($_POST["new_password"])))
	{
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6)
	{
        $new_password_err = "Password must have atleast 6 characters.";
    } 
	else
	{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE user SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            
			mysqli_stmt_bind_param($stmt, "ss", $param_password, $param_id);
            
            // Set parameters
            $param_password = hash('sha256',$new_password);
            $param_id = $_POST["username"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.html");
                exit();
            } else{
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
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }

        @import url('https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap');
*{
    margin: 0;
    padding: 0;
    font-family: 'Hammersmith One', sans-serif;
    box-sizing: border-box;
}
body{
    height: 100vh;
    background: #594F4F;
    background: -moz-linear-gradient(-45deg, #1d4946  10%, #594F4F 100%);
    background: -webkit-linear-gradient(-45deg, #1d4946  10%, #594F4F    100%);
    background: linear-gradient(200deg, #1d4946  10%, #594F4F 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1d4946 ', endColorstr='594F4F   ', GradientType=1 );
}
.login-page{
    background: rgb(235, 235, 235);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    color: #292929;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    transition: all .4s;
}
.login-page:hover{
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.300);
}
.login-page h1{
    margin-bottom: 30px;
}
.login-page form{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.login-page form input  {
    width: 250px;
    height: 35px;
    margin: 8px;
    background: rgb(218, 218, 218);
    border: none;
    padding-left: 10px;
    outline: none;
    transition: all .4s;
}
.login-page form input:focus{
    background: rgb(194, 194, 194);
}
.login-page form .btn{
    width: 250px;
    height: 35px;
    margin: 8px;
    background: #db02b0cc;
    color: #e2e2e2;
    border: none;
    outline: none;
    cursor: pointer;
    transition: all .4s;
}
.login-page form .btn:hover{
    background: #db02b08f;
}

    </style>
</head>
<body>
    <center>
    <div class="wrapper login-page">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>ID</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="login.html">Cancel</a>
            </div>
        </form>
    </div> 
    </center>   
</body>
</html>