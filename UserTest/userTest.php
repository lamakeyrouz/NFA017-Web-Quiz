<?php
    include '../connect.php';

    $something_wrong = "<script>alert('Something went wrong, please try again later');window.location.href = '../UserHomePage/userHomePage.php';</script>";

    if (isset($_POST["Languages"]) && isset($_POST["difficulty"])) {
        $language = $_POST["Languages"];
        $difficulty = $_POST["difficulty"];
        
        // Get all questions based on language and difficulty
        $check_tests_query = "SELECT * FROM question JOIN langage ON (question.idlangage = langage.idlangage) WHERE nomlangage = '$language' AND niveau = $difficulty";
        $check_tests_res = mysqli_query($db, $check_tests_query);

        $quiz = array();
        $quiz_object = null;

        // If we don't have more than 5 question for that language or difficulty send the user back to the home page
        if (mysqli_num_rows($check_tests_res) >= 5) {

            // Select 5 random questions
            $random_questions_query = "SELECT * FROM question order by RAND() limit 5";
            $res = mysqli_query($db, $random_questions_query);

            if($res) {
                while($question_row = mysqli_fetch_array($res)) {
                    $no_question = $question_row["noquestion"];
                    $answers_query = "SELECT * FROM reponse WHERE noquestion = $no_question";
                    $answers_res = mysqli_query($db, $answers_query);
                    if($answers_res){
                        // Builds the associative array to pass it to the javascript BuildQuiz function with the questions, answers and the right answer
                        $answer_array = array();
                        $answer_array_ids = array();
                        $correct = null;
                        while($row_answers = mysqli_fetch_array($answers_res)){
                            array_push($answer_array, $row_answers["texte"]);
                            array_push($answer_array_ids, $row_answers["noreponse"]);
                            if ($row_answers["correct"] == 1) {
                                $correct = $row_answers["noreponse"];
                            }
                        }
                        $temp_array = array("language" => $question_row["idlangage"], "difficulty" => $difficulty, "question" => $question_row["enonce"], "answers" => $answer_array, "answers_ids" => $answer_array_ids, "correct_answer" => $correct);
                        array_push($quiz, $temp_array);
                    }else{
                        echo $something_wrong;
                        return;
                    }
                }
                $quiz_object = json_encode($quiz);
            } else {
                echo $something_wrong;
                return;
            }
        }else{
            echo "<script>alert('Questions found for that language are not enough to take a test, please choose another option or try again later');window.location.href = '../UserHomePage/userHomePage.php';</script>";
            return;
        }
    } else if (isset($_POST["user_result"]) && isset($_POST["date"]) && isset($_POST["langage"]) && isset($_POST["difficulty"]) && isset($_COOKIE["UserId"])) {
        $user_result = $_POST["user_result"];
        $date = $_POST["date"];
        $langage = $_POST["langage"];
        $difficulty = $_POST["difficulty"];
        $UserId = $_COOKIE["UserId"];
        $date_temp=date("Y-m-d",strtotime($date));

        // Insert the results into the test table
        $query = "INSERT INTO test(note, datetest, idlangage, niveau, idabonne) VALUES ('$user_result', '$date_temp', '$langage', '$difficulty', '$UserId')";
        $query_result = mysqli_query($db, $query) or die(mysqli_error($db));
        if($query_result) {
            echo "<script>alert('Your results are: ".$user_result."/5');window.location.href = '../UserHomePage/userHomePage.php'</script>";
        }else{
            echo $something_wrong;
        }
    } else {
        echo $something_wrong;
        return;
    }
?>


<!DOCTYPE html>
<html>

<head>
    <title>Web Quiz</title>
    <link rel="stylesheet" href="./userTest.css" />
    <script src="./userTest.js"></script>
</head>

<body onload='saveData(<?php echo $quiz_object; ?>)'>
    <h1>Quiz</h1>
    <form id="quiz_form" name="quiz_form" onsubmit="return false" method="POST">
        <input class="hidden_input" type="text" value="" id="user_result" name="user_result" />
        <input class="hidden_input" type="text" value="" id="date" name="date" />
        <input class="hidden_input" type="text" value="" id="langage" name="langage" />
        <input class="hidden_input" type="text" value="" id="difficulty" name="difficulty" />
    </form>
    <div class="quizContainer">
        <div id="quiz"></div>
    </div>
    <div class="buttonContainer">
        <button id="previous" onclick="showPreviousSlide()">Question Précédente</button>
        <button id="next" onclick="showNextSlide()">Question Suivante</button>
        <button id="submit" onclick="showResults()">Afficher Résultat</button>
    </div>
</body>

</html>
    <?php
    mysqli_close($db);
?>