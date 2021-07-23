/**
 * Goes to landing
 */
function goToLanding() {
    window.location.href = '../../Landing/landing.html';
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
 * Check Form
 * @param {Boolean} isAdmin 
 * 
 * Validates the form before submit
 */
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