/**
 * Called when value of number of answers changes so we're able to set the array of the number of the correct answer
 */
function onValueChange() {
    var form = document.getElementById("home_form");
    form.submit();
}

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
 * On Load
 * @param {String} language
 * 
 * Autofill the language if it was submitted to show the difficulty levels specific to that language
 */
function onLoad(language) {
    if (language) {
        document.getElementById("Languages").value = language;
    }
}

/**
 * Validates the form before submit
 */
function checkForm() {
    var form = document.getElementById("home_form");
    var language = document.getElementById("Languages");
    var difficulty = document.getElementById("difficulty");

    if (isEmpty(language.value)) {
        alert("Please select a language");
        return;
    }
    if (isEmpty(difficulty.value)) {
        alert("Please select a difficulty");
        return;
    }

    form.action = "../UserTest/userTest.php"
    form.submit();
}

/**
 * Goes to results
 */
function goToResults() {
    window.location.href = '../Results/results.php';
}