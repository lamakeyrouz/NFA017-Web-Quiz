<?php
    include '../connect.php';

    $query = "SELECT * FROM `test`";
    $res = mysqli_query($db , $query);
    $no_data = false;

    // Checks if string is empty or blank
    function notEmpty($string) {
        return strlen(trim($string)) > 0;
    }

    if(!(mysqli_num_rows($res) > 0)){
        $no_data = true;
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Web Quiz</title>
    <link rel="stylesheet" href="./results.css" />
    <script src="./results.js"></script>
</head>

<body>
    <div class="main">
        <div class="back">
            <h1>Résultas</h1>
            <div onclick="goToHome()">
                <a><</a>
            </div>
        </div>
        <div class="main_container">
            <?php
                if($no_data) {
            ?>
                    <div>  
                        <h2>No Data Found</h2>
                    </div>
            <?php
                }else{
                    while ($row = mysqli_fetch_array($res)) {
                        if (notEmpty($row["datetest"]) && notEmpty($row["note"])) {
            ?>
                    <div class="question" id="<?php echo $row["notest"]; ?>">
                        <input readonly type="text" value='<?php echo 'Note: '.$row["note"].'/5 Éffectuer le: '.$row["datetest"]; ?>'></input>
                    </div>
            <?php
                        }
                    }
                }
            ?>
        </div>
    </div>
</body>

</html>
<?php
    mysqli_close($db);
?>