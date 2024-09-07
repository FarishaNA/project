function confirmLogout() {
    var result = confirm("Are you sure you want to log out?");
    if (result) {
        window.location.href = '../public/logout.php';
    }
}
