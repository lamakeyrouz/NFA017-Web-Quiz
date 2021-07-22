<?php
    include '../connect.php';

    // Get all languages
    $language_query = "SELECT * FROM langage";
    $language_res = mysqli_query($db , $language_query) or die( mysqli_error($db));

    // Difficulty array
    $difficulty_array = [];
    $language_name = null;

    if (isset($_COOKIE["UserId"])) {
        if(isset($_POST["Languages"])) {
            $language_name = $_POST["Languages"];
            $language_res = mysqli_query($db , $language_query) or die( mysqli_error($db));
    
            $get_test_results_query = "SELECT * FROM test JOIN langage ON (test.idLangage = langage.idLangage) WHERE langage.nomlangage = '{$_POST["Languages"]}' AND idabonne = '{$_COOKIE["UserId"]}' AND note >= 4 ORDER BY note DESC";
            $res_test_results = mysqli_query($db, $get_test_results_query);
        
            $difficulty_array = ["1"];
            if (mysqli_num_rows($res_test_results) > 0 ) {
                $highest_rating = 1;
                while($row_test_results = mysqli_fetch_array($res_test_results)) {
                    if ($row_test_results["niveau"] == 3) {
                        $highest_rating = 3;
                    }else if ($row_test_results["niveau"] == 2) {
                        if ($highest_rating <=2 ) {
                            $highest_rating = 2;
                        }
                    }
                }
                if ($highest_rating == 3) {
                    array_push($difficulty_array, "2");
                    array_push($difficulty_array, "3");
                }else {
                    array_push($difficulty_array, "2");
                }
            }
        }
    }else{
        header("Location: ../Landing/landing.html");
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Web Quiz</title>
    <link rel="stylesheet" href="./userHomePage.css" />
    <script src="./userHomePage.js"></script>
</head>

<body onload="onLoad('<?php echo $language_name; ?>')">
    <div class="main">
        <h1>Home Page</h1>
        <form id="home_form" name="home_form" onsubmit="return false" method="POST">
            <div class="language">
                <label for="Languages"><b>Langage</b></label>
                <div>
                    <select name="Languages" id="Languages" onchange="onValueChange();">
                        <option></option>
                        <?php
                            while($language_row = mysqli_fetch_array($language_res)){
                        ?>
                        <option id="<?php echo $language_row["idlangage"] ?>">
                            <?php echo $language_row["nomlangage"] ?>
                        </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="difficulty">
                <label for="difficulty"><b>Difficulté</b></label>
                <div>
                    <select name="difficulty" id="difficulty">
                        <option></option>
                        <?php
                            for ($i=0; $i < count($difficulty_array); $i++) {
                        ?>
                        <option id="<?php echo $difficulty_array[$i] ?>">
                            <?php echo $difficulty_array[$i] ?>
                        </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="button">
                <button onclick="checkForm()">Commencez Le Test</button>
                <button onclick="goToResults()"> Résultas</button>
            </div>
        </form>
    </div>
</body>

</html>
    <?php
    mysqli_close($db);
?>