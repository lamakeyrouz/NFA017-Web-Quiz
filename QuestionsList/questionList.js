function goToQuestionary() {
    window.location.href = '../AdminQuestionary/adminQuestionary.php';
}

function goToQuestionaryWithDetails(noQuestion) {
    if (noQuestion) {
        window.location.href = `../AdminQuestionary/adminQuestionary.php?questionId=${noQuestion}`;
    }
}