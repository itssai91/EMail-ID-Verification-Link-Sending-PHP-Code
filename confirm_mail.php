<?php

if ($_GET['key'] && $_GET['token']) {
    email_verification_check($_GET['key'], $_GET['token'], date('Y-m-d H:i:s'));
} else {
    echo "<script> alert('Sorry invalid link')</script>";
    echo '<h1 style="color:red";>Sorry Invalid Link</h1>';
}

function email_verification_check($key, $token, $date)
{
    require 'db_connection.php';
    $sql = "SELECT * FROM `users` WHERE `email` = '$key' AND `verify_link` = '$token'";
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        if ($row['email_verified_at'] == null) {
            $conn->query("UPDATE `users` SET `email_verified_at` = '$date' WHERE `email` = '$key' AND `verify_link` = '$token'");
            echo "<script> alert('Email Verified Success')</script>";
            echo '<h1 style="color:green";>Email Verified Success</h1>';
        } else {
            echo "<script> alert('This email is already registered with us')</script>";
            echo '<h1 style="color:red";>Email already verified please click the below button to login</h1>';
        }
    } else {
        echo "<script> alert('This email is not registered')</script>";
    }
    $conn->close();
}
