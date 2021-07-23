/**
 * Helper class to store the data of the quiz
 */
class Data {
    static data_array = [];
    static currentSlide = 0;
}

/**
 * Helper to build the quiz and show them in html elements
 */
function buildQuiz() {
    Data.currentSlide = 0;
    const quizContainer = document.getElementById('quiz');

    const output = [];

    Data.data_array.forEach(
        (currentQuestion, questionNumber) => {

            const answers = [];

            for (let i = 0; i < currentQuestion.answers.length; i++) {

                answers.push(
                    `<label>
                        <input class="radio" type="radio" id="${currentQuestion.answers_ids[i]}" name="question${questionNumber}" value="${currentQuestion.answers[i]}">
                        ${currentQuestion.answers[i]}
                    </label>`
                );
            }

            output.push(
                `<div class="slide">
                        <div class="question"> Énoncé: ${currentQuestion.question} </div>
                        <div class="answers"> ${answers.join("")} </div>
                    </div>`
            );
        }
    );
    quizContainer.innerHTML = output.join('');
}

/**
 * Function to help count the correct answers and submit them to the hidden form by "post" method for better security
 */
function showResults() {
    const quizContainer = document.getElementById('quiz');
    const quiz_form = document.getElementById('quiz_form');
    const answerContainers = quizContainer.querySelectorAll('.answers');

    let numCorrect = 0;
    Data.data_array.forEach((currentQuestion, questionNumber) => {

        const answerContainer = answerContainers[questionNumber];
        const selector = `input[name=question${questionNumber}]:checked`;
        const userAnswer = (answerContainer.querySelector(selector) || {}).id;

        if (userAnswer === currentQuestion.correct_answer) {
            numCorrect++;
        }
    });

    const user_result = document.getElementById('user_result');
    const date = document.getElementById('date');
    const langage = document.getElementById('langage');
    const difficulty = document.getElementById('difficulty');
    user_result.value = numCorrect;

    let date_value = new Date();

    var dd = String(date_value.getDate()).padStart(2, '0');
    var mm = String(date_value.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = date_value.getFullYear();

    date_value = yyyy + '-' + mm + '-' + dd;

    date.value = date_value;
    langage.value = Data.data_array.length > 0 ? Data.data_array[0].language : '';
    difficulty.value = Data.data_array.length > 0 ? Data.data_array[0].difficulty : '';
    quiz_form.submit();

}

/**
 * Show Slides
 * @param {Number} n 
 * 
 * Shows the specific slide and button according to it's index
 */
function showSlide(n) {
    const previousButton = document.getElementById("previous");
    const nextButton = document.getElementById("next");
    const slides = document.querySelectorAll(".slide");
    const submitButton = document.getElementById('submit');

    slides[Data.currentSlide].classList.remove('active-slide');
    slides[n].classList.add('active-slide');
    Data.currentSlide = n;
    if (Data.currentSlide === 0) {
        previousButton.style.display = 'none';
    } else {
        previousButton.style.display = 'inline-block';
    }
    if (Data.currentSlide === slides.length - 1) {
        nextButton.style.display = 'none';
        submitButton.style.display = 'inline-block';
    } else {
        nextButton.style.display = 'inline-block';
        submitButton.style.display = 'none';
    }
}

/**
 * Shows next slide
 */
function showNextSlide() {
    showSlide(Data.currentSlide + 1);
}

/**
 * Shows previous slide
 */
function showPreviousSlide() {
    showSlide(Data.currentSlide - 1);
}

/**
 * Save Data
 * @param {JSON} data 
 * 
 * Saves the data in form of an array to our Data class
 */
function saveData(data) {
    if (data) {
        var jsonArray = JSON.parse(JSON.stringify(data));
        Data.data_array = jsonArray;
        buildQuiz();
        showSlide(Data.currentSlide);
    } else {
        alert('Something went wrong, please try again later');
        window.location.href = '../UserHomePage/userHomePage.php';
    }
}