function onValueChange() {
    var form = document.getElementById("home_form");
    form.submit();
}

function isEmpty(str) {
    return (!str || str.length === 0);
}

function onLoad(language) {
    if (language) {
        document.getElementById("Languages").value = language;
    }
}

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

function goToResults() {
    window.location.href = '../Results/results.php';
}