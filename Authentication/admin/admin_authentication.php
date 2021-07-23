<?php
    include '../../connect.php';

    if(isset($_POST["admin_email"])){
        $admin_email = $_POST["admin_email"];
        $admin_password = $_POST["admin_password"];
        $query = "SELECT * FROM `admin` WHERE `username` = '{$admin_email}'";
        $res = mysqli_query($db , $query);
        $row = mysqli_fetch_array($res);
        if(mysqli_num_rows($res) > 0 ){
            if(strcmp($admin_password ,$row["password"]) == 0){
                // Set cookies to save the type of login and to use the id of the admin/user
                setcookie("UserId","", time() - 3600 ,"/");
                setcookie("AdminId", $row["idadmin"], time() + (10 * 365 * 24 * 60 * 60), "/");
                header("Location: ../../AdminQuestionary/adminQuestionary.php");
            }else{
                echo "<script>alert('wrong username or password')</script>";
            }
        }else{
            echo "<script>alert('wrong username or password')</script>";
        }
    }
?>
<!DOCTYPE html>
<html class="background_admin">

<head>
    <title>Web Quiz</title>
    <link rel="stylesheet" href="../authentication.css" />
    <script src="../authentication.js"></script>
</head>

<body>
    <div id="login background_admin" class="login login_admin">
        <div class="back">
            <h1>Admin Login</h1>
            <div onclick="goToLanding()">
                <a><</a>
            </div>
        </div>
        <form id="admin_loginForm" class="loginContent" onsubmit="return false" autocomplete="off" method="post">
            <div class="loginContainer loginContainer_admin">
                <label for="admin_email"><b>Username</b></label>
                <input autocomplete="off" id="admin_email" type="text" placeholder="Enter Username" name="admin_email" required>

                <label for="admin_password"><b>Password</b></label>
                <input autocomplete="off" id="admin_password" type="password" placeholder="Enter Password" name="admin_password" required>

                <button onclick="checkForm(true)" class="loginButton loginButton_admin">Login</button>
            </div>
        </form>
    </div>
</body>

</html>
<?php
    mysqli_close($db);
?>