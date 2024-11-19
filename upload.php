<?php

require_once './connectDB.php';

$deviceName = $_GET['deviceName'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT *FROM device WHERE deviceName = '$deviceName'";

    if ($res = $con->query($sql)) {

        while ($row = mysqli_fetch_assoc($res)) {
            echo json_encode(array(
                "status" => $row["deviceStatus"],
                "led1" => $row["led1"],
                "led2" => $row["led2"],
                "led3" => $row["led3"],
                "led4" => $row["led4"],
                "led5" => $row["led5"],
                "led6" => $row["led6"],
            ));
        }
    } else {
        echo json_encode(array(
            "status" => "False",
            "message" => "cann't fetch data from server",
        ));
    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // $deviceName = $sessionAuth['deviceName'];
    $sql = "SELECT `deviceStatus` FROM `device` WHERE  deviceName = '$deviceName'";
    // $sql = "SELECT `deviceStatus` FROM `device` WHERE  deviceName = 'deviceThree'";

    $res = $con->query($sql)->fetch_column();

    if ($res == "active") {

        $led1 = $_POST["led1"];
        $led2 = $_POST["led2"];
        $led3 = $_POST["led3"];
        $led4 = $_POST["led4"];
        $led5 = $_POST["led5"];
        $led6 = $_POST["led6"];

        $sql = "UPDATE device SET led1 = " . $led1 . " , led2 = " . $led2 . ", led3 = " . $led3 .
            ", led4 = " . $led4 . " , led5 = " . $led5 . ", led6 = " . $led6 . " WHERE deviceName = 'deviceThree'";

        if (!$con->query($sql)) {
            die(json_encode(array(
                "status" => "False",
                "message" => "cann't fetch data from server",
            )));
        }

    } else {
        echo json_encode(array(
            "status" => "False",
            "message" => "please activate your device!",
        ));
    }
}
