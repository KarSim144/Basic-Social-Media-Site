<?php
session_start();
//oturum acıldıgında olacaklar:
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if(!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}
//user id gelmesi burda oluyor kısaca
function getUsername() {
    return $_SESSION['username'] ?? null;
    //buda kullanıcı adını getirmek için kısa bi class ayrı olması iyi
}
?>