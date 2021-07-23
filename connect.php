<?php
    /**
    * This file is included at the start of every file that need to connect to the database
    */
    $db = mysqli_connect("localhost","root","","web_quiz");
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: ".mysqli_connect_error();
    }
?>