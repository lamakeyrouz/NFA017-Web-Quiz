<?php
    include '../connect.php';

    $query = "SELECT * FROM `question`";
    $res = mysqli_query($db , $query);

    function notEmpty($string) {
        return strlen(trim($string)) > 0;
    }

    if(!(mysqli_num_rows($res) > 0)){
        echo "<script>alert('Something went wrong');window.location.href = '../Landing/landing.html';</script>";
        return;
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
                while ($row = mysqli_fetch_array($res)) {
                    if (notEmpty($row["enonce"])) {
            ?>
                <div class="question" id="<?php echo $row["noquestion"]; ?>" onclick="goToQuestionaryWithDetails(<?php echo $row['noquestion'] ?>)" >
                    <input readonly type="text" value='<?php echo 'Énoncé: '.$row["enonce"]; ?>'></input>
                </div>
            <?php
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