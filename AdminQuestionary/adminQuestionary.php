<?php
    include '../connect.php';

    $language_query = "SELECT * FROM `langage`";
    $language_res = mysqli_query($db , $language_query) or die( mysqli_error($db));

    $difficulty_array = ["1", "2", "3"];
    $answers_numbers = [3, 4, 5];

    function notEmpty($string) {
        return strlen(trim($string)) > 0;
    }

    function deleteAll($database, $queries) {
        for ($i=0; $i < count($queries); $i++) { 
            mysqli_query($database , $queries[$i]) or die( mysqli_error($database));
        }
    }

    $questionary_object = '';
    $question_id = null;
    if (isset($_GET["questionId"]) && !isset($_GET["edited"])) {
        $question_id= $_GET["questionId"];
        $get_question_details_query = "SELECT * FROM `question` JOIN `langage` ON(question.idlangage = langage.idlangage) JOIN `admin` ON(question.idadmin = admin.idadmin) WHERE `noquestion` = '$question_id'";
        $get_all_answers_query = "SELECT * FROM `reponse` WHERE `noquestion` = '$question_id'";

        $res_question_details = mysqli_query($db , $get_question_details_query) or die( mysqli_error($db));
        $res_all_answers = mysqli_query($db , $get_all_answers_query) or die( mysqli_error($db));

        $row_question_details = mysqli_fetch_array($res_question_details);

        if(mysqli_num_rows($res_question_details) > 0) {
            $templanguage = $row_question_details['nomlangage'];
            $tempdifficulty = $row_question_details['niveau'];
            $tempquestion = $row_question_details['enonce'];
            if(mysqli_num_rows($res_all_answers) > 0) {
                $answers = array();
                $answer_right = "";
                while($answer_row = mysqli_fetch_array($res_all_answers)) {
                    array_push($answers, $answer_row["texte"]);
                    if ($answer_row["correct"] == 1) {
                        $answer_right = $answer_row["texte"];
                    }
                }
                $temp_array = array("language"=>$templanguage, "difficulty"=>$tempdifficulty, "question"=>$tempquestion, "answers"=>$answers, "rightAnswer"=>"$answer_right");
                $questionary_object = json_encode($temp_array);
            }
        }
    }
    if(isset($_POST["Languages"]) && isset($_POST["difficulty"]) && isset($_POST["question"])){
        $edited = false;
        $questionId = null;
        if (isset($_GET["edited"]) && isset($_GET["questionId"])) {
            $edited = $_GET["edited"];
            $questionId = isset($_GET["questionId"]);
        }
        $language = $_POST["Languages"];
        $difficulty = $_POST["difficulty"];
        $question = $_POST["question"];
        $answersCount = $_POST["answers_count"];

        $get_language_id_query = "SELECT * FROM `langage` WHERE `nomlangage` = '$language'";
        $res_language_id = mysqli_query($db , $get_language_id_query) or die( mysqli_error($db));
        $row_language = mysqli_fetch_array($res_language_id);

        $something_wrong_string = "<script>alert('Something went wrong, please try again later')</script>";
        $something_wrong_string_update = "<script>alert('Something went wrong, please try again later');</script>";

        if(mysqli_num_rows($res_language_id) > 0 ){
            if(isset($_COOKIE["AdminId"]) && notEmpty($_COOKIE["AdminId"])) {
                $get_all_questions = "SELECT * FROM `question` WHERE `enonce` = '$question' AND `niveau` = '$difficulty' AND `idlangage` = '{$row_language["idlangage"]}' AND `idadmin` = '{$_COOKIE["AdminId"]}'";
                $res_all_questions_temp = mysqli_query($db , $get_all_questions) or die( mysqli_error($db));

                if (mysqli_num_rows($res_all_questions_temp) >= 1 && !$edited && !$questionId) {
                    echo "<script>alert('Form submitted successfully');window.location.href = '../QuestionsList/questionList.php';</script>";
                    return;
                }

                $insert_query = "INSERT INTO question(enonce, niveau, idlangage, idadmin) VALUES ('{$question}', '{$difficulty}', '{$row_language["idlangage"]}', '{$_COOKIE["AdminId"]}')";
                $update_query = "UPDATE question SET enonce = '{$question}', niveau = '{$difficulty}', idlangage = '{$row_language["idlangage"]}', idadmin = '{$_COOKIE["AdminId"]}' WHERE noquestion = '{$questionId}'";
                
                if($edited && $questionId) {
                    $res_update_query = mysqli_query($db, $update_query) or die( mysqli_error($db));

                    if(!$res_update_query) {
                        echo $something_wrong_string_update;
                    }
                }else{
                    $res_insert_query = mysqli_query($db , $insert_query) or die( mysqli_error($db));

                    if(!$res_insert_query) {
                        echo $something_wrong_string;
                        return;
                    }
                }


                $res_all_questions = mysqli_query($db , $get_all_questions) or die( mysqli_error($db));
                $row_questions = mysqli_fetch_array($res_all_questions);

                $firstAnswer = $_POST["answer_1"];
                $secondAnswer = $_POST["answer_2"];
                $thirdAnswer = $_POST["answer_3"];
                $fourthAnswer = $_POST["answer_4"];
                $fifthAnswer = $_POST["answer_5"];
                $rightAnswer = $_POST["right_answer"];

                $correct = $rightAnswer == 1 ? 1 : 0;
                $correct_2 = $rightAnswer == 2 ? 1 : 0;
                $correct_3 = $rightAnswer == 3 ? 1 : 0;
                $correct_4 = $rightAnswer == 4 ? 1 : 0;
                $correct_5 = $rightAnswer == 5 ? 1 : 0;

                $delete_queries = [
                    "DELETE FROM question WHERE `enonce` = '$question' AND `niveau` = '$difficulty' AND `idlangage` = '{$row_language["idlangage"]}' AND `idadmin` = '{$_COOKIE["AdminId"]}'", 
                    "DELETE FROM reponse WHERE `texte` = {$firstAnswer} AND `correct` = '{$correct}' AND `noquestion` = '{$row_questions["noquestion"]}'",
                    "DELETE FROM reponse WHERE `texte` = {$secondAnswer} AND `correct` = '{$correct_2}' AND `noquestion` = '{$row_questions["noquestion"]}'",
                    "DELETE FROM reponse WHERE `texte` = {$thirdAnswer} AND `correct` = '{$correct_3}' AND `noquestion` = '{$row_questions["noquestion"]}'",
                    "DELETE FROM reponse WHERE `texte` = {$fourthAnswer} AND `correct` = '{$correct_4}' AND `noquestion` = '{$row_questions["noquestion"]}'",
                    "DELETE FROM reponse WHERE `texte` = {$fifthAnswer} AND `correct` = '{$correct_5}' AND `noquestion` = '{$row_questions["noquestion"]}'"
                ];

                if(notEmpty($rightAnswer) && $row_questions && mysqli_num_rows($res_all_questions) == 1) {
                    if(notEmpty($firstAnswer)) {
                        if($edited && $questionId) {
                            $get_all_reponse = "SELECT * FROM `reponse` WHERE `noquestion` = '{$questionId}' AND `texte` = '{$firstAnswer}' AND `correct` = '{$correct}' AND `noquestion` = '{$row_questions["noquestion"]}'";
                            $res_get_all_reponse = mysqli_query($db , $get_all_reponse) or die( mysqli_error($db));
                            $row_all_reponse = mysqli_fetch_array($res_get_all_reponse);

                            if(mysqli_num_rows($res_get_all_reponse) == 1 ) {
                                $update_answer = "UPDATE reponse SET texte = {$firstAnswer}, correct = '{$correct}', noquestion = '{$row_questions["noquestion"]}' WHERE noreponse = '{$row_all_reponse["noreponse"]}' ";
                                $res_update_answer = mysqli_query($db , $update_answer) or die( mysqli_error($db));
                                if(!$res_update_answer) {
                                    echo $something_wrong_string_update;
                                    return;
                                }
                            }else{
                                echo $something_wrong_string_update;
                            }
                            
                           
                        }else{
                            $insert_answer = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('{$firstAnswer}', '{$correct}', '{$row_questions["noquestion"]}')";
                            $res_answers = mysqli_query($db , $insert_answer) or die( mysqli_error($db));
                            if(!$res_answers) {
                                echo $something_wrong_string;
                                deleteAll($db, $delete_queries);
                                return;
                            }
                        }
                    }
                    if(notEmpty($secondAnswer)) {
                        
                        if($edited && $questionId) {
                            $get_all_reponse_2 = "SELECT * FROM `reponse` WHERE `noquestion` = '{$questionId}' AND `texte` = {$secondAnswer} AND `correct` = '{$correct_2}' AND `noquestion` = '{$row_questions["noquestion"]}'";
                            $res_get_all_reponse_2 = mysqli_query($db , $get_all_reponse_2) or die( mysqli_error($db));
                            $row_all_reponse_2 = mysqli_fetch_array($res_get_all_reponse_2);

                            if(mysqli_num_rows($res_get_all_reponse_2) == 1 ) {
                                $update_answer_2 = "UPDATE reponse SET texte = {$secondAnswer}, correct = '{$correct_2}', noquestion = '{$row_questions["noquestion"]}' WHERE noreponse = '{$row_all_reponse_2["noreponse"]}' ";
                                $res_update_answer_2 = mysqli_query($db , $update_answer_2) or die( mysqli_error($db));
                                if(!$res_update_answer_2) {
                                    echo $something_wrong_string_update;
                                    return;
                                }
                            }else{
                                echo $something_wrong_string_update;
                            }
                            
                           
                        } else {
                            $insert_answer_2 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('{$secondAnswer}', '{$correct_2}', '{$row_questions["noquestion"]}')";
                            $res_answers_2 = mysqli_query($db , $insert_answer_2) or die( mysqli_error($db));
                            if(!$res_answers_2) {
                                echo $something_wrong_string;
                                return;
                            }
                        }
                    }
                    if(notEmpty($thirdAnswer)) {
                        if($edited && $questionId) {
                            $get_all_reponse_3 = "SELECT * FROM `reponse` WHERE `noquestion` = '{$questionId}' AND `texte` = {$thirdAnswer} AND `correct` = '{$correct_3}' AND `noquestion` = '{$row_questions["noquestion"]}'";
                            $res_get_all_reponse_3 = mysqli_query($db , $get_all_reponse_3) or die( mysqli_error($db));
                            $row_all_reponse_3 = mysqli_fetch_array($res_get_all_reponse_3) or die( mysqli_error($db));

                            if(mysqli_num_rows($res_get_all_reponse_3) == 1 ) {
                                $update_answer_3 = "UPDATE reponse SET `texte` = {$thirdAnswer} AND `correct` = '{$correct_3}' AND `noquestion` = '{$row_questions["noquestion"]}' WHERE noreponse = '{$row_all_reponse_3["noreponse"]}' ";
                                $res_update_answer_3 = mysqli_query($db , $update_answer_3) or die( mysqli_error($db));
                                if(!$res_update_answer_3) {
                                    echo $something_wrong_string_update;
                                    return;
                                }
                            }else{
                                echo $something_wrong_string_update;
                            }
                            
                           
                        } else {
                            $insert_answer_3 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('{$thirdAnswer}', '{$correct_3}', '{$row_questions["noquestion"]}')";
                            $res_answers_3 = mysqli_query($db , $insert_answer_3) or die( mysqli_error($db));
                            if(!$res_answers_3) {
                                echo $something_wrong_string;
                                mysqli_query($db , $delete_query) or die( mysqli_error($db));
                                return;
                            }
                        }
                    }
                    if(notEmpty($fourthAnswer)) {

                        if($edited && $questionId) {
                            $get_all_reponse_4 = "SELECT * FROM `reponse` WHERE `noquestion` = '{$questionId}' AND `texte` = {$fourthAnswer} AND `correct` = '{$correct_4}' AND `noquestion` = '{$row_questions["noquestion"]}'";
                            $res_get_all_reponse_4 = mysqli_query($db , $get_all_reponse_4) or die( mysqli_error($db));
                            $row_all_reponse_4 = mysqli_fetch_array($res_get_all_reponse_4);

                            if(mysqli_num_rows($res_get_all_reponse_4) == 1 ) {
                                $update_answer_4 = "UPDATE reponse SET `texte` = {$fourthAnswer} AND `correct` = '{$correct_4}' AND `noquestion` = '{$row_questions["noquestion"]}' WHERE noreponse = '{$row_all_reponse_4["noreponse"]}' ";
                                $res_update_answer_4 = mysqli_query($db , $update_answer_4) or die( mysqli_error($db));
                                if(!$res_update_answer_4) {
                                    echo $something_wrong_string_update;
                                    return;
                                }
                            }else{
                                echo $something_wrong_string_update;
                            }
                            
                           
                        } else {
                            $insert_answer_4 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('{$fourthAnswer}', '{$correct_4}', '{$row_questions["noquestion"]}')";
                            $res_answers_4 = mysqli_query($db , $insert_answer_4) or die( mysqli_error($db));
                            if(!$res_answers_4) {
                                echo $something_wrong_string;
                                mysqli_query($db , $delete_query) or die( mysqli_error($db));
                                return;
                            }
                        }
                    }
                    if(notEmpty($fifthAnswer)) {

                        if($edited && $questionId) {
                            $get_all_reponse_5 = "SELECT * FROM `reponse` WHERE `noquestion` = '{$questionId}' AND `texte` = {$fifthAnswer}' AND `correct` = '{$correct_5}' AND `noquestion` = '{$row_questions["noquestion"]}'";
                            $res_get_all_reponse_5 = mysqli_query($db , $get_all_reponse_5) or die( mysqli_error($db));
                            $row_all_reponse_5 = mysqli_fetch_array($res_get_all_reponse_5);

                            if(mysqli_num_rows($res_get_all_reponse_5) == 1 ) {
                                $update_answer_5 = "UPDATE reponse SET `texte` = {$fifthAnswer} AND `correct` = '{$correct_5}' AND `noquestion` = '{$row_questions["noquestion"]}' WHERE noreponse = '{$row_all_reponse_5["noreponse"]}' ";
                                $res_update_answer_5 = mysqli_query($db , $update_answer_5) or die( mysqli_error($db));
                                if(!$res_update_answer_5) {
                                    echo $something_wrong_string_update;
                                    return;
                                }
                            }else{
                                echo $something_wrong_string_update;
                            }
                            
                           
                        } else {
                            $insert_answer_5 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('{$fifthAnswer}', '{$correct_5}', '{$row_questions["noquestion"]}')";
                            $res_answers_5 = mysqli_query($db , $insert_answer_5) or die( mysqli_error($db));
                            if(!$res_answers_5) {
                                echo $something_wrong_string;
                                mysqli_query($db , $delete_query) or die( mysqli_error($db));
                                return;
                            }
                        }
                    }
                    echo "<script>alert('Form submitted successfully');window.location.href = '../QuestionsList/questionList.php';</script>";
                } else {
                    echo $something_wrong_string;
                }
            } else {
                header("Location: ../Landing/landing.html");
            }
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Web Quiz</title>
    <link rel="stylesheet" href="./adminQuestionary.css" />
    <script src="./adminQuestionary.js"></script>
</head>

<body onload='checkData(<?php echo $questionary_object; ?>)'>
    <div class="main">
        <div class="back">
            <h1>Questionary</h1>
            <div onclick="goToLanding()" class="back">
                <a><</a>
            </div>
        </div>
        <form id="questionary_form" name="questionary_form" action="./adminQuestionary.php?edited=<?php echo $question_id ? true : false; ?>&questionId=<?php echo $question_id  ?>" onsubmit="return false" method="POST">
            <div class="language">
                <label for="Languages"><b>Languages</b></label>
                <div>
                    <select name="Languages" id="Languages">
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
                <label for="difficulty"><b>Difficulty Level</b></label>
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

            <div class="question">
                <label for="question"><b>Question</b></label>
                <input autocomplete="off" id="question" type="text" placeholder="Enter Question" name="question" required>
            </div>

            <div class="answers_count">
                <label for="answers_count"><b>Number Of Answers</b></label>
                <div>
                    <select name="answers_count" id="answers_count" onchange="onValueChange(this);">
                        <option></option>
                        <?php
                            for ($i=0; $i < count($answers_numbers); $i++) {
                        ?>
                        <option id="<?php echo $answers_numbers[$i] ?>">
                            <?php echo $answers_numbers[$i] ?>
                        </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div id="answer_1" class="answer_1 answers">
                <label for="answer_1"><b>First Answer</b></label>
                <input class="input_answer" autocomplete="off" id="answer_1" type="text" placeholder="Enter The First Answer" name="answer_1" required>
            </div>

            <div id="answer_2" class="answer_2 answers">
                <label for="answer_2"><b>Second Answer</b></label>
                <input class="input_answer" autocomplete="off" id="answer_2" type="text" placeholder="Enter The Second Answer" name="answer_2" required>
            </div>

            <div id="answer_3" class="answer_3 answers">
                <label for="answer_3"><b>Third Answer</b></label>
                <input class="input_answer" autocomplete="off" id="answer_3" type="text" placeholder="Enter The Third Answer" name="answer_3" required>
            </div>


            <div id="answer_4" class="answer_4 answers">
                <label for="answer_4"><b>Fourth Answer</b></label>
                <input class="input_answer" autocomplete="off" id="answer_4" type="text" placeholder="Enter The Fourth Answer" name="answer_4" required>
            </div>

            <div id="answer_5" class="answer_5 answers">
                <label for="answer_5"><b>Fifth Answer</b></label>
                <input class="input_answer" autocomplete="off" id="answer_5" type="text" placeholder="Enter The Fifth Answer" name="answer_5" required>
            </div>

            <div class="right_answer">
                <label id="right_answer_label" for="right_answer"><b>Select The Number Of The Right Answer</b></label>
                <div>
                    <select name="right_answer" id="right_answer">
                    </select>
                </div>
            </div>

            <div class="button">
                <button onclick="checkForm()" class="">Modification</button>
            </div>
        </form>
    </div>
</body>

</html>
    <?php
    mysqli_close($db);
?>