<?php

require_once './connectDB.php';
function changePassword($password, $confirmPassword, $email, $where)
{
    global $con;

    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($password) || ($password != $confirmPassword)) {
        die(json_encode(["status" => "fail", "message" => "password not match!"]));
    }

    $newPassword = md5($password);

    $sql = "UPDATE `$where` SET `password`='$newPassword' WHERE `email` ='$email'";

    if ($con->query($sql)) {
        die(json_encode(["status" => "success", "message" => 'password changed successfully!']));
    } else {
        die(json_encode(["status" => "fail", "message" => 'password not changed please try again later!']));
    }

}