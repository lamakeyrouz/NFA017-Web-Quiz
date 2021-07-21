function goToLanding() {
    window.location.href = '../../Landing/landing.html';
}

function isEmpty(str) {
    return (!str || str.length === 0);
}

function checkForm(isAdmin) {
    var form = isAdmin ? document.getElementById('admin_loginForm') : document.getElementById('user_loginForm');
    var userName = isAdmin ? document.getElementById('admin_email') : document.getElementById('user_email');
    var password = isAdmin ? document.getElementById('admin_password') : document.getElementById('user_password');

    if (isEmpty(userName.value)) {
        alert("Please fill in a username");
    } else if (isEmpty(password.value)) {
        alert("Please fill a password");
    } else {
        form.submit();
    }
}