/**
 * Is Empty
 * @param {String} str 
 * 
 * Check if string is empty or blanck
 */
function isEmpty(str) {
    return (!str || str.length === 0);
}

/**
 * Show Answers
 * @param {Number} number 
 * 
 * show answers fields according to number give
 */
function showAnswers(number) {
    var answers = document.getElementsByClassName("answers");
    var input_answers = document.getElementsByClassName("input_answer");
    if (number == 0) {
        for (const element of answers) {
            element.style.display = "none";
        }
    } else {
        for (let i = 0; i < answers.length; i++) {
            if (i < number) {
                answers[i].style.display = "flex";
            } else {
                answers[i].style.display = "none";
                input_answers[i].value = null;
            }
        }
    }
}

/**
 * On Value Change
 * @param {Number} number 
 * 
 * Called when value of number of answers changes so we're able to set the array of the number of the correct answer
 */
function onValueChange(number) {
    showAnswers(0);
    if (number && number.value) {
        var rightAnswer = document.getElementById("right_answer");
        rightAnswer.style.display = "flex";
        document.getElementById("right_answer_label").style.display = "flex";

        var html = "<option></option>";
        for (let i = 0; i < document.getElementById("answers_count").value; i++) {
            html = html + "<option id=" + (i + 1) + ">" + (i + 1) + "</option>";
        }
        rightAnswer.innerHTML = html;

        showAnswers(number.value);
    } else {
        showAnswers(0);
    }
}

/**
 * Validates the form before submit
 */
function checkForm() {
    var form = document.getElementById("questionary_form");
    var language = document.getElementById("Languages");
    var difficulty = document.getElementById("difficulty");
    var question = document.getElementById("question");
    var answers_count = document.getElementById("answers_count");
    var rightAnswer = document.getElementById("right_answer");
    var answers = document.getElementsByClassName("input_answer");
    if (isEmpty(language.value)) {
        alert("Please select a language");
        return;
    }
    if (isEmpty(difficulty.value)) {
        alert("Please select a difficulty");
        return;
    }
    if (isEmpty(question.value)) {
        alert("Question cannot be empty");
        return;
    }
    if (isEmpty(answers_count.value)) {
        alert("Please select the number of answers");
        return;
    }
    for (let i = 0; i < answers_count.value; i++) {
        if (isEmpty(answers[i].value)) {
            alert("Please fill all answers");
            return;
        }
    }
    if (isEmpty(rightAnswer.value)) {
        alert("Please select a right answer");
        return;
    }
    form.submit();
}

/**
 * Check Data
 * @param {JSON} data 
 * 
 * If the admin wants to edit the questionary and not add to it 
 * this function helps to autocomplete the fields according to the selected question
 */
function checkData(data) {
    document.getElementById("right_answer").style.display = "none";
    document.getElementById("right_answer_label").style.display = "none";
    if (data) {
        var jsonArray = JSON.parse(JSON.stringify(data));
        var language = document.getElementById("Languages");
        var difficulty = document.getElementById("difficulty");
        var question = document.getElementById("question");
        var answers_count = document.getElementById("answers_count");
        var rightAnswer = document.getElementById("right_answer");
        var answers = document.getElementsByClassName("input_answer");

        language.value = jsonArray.language;
        difficulty.value = jsonArray.difficulty;
        question.value = jsonArray.question;
        let length = jsonArray.answers.length;
        answers_count.value = length;

        rightAnswer.style.display = "flex";
        document.getElementById("right_answer_label").style.display = "flex";

        var html = "<option></option>";
        for (let i = 0; i < document.getElementById("answers_count").value; i++) {
            html = html + "<option id=" + (i + 1) + ">" + (i + 1) + "</option>";
        }
        rightAnswer.innerHTML = html;

        for (let i = 0; i < length; i++) {
            answers[i].value = jsonArray.answers[i];
            if (!isEmpty(jsonArray.rightAnswer) && jsonArray.rightAnswer == jsonArray.answers[i]) {
                rightAnswer.value = i + 1;
            }
        }

        showAnswers(0);
        showAnswers(length);
    } else {
        showAnswers(0);
    }
}

/**
 * Goes to question list view
 */
function goToList() {
    window.location.href = `../QuestionsList/questionList.php`;
}

/**
 * Goes to authentication view
 */
function goToAuthentication() {
    window.location.href = '../Authentication/admin/admin_authentication.php';
}