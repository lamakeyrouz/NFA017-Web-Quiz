<?php
    include '../connect.php';

    // Get all languages
    $language_query = "SELECT * FROM langage";
    $language_res = mysqli_query($db , $language_query) or die( mysqli_error($db));

    // Difficulty array
    $difficulty_array = ["1", "2", "3"];
    // Answers Numbers array 
    $answers_numbers = [3, 4, 5];

    // Check if string is empty or has only white space
    function notEmpty($string) {
        return strlen(trim($string)) > 0;
    }

    // Delete the inserted rows
    function deleteAll($database, $queries) {
        for ($i=0; $i < count($queries); $i++) { 
            mysqli_query($database , $queries[$i]) or die( mysqli_error($database));
        }
    }

    // Variable to autocomplete the form
    $questionary_object = '';
    $question_id = null;

    // Check if should autocomplete the form (admin came from questionList)
    if (isset($_GET["questionId"]) && !isset($_GET["edited"])) {
        $question_id= $_GET["questionId"];

        $get_question_details_query = "SELECT * FROM question JOIN langage ON(question.idlangage = langage.idlangage) JOIN admin ON(question.idadmin = admin.idadmin) WHERE noquestion = $question_id";
        $get_all_answers_query = "SELECT * FROM reponse WHERE noquestion = $question_id";

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

    // If user submitted the form
    if(isset($_POST["Languages"]) && isset($_POST["difficulty"]) && isset($_POST["question"])){
        $edited = false;

        // If user submitted the autocompleted form to edit rows
        if (isset($_GET["edited"]) && isset($_GET["questionId"])) {
            $edited = $_GET["edited"];
            $question_id = $_GET["questionId"];
        }

        $language = $_POST["Languages"];
        $difficulty = $_POST["difficulty"];
        $question = $_POST["question"];
        $answersCount = $_POST["answers_count"];

        $get_language_id_query = "SELECT * FROM langage WHERE nomlangage = '$language'";
        $res_language_id = mysqli_query($db , $get_language_id_query) or die( mysqli_error($db));
        $row_language = mysqli_fetch_array($res_language_id);

        $id_language = $row_language["idlangage"];
        $admin_id = $_COOKIE["AdminId"];

        $something_wrong_string = "<script>alert('Something went wrong, please try again later');window.location.href = '../QuestionsList/questionList.php';</script>";

        if(mysqli_num_rows($res_language_id) > 0 ){
            // Check if user is signed in as admin
            if(isset($_COOKIE["AdminId"]) && notEmpty($_COOKIE["AdminId"])) {
                $get_all_questions = $question_id ? "SELECT * FROM question WHERE noquestion = $question_id" : "SELECT * FROM question WHERE enonce = '$question' AND niveau = $difficulty AND idlangage = $id_language AND idadmin = $admin_id";
                $res_all_questions = mysqli_query($db , $get_all_questions) or die( mysqli_error($db));
            
                // Check if row already exists to not have duplicates
                if (mysqli_num_rows($res_all_questions) >= 1 && !$edited && !$question_id) {
                    echo "<script>alert('Form submitted successfully');window.location.href = '../QuestionsList/questionList.php';</script>";
                    return;
                }

                $insert_query = "INSERT INTO question(enonce, niveau, idlangage, idadmin) VALUES ('$question', $difficulty, $id_language, $admin_id)";
                $update_query = "UPDATE question SET enonce = '$question', niveau = $difficulty, idlangage = $id_language, idadmin = $admin_id WHERE noquestion = $question_id";
                
                // if edited update the row ...
                if($edited && $question_id) {
                    $res_update_query = mysqli_query($db, $update_query) or die( mysqli_error($db));

                    if(!$res_update_query) {
                        echo $something_wrong_string;
                    }
                }else{
                    // else insert the row
                    $res_insert_query = mysqli_query($db , $insert_query) or die( mysqli_error($db));

                    if(!$res_insert_query) {
                        echo $something_wrong_string;
                        return;
                    }

                    $res_all_questions_temp = mysqli_query($db , $get_all_questions) or die( mysqli_error($db));
                    $row_all_question = mysqli_fetch_array($res_all_questions_temp);

                    if(!$question_id) {
                        $question_id = $row_all_question["noquestion"];
                    }
                }

                // get each answer
                $firstAnswer = $_POST["answer_1"];
                $secondAnswer = $_POST["answer_2"];
                $thirdAnswer = $_POST["answer_3"];
                $fourthAnswer = $_POST["answer_4"];
                $fifthAnswer = $_POST["answer_5"];
                $rightAnswer = $_POST["right_answer"];

                // get the correct value of each answer
                $correct = $rightAnswer == 1 ? 1 : 0;
                $correct_2 = $rightAnswer == 2 ? 1 : 0;
                $correct_3 = $rightAnswer == 3 ? 1 : 0;
                $correct_4 = $rightAnswer == 4 ? 1 : 0;
                $correct_5 = $rightAnswer == 5 ? 1 : 0;

                if(notEmpty($rightAnswer) && mysqli_num_rows($res_all_questions_temp) > 0) {
                    

                    $get_all_reponse = "SELECT * FROM reponse WHERE noquestion = $question_id ORDER BY noreponse ASC";
                    $res_get_all_reponse = mysqli_query($db , $get_all_reponse) or die( mysqli_error($db));

                    $arrayofrows = array();

                    while($row_all_reponse = mysqli_fetch_array($res_get_all_reponse)) {
                        array_push($arrayofrows, $row_all_reponse);
                    }

                    if(notEmpty($firstAnswer)) {
                        // if edited update the row ...
                        if($edited && $question_id && count($arrayofrows) > 0) {
                            $no_reponse = $arrayofrows[0]["noreponse"];
                            $update_answer = "UPDATE reponse SET texte = '$firstAnswer', correct = $correct, noquestion = $question_id WHERE noreponse = $no_reponse";
                            $res_update_answer = mysqli_query($db , $update_answer) or die( mysqli_error($db));
                            if(!$res_update_answer) {
                                echo $something_wrong_string;
                                return;
                            }
                        }else{
                            // else insert the row.
                            $insert_answer = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('$firstAnswer', $correct, $question_id)";
                            $res_answers = mysqli_query($db , $insert_answer) or die( mysqli_error($db));
                            if(!$res_answers) {
                                echo $something_wrong_string;
                                return;
                            }
                        }
                    }
                    if(notEmpty($secondAnswer)) {
                        // if edited update the row ...
                        if($edited && $question_id && count($arrayofrows) > 1) {
                            $no_reponse_2 = $arrayofrows[1]["noreponse"];
                            $update_answer_2 = "UPDATE reponse SET texte = '$secondAnswer', correct = $correct_2, noquestion = $question_id WHERE noreponse = $no_reponse_2";
                            $res_update_answer_2 = mysqli_query($db , $update_answer_2) or die( mysqli_error($db));
                            if(!$res_update_answer_2) {
                                echo $something_wrong_string;
                                return;
                            }
                        } else {
                            // else insert the row.
                            $insert_answer_2 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('$secondAnswer', $correct_2, $question_id)";
                            $res_answers_2 = mysqli_query($db , $insert_answer_2) or die( mysqli_error($db));
                            if(!$res_answers_2) {
                                echo $something_wrong_string;
                                return;
                            }
                        }
                    }
                    if(notEmpty($thirdAnswer)) {
                        // if edited update the row ...
                        if($edited && $question_id && count($arrayofrows) > 2) {
                            $no_reponse_3 = $arrayofrows[2]["noreponse"];
                            $update_answer_3 = "UPDATE reponse SET texte = '$thirdAnswer', correct = $correct_3, noquestion = $question_id WHERE noreponse = $no_reponse_3 ";
                            $res_update_answer_3 = mysqli_query($db , $update_answer_3) or die( mysqli_error($db));
                            if(!$res_update_answer_3) {
                                echo $something_wrong_string;
                                return;
                            }
                        } else {
                            // else insert the row.
                            $insert_answer_3 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('$thirdAnswer', $correct_3, $question_id)";
                            $res_answers_3 = mysqli_query($db , $insert_answer_3) or die( mysqli_error($db));
                            if(!$res_answers_3) {
                                echo $something_wrong_string;
                                return;
                            }
                        }
                    }
                    if(notEmpty($fourthAnswer)) {
                        // if edited update the row ...
                        if($edited && $question_id && count($arrayofrows) > 3) {
                            $no_reponse_4 = $arrayofrows[3]["noreponse"];
                            $update_answer_4 = "UPDATE reponse SET texte = '$fourthAnswer', correct = $correct_4, noquestion = $question_id WHERE noreponse = $no_reponse_4 ";
                            $res_update_answer_4 = mysqli_query($db , $update_answer_4) or die( mysqli_error($db));
                            if(!$res_update_answer_4) {
                                echo $something_wrong_string;
                                return;
                            }
                        } else {
                            // else insert the row.
                            $insert_answer_4 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('$fourthAnswer', $correct_4, $question_id)";
                            $res_answers_4 = mysqli_query($db , $insert_answer_4) or die( mysqli_error($db));
                            if(!$res_answers_4) {
                                echo $something_wrong_string;
                                return;
                            }
                        }
                    } else {
                        // if user was editing the form and selected less answers than he did originally remove the row from the database
                        if (count($arrayofrows) > 3) {
                            $no_reponse_4_temp = $arrayofrows[3]["noreponse"];
                            $temp_query_4 = "DELETE FROM reponse WHERE noreponse = $no_reponse_4_temp";
                            $res_temp_4 = mysqli_query($db , $temp_query_4) or die( mysqli_error($db));
                        }
                    }
                    if(notEmpty($fifthAnswer)) {
                        // if edited update the row ...
                        if($edited && $question_id  && count($arrayofrows) > 4) {
                            $no_reponse_5 = $arrayofrows[4]["noreponse"];
                            $update_answer_5 = "UPDATE reponse SET texte = '$fifthAnswer', correct = $correct_5, noquestion = $question_id WHERE noreponse = $no_reponse_5 ";
                            $res_update_answer_5 = mysqli_query($db , $update_answer_5) or die( mysqli_error($db));
                            if(!$res_update_answer_5) {
                                echo $something_wrong_string;
                                return;
                            }
                        } else {
                            // else insert the row.
                            $insert_answer_5 = "INSERT INTO reponse(texte, correct, noquestion) VALUES ('$fifthAnswer', $correct_5, $question_id)";
                            $res_answers_5 = mysqli_query($db , $insert_answer_5) or die( mysqli_error($db));
                            if(!$res_answers_5) {
                                echo $something_wrong_string;
                                return;
                            }
                        }
                    } else {
                        // if user was editing the form and selected less answers than he did originally remove the row from the database
                        if (count($arrayofrows) > 4) {
                            $no_reponse_5_temp = $arrayofrows[4]["noreponse"];
                            $temp_query_5 = "DELETE FROM reponse WHERE noreponse = $no_reponse_5_temp";
                            $res_temp_5 = mysqli_query($db , $temp_query_5) or die( mysqli_error($db));
                        }
                    }

                    // success
                    echo "<script>alert('Form submitted successfully');window.location.href = '../QuestionsList/questionList.php';</script>";
                } else {
                    // error
                    echo $something_wrong_string;
                }
            } else {
                // not logged in as admin or cookie expired
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
            <h1>Questionnaire</h1>
            <div onclick="goToList()" class="back">
                <a><</a>
            </div>
        </div>
        <form id="questionary_form" name="questionary_form" action="./adminQuestionary.php?edited=<?php echo $question_id ? true : false; ?>&questionId=<?php echo $question_id  ?>" onsubmit="return false" method="POST">
            <div class="language">
                <label for="Languages"><b>Langage</b></label>
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

            <div class="question">
                <label for="question"><b>Question</b></label>
                <input autocomplete="off" id="question" type="text" placeholder="Enter Question" name="question" required>
            </div>

            <div class="answers_count">
                <label for="answers_count"><b>Nombre De Réponse</b></label>
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
                <label for="answer_1"><b>Première Reponse</b></label>
                <input class="input_answer" autocomplete="off" id="answer_1" type="text" placeholder="Ajoutez La Première Reponse" name="answer_1" required>
            </div>

            <div id="answer_2" class="answer_2 answers">
                <label for="answer_2"><b>Deuxième Reponse</b></label>
                <input class="input_answer" autocomplete="off" id="answer_2" type="text" placeholder="Ajoutez La Deuxième Reponse" name="answer_2" required>
            </div>

            <div id="answer_3" class="answer_3 answers">
                <label for="answer_3"><b>Troisième Reponse</b></label>
                <input class="input_answer" autocomplete="off" id="answer_3" type="text" placeholder="Ajoutez La Troisième Reponse" name="answer_3" required>
            </div>


            <div id="answer_4" class="answer_4 answers">
                <label for="answer_4"><b>Quatrième Reponse</b></label>
                <input class="input_answer" autocomplete="off" id="answer_4" type="text" placeholder="Ajoutez La Quatrième Reponse" name="answer_4" required>
            </div>

            <div id="answer_5" class="answer_5 answers">
                <label for="answer_5"><b>Cinquième Reponse</b></label>
                <input class="input_answer" autocomplete="off" id="answer_5" type="text" placeholder="Ajoutez La Cinquième Reponse" name="answer_5" required>
            </div>

            <div class="right_answer">
                <label id="right_answer_label" for="right_answer"><b>Selectionner Le Nombre De La Bonne Réponse</b></label>
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