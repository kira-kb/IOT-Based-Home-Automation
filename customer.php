<?php

session_start();

require_once "./connectDB.php";

$cookieAuth = $_COOKIE['auth'];

$sessionAuth = $_SESSION[$cookieAuth];

$deviceName = $sessionAuth['deviceName'];
$email = $sessionAuth["email"];

if ($sessionAuth['second role'] != 'customer' || empty($sessionAuth['deviceName'])) {
    unset($_SESSION[$cookieAuth]);
    setcookie('auth', '', time() - 86400);
    header('location: login.php');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['switchAccount'])) {
        if ($sessionAuth['role'] == 'admin') {
            die(json_encode(array("status" => "success", "location" => "admin.php")));
        } else if ($sessionAuth['role'] == 'manufacturrer') {
            die(json_encode(array("status" => "success", "location" => "manufacturer.php")));
        } else if ($sessionAuth['role'] == 'seller') {
            die(json_encode(array("status" => "success", "location" => "sale.php")));
        }
    }

    if (isset($_POST['logout'])) {
        unset($_SESSION[$cookieAuth]);
        setcookie('auth', '', time() - 86400);
        die(json_encode(["status" => "success", "location" => "home/index.php"]));
    }

    if (isset($_POST['identify'])) {
        if ($_POST['identify'] == 'changePassword') {

            require_once 'changePassword.php';

            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            changePassword($password, $confirmPassword, $email, 'customers');
        } elseif ($_POST['identify'] == 'send feedback') {
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            $date = date("y-m-d H:i:s");

            if (empty($subject) || empty($message)) return;

            $sql = "INSERT INTO `feedbacks`(`email`, `subject`, `message`, `favorite`, `testimonial`, `status`, `date`) 
            VALUES ('$email','$subject','$message','false','false','unreaded','$date')";
            // echo $sql;
            // return;
            if ($con->query($sql)) {
                die(json_encode(["status" => "success", "message" => 'Feedback sent successfully']));
            } else {
                die(json_encode(["status" => "fail", "message" => 'Faild to send Feedback!']));
            }
        }

        exit();
    }

    // ////////////////////////////////////#fff#fff#fff//////////////////////////////////////////

    if (isset($_POST['getData'])) {

        $sql = "SELECT *FROM device WHERE deviceName = '$deviceName'";

        if ($res = $con->query($sql)) {

            while ($row = mysqli_fetch_assoc($res)) {
                die(json_encode(array(
                    "status" => $row["deviceStatus"],
                    "led1" => $row["led1"],
                    "led2" => $row["led2"],
                    "led3" => $row["led3"],
                    "led4" => $row["led4"],
                    "led5" => $row["led5"],
                    "led6" => $row["led6"],
                )));
            }
        } else {
            die(json_encode(array(
                "status" => "False",
                "message" => "cann't fetch data from server",
            )));
        }
    }

    if (isset($_POST['updateData'])) {
        $sql = "SELECT `deviceStatus` FROM `device` WHERE  deviceName = '$deviceName'";
        // $sql = "SELECT `deviceStatus` FROM `device` WHERE  deviceName = 'deviceThree'";

        $res = $con->query($sql)->fetch_column();

        if ($res != "active") {
            echo json_encode(array(
                "status" => "False",
                "message" => "please activate your device!",
            ));
        }

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
        exit();
    }
}

if (!($_SERVER['REQUEST_METHOD'] == 'GET')) {
    die();
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <link rel="stylesheet" href="css/style.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter&display=swap" />

    <title>Smartizer</title>
</head>

<body>
    <noscript>please enable your javascript</noscript>
    <div class="notifications"></div>

    <header>
        <div class="header_container">
            <ul class="header_list">
                <!-- <li>SmartizerOne</li> -->
                <li><?php echo $deviceName; ?></li>
                <!-- <li>kirubelbewket@gmail.com</li> -->
                <li class="updt"><?php echo $email; ?><span>&upharpoonleft;&downharpoonright;</span></li>
                <li class="switchAccount">Switch Account</li>
                <li class="sndfdb">send feedback</li>
                <li>
                    <label for="day_night">day</label>
                    <input type="checkbox" name="" id="day_night" />
                </li>
                <li class="logout">logout</li>
            </ul>
        </div>
    </header>
    <div class="container">
        <div class="ceil">
            <h3 class="type">swith 1</h3>
            <button class="btn1 btn">
                <ion-icon name="bulb-outline"></ion-icon>
            </button>
            <div class="des">salon light</div>
        </div>

        <div class="ceil">
            <h3 class="type">swith 2</h3>
            <button class="btn2 btn">
                <ion-icon name="bulb-outline"></ion-icon>
            </button>
            <div class="des">master bedroom light</div>
        </div>

        <div class="ceil">
            <h3 class="type">swith 3</h3>
            <button class="btn3 btn">
                <ion-icon name="bulb-outline"></ion-icon>
            </button>
            <div class="des">children bedroom lightt</div>
        </div>

        <div class="ceil">
            <h3 class="type">swith 4</h3>
            <button class="btn4 btn">
                <ion-icon name="bulb-outline"></ion-icon>
            </button>
            <div class="des">children bedroom lightt</div>
        </div>

        <div class="ceil">
            <h3 class="type">swith 5</h3>
            <button class="btn5 btn">
                <ion-icon name="bulb-outline"></ion-icon>
            </button>
            <div class="des">garage light</div>
        </div>

        <div class="ceil">
            <h3 class="type">swith 6</h3>
            <button class="btn6 btn">
                <ion-icon name="bulb-outline"></ion-icon>
            </button>
            <div class="des">garage light</div>
        </div>
    </div>

    <!-- jaghkslddddjhagkajfjkz -->
    <!-- <div class="modal"> -->
    <div class="modal hidden">
        <button class="btn--close-modal">&times;</button>
        <h2 class="modal__header">Change Password</h2>

        <!-- <div class="info">
        <span class="x">&times;</span>
        <span class="loginMSG">incorrect email or password</span>
      </div> -->
        <form class="modal__form" method="post" action="customer.php">
            <input type="hidden" name="identify" value="changePassword" />
            <label>🗝️</label>
            <input type="Password" name="password" class="p" placeholder="***************" />
            <label>Confirm 🗝️</label>
            <input type="password" name="confirmPassword" placeholder="***************" class="cp" />
            <button>Change password 🗝️</button>
        </form>
    </div><!-- <div class="overlay hidden"></div> -->
    <!-- <div class="overlay"></div> -->

    <div class="feedModal hidden">
        <button class="btn--close-modal2">&times;</button>
        <h2 class="modal__header">Send Feedback</h2>

        <form class="modal__form2 modal__form" method="post" action="customer.php">
            <input type="hidden" name="identify" value="send feedback" />
            <label>Subject: </label>
            <input
                type="text"
                name="subject"
                class="p"
                placeholder="Subject" />
            <label>Message: </label>
            <textarea name="message" id="" placeholder="Message..." rows="8"></textarea>
            <button>Send Feedback &rightarrow;</button>
        </form>
    </div>
    <div class="overlay hidden"></div>
    <!-- <script src="js/script.js"></script> -->
    <script src="js/customer.js"></script>
</body>

</html>