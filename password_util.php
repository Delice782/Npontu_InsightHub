<?php
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($enteredPassword, $hashedPassword) {
    return password_verify($enteredPassword, $hashedPassword);
}
?>

