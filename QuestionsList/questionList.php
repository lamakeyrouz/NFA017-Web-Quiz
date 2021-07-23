<?php
    include '../connect.php';

    $query = "SELECT * FROM `question`";
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
    <link rel="stylesheet" href="./questionList.css" />
    <script src="./questionList.js"></script>
</head>

<body>
    <div class="main">
        <div class="back">
            <h1>Questions</h1>
            <div onclick="goToQuestionary()">
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
                        if (notEmpty($row["enonce"])) {
            ?>
                    <div class="question" id="<?php echo $row["noquestion"]; ?>" onclick="goToQuestionaryWithDetails(<?php echo $row['noquestion'] ?>)" >
                        <input readonly type="text" value='<?php echo 'Énoncé: '.$row["enonce"]; ?>'></input>
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