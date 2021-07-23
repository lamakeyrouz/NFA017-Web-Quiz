/**
 * Goes to questionary
 */
function goToQuestionary() {
    window.location.href = '../AdminQuestionary/adminQuestionary.php';
}

/**
 * Go To Questionary With Details
 * @param {Number} noQuestion 
 * 
 * Goes to the questionary form in edit mode and passes the id of the question selected to autocomplete and update the form
 */
function goToQuestionaryWithDetails(noQuestion) {
    if (noQuestion) {
        window.location.href = `../AdminQuestionary/adminQuestionary.php?questionId=${noQuestion}`;
    }
}