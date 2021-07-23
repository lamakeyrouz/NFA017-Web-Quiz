<?php
    include '../../connect.php';
    
    if(isset($_POST["user_email"])){
        $user_email = $_POST["user_email"];
        $user_password = $_POST["user_password"];
        $query = "SELECT * FROM `abonne` WHERE `username` = '{$user_email}'";
        $res = mysqli_query($db , $query);
        $row = mysqli_fetch_array($res);
        if(mysqli_num_rows($res) > 0 ){
            if(strcmp($user_password, $row["password"]) == 0){
                // Set cookies to save the type of login and to use the id of the admin/user
                setcookie("AdminId","", time() - 3600 ,"/");
                setcookie("UserId", $row["idabonne"], time() + (10 * 365 * 24 * 60 * 60), "/");
                header("Location: ../../UserHomePage/userHomePage.php");
            }else{
                echo "<script>alert('wrong username or password')</script>";
            }
        }else{
            echo "<script>alert('wrong username or password')</script>";
        }
    }
?>
<!DOCTYPE html>
<html class="background_user">

<head>
    <title>Web Quiz</title>
    <link rel="stylesheet" href="../authentication.css" />
    <script src="../authentication.js"></script>
</head>

<body>
    <div id="login background_user" class="login login_user">
        <div class="back">
            <h1>User Login</h1>
            <div onclick="goToLanding()">
                <a><</a>
            </div>
        </div>
        <form id="user_loginForm" class="loginContent" onsubmit="return false" autocomplete="off" method="post">
            <div class="loginContainer loginContainer_user">
                <label for="user_email"><b>Username</b></label>
                <input autocomplete="off" id="user_email" type="text" placeholder="Enter Username" name="user_email" required>

                <label for="user_password"><b>Password</b></label>
                <input autocomplete="off" id="user_password" type="password" placeholder="Enter Password" name="user_password" required>

                <button onclick="checkForm(false)" class="loginButton loginButton_user">Login</button>
            </div>
        </form>
    </div>
</body>

</html>
<?php
    mysqli_close($db);
?>