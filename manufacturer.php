<?php

session_start();

function sanitizeInput($input)
{
    $sanitizedInput = htmlspecialchars(trim($input));
    return $sanitizedInput;
}

function response($status, $message)
{
    $response = array(
        "status" => $status,
        "message" => $message,
    );
    die(json_encode($response));
}

$cookieAuth = $_COOKIE['auth'];

$sessionAuth = $_SESSION[$cookieAuth];

if (!($sessionAuth['role'] == 'manufacturrer') || !preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $sessionAuth['email'])) {
    unset($_SESSION[$cookieAuth]);
    setcookie('auth', '', time() - 86400);
    header('location: home/index.php');
    exit();
}

$email = $sessionAuth['email'];

require_once 'connectDB.php';

// #fff side menu

$result;
$firstName;
$lastName;
$profilePicture;

// $email = 'kirubelbewket@gmail.com';
// $email = "getnetmersha@gmail.com";

$sql = "SELECT `firstName`, `lastName`, `profile picture` FROM `employees` WHERE `email` = '$email'";

if ($result = $con->query($sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $profilePicture = $row['profile picture'];
    }
}

// ################################### #fff FIRMWARE RELEASE #################################
if (isset($_POST["submit"])) {
    $inoFile = $_FILES['inoFile'];
    $binFile = $_FILES['binFile'];

    if (!is_dir('firmwares')) {
        mkdir('firmwares');
    }

    if ($inoFile['tmp_name'] && $binFile['tmp_name']) {
        $sql = 'SELECT MAX(`id`) FROM `firmwares`;';

        $res = $con->query($sql);

        $name = $res->fetch_column();
        $name += 1;
        $name .= '.';
        // die($name);

        $binPath = 'firmwares/' . $name . strtolower(pathinfo($binFile['name'], PATHINFO_EXTENSION));
        $inoPath = 'firmwares/' . $name . strtolower(pathinfo($inoFile['name'], PATHINFO_EXTENSION));

        if (move_uploaded_file($binFile['tmp_name'], $binPath)) {
            if (move_uploaded_file($inoFile['tmp_name'], $inoPath)) {
                // die('completed!!');
                $sql = "INSERT INTO `firmwares`(`firmware`, `code`, `released_by`) VALUES ('$binPath','$inoPath','$email')";
                if ($con->query($sql)) {
                    die(header("location: sale.php?msg=upload success"));
                } else {
                    die(header("location: sale.php?msg=faild to upload"));
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        unset($_SESSION[$cookieAuth]);
        setcookie('auth', '', time() - 86400);

        response("success", "logedout");
    }

    $type = sanitizeInput($_POST['type']);

    if ($type === 'pageInfo') {
        $sql = "SELECT `deviceName`, `deviceStatus` FROM `device` WHERE `deviceStatus` = 'inactive'";
        $result = $con->query($sql);

        if (!$result) {
            response('fail', "cann't get the data form the server");
        }

        $return = '';
        while ($row = $result->fetch_assoc()) {
            $return .= '{"deviceName": "' . $row['deviceName'] . '", "deviceStatus": "' . $row['deviceStatus'] . '"}, ';
        }

        $sql = "SELECT `amount` FROM `pricing`;";
        $price = $con->query($sql)->fetch_column();

        //////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////////

        $sql = "SELECT `code` FROM `firmwares` ORDER BY `id` DESC";
        $result = $con->query($sql);

        if (!$result) {
            response('fail', "cann't get the data form the server");
        }

        $msg = '{"price" : ' . $price;

        $msg = $msg . ', "devices": ' . str_replace('}, ]', '}]', '[' . $return . ']');

        $return = '';
        while ($row = $result->fetch_assoc()) {
            // $return .= '{"deviceName": "' . $row['deviceName'] . '", "deviceStatus": "' . $row['deviceStatus'] . '"}, ';
            $return .= json_encode($row) . ',';
        }
        $firmwares = str_replace(',]', ']', '[' . $return . ']');
        $msg .= ', "firmwares": ' . $firmwares . '}';

        response('success', $msg);
    } elseif ($type === 'sellDevice') {
        $deviceName = sanitizeInput($_POST['deviceName']);
        $email = sanitizeInput($_POST['email']);
        $password = sanitizeInput($_POST['password']);

        $password = md5($password);

        $res;

        $sql = ("SELECT `deviceName` FROM `device` WHERE `deviceStatus` = 'inactive'");

        if (!($res = $con->query($sql))) {
            response('fail', "cann't sale the device");
        }

        while ($row = mysqli_fetch_assoc($res)) {

            if ($deviceName != $row['deviceName']) {
                response('fail', "unknown device * $deviceName *");
            }

            $sql = "SELECT `email` FROM `customers` WHERE `email` = '$email'";

            if ($con->query($sql)->fetch_assoc()) {
                response('fail', 'email already exist!!!');
            }

            $sql = "SELECT `amount` FROM `pricing`;";
            $price = $con->query($sql)->fetch_column();

            $sql = ("INSERT INTO `customers`(`id`, `device`, `email`, `password`, `addedBy`, `status`, `price`)
                 VALUES ('', '$deviceName', '$email', '$password', 'company', 'active', $price)");

            if (!$con->query($sql)) {
                response('fail', "something went wrong, try again!!!");
            }

            $date = date("y-m-d H:i:s");
            $sql = "INSERT INTO `transactions`(`id`, `amount`, `reason`, `type`, `description`, `date`) VALUES ('','$price','$deviceName','sell','sell','$date')";

            if (!$con->query($sql)) {
                response("warning", "contact the admin, transaction not saved!");
            }

            $sql = ("UPDATE `device` SET `deviceStatus`='active' WHERE `deviceName`='$deviceName'");

            if (!$con->query($sql)) {
                response("warning", "device sold but it is inactive");
            }

            response("success", "*$deviceName* sold!!!");
        }
    } elseif ($type == "addDevice") {

        $deviceName = sanitizeInput($_POST['deviceName']);

        $sql = ("INSERT INTO `device`(`id`, `deviceName`, `led1`, `led2`, `led3`, `led4`, `led5`, `led6`, `deviceStatus`)
                 VALUES ('','$deviceName',0, 0, 0, 0, 0, 0, 'inactive')");

        if ($con->query($sql)) {
            response("success", "device added");
        } else {
            response("fail", "cann't add device something wen't wrong");
        }
    } elseif ($type == "updatePrice") {
        $amount = sanitizeInput($_POST['price']);

        $sql = ("UPDATE `pricing` SET `amount`='$amount' WHERE `id` = 1");

        if ($con->query($sql)) {
            response("success", "price updated");
        } else {
            response("fail", "cann't update price something wen't wrong");
        }
    } elseif ($type == 'Change Password') {
        require "changePassword.php";

        $password = sanitizeInput($_POST['password']);
        $ConfirmPassword = sanitizeInput($_POST['confirmPassword']);

        changePassword($password, $ConfirmPassword, $email, 'employees');
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
    <title>Manufacturrer</title>

    <link rel="stylesheet" href="css/adminStyle.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism.css" />
    <script src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js"></script>
</head>

<body>
    <div class="notifications"></div>

    <div class="overlay hidden"></div>

    <div class="producer_modal hidden">
        <div class="modal-identifiyer2">
            <span class="las la-question-circle modal-icon"></span>
            <div class="modal-type">HELP UPLOAD</div>
        </div>

        <span class="x2 las la-times"></span>

        <div class="how_to_view">
            <div class="step">Step1): Before going to the project make sure that you've already installed the latest
                version of Arduino IDE on your desktop (Windows, Linux, or MAC OS X). You can download Arduino IDE on <a
                    href="https://www.arduino.cc/en/main/software" target="_blank"
                    rel="noopener noreferrer">https://www.arduino.cc/en/main/software.</a></strong></div>
            <img src="img/steps/1.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step2): Click <strong>Select software version</strong> and <b>Download</b></div>
            <img src="img/steps/2.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step4): Open your Arduino IDE, go to <strong>File>Preferences</strong></div>
            <img src="img/steps/4.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step5): Put <small><b>https://dl.espressif.com/dl/package_esp32_index.json</b></small>
                into the “Additional Board Manager URLs” field as shown in the figure below. Then, click the “OK” button
                <br>
                <br>
                <strong>Note:</strong> if you already have another boards (i.e ESP8266 boards URL), you can separate the
                URLs with a comma like this:
                <br>
                <br>
                <hr>
                <small><b>
                        https://dl.espressif.com/dl/package_esp32_index.json,
                        http://arduino.esp8266.com/stable/package_esp8266com_index.json</b></small>
                <hr>
            </div>
            <img src="img/steps/5.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step6): Click <b>BOARDS MANAGER</b> search <b>esp32</b> and install <b>esp32 by
                    Espressif</b></div>
            <img src="img/steps/6.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step7): Click LIBRARY MANNAGER search and install { <b>WiFiManager</b> (by tzapu),
                <b>AceButton</b> (by Brian T), <b>ArduinoJson</b> (by Benoit) }
            </div>
            <img src="img/steps/7.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step8): Connect <b>esp32</b> to the computer</div>
            <img src="img/steps/8.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step9): Click <b>Select Board</b> then Click <b>Select other board and ports</b></div>
            <img src="img/steps/9.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step10): Search board and select <b>BOARD</b> and <b>PORT</b></div>
            <img src="img/steps/10.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step11): Write/upload code and edit Click <b>Verify</b> makesure it doesn't have error
            </div>
            <img src="img/steps/11.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step12): After the 11th step Click <b>Upload</b> and wait until <b>Connection...</b>
                message apears</div>
            <img src="img/steps/12.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Step13): Press <b>BOOT</b> button until it starts to upload</div>
            <img src="img/steps/13.png" alt="">
        </div>

        <div class="how_to_view">
            <div class="step">Note: If the <b>BOOT</b> button is not pressed <b>Error message</b> will apear like
                <b>&downarrow;</b> :- start re-upload
            </div>
            <img src="img/steps/14.png" alt="">
        </div>
    </div>

    <div class="modal hidden">
        <div class="modal-identifiyer"></div>

        <span class="x las la-times"></span>

        <form action="" method="post" class="" enctype="multipart/form-data">
            <div class="modal-forms"></div>

            <div class="modal-action">
                <input type="reset" value="Clear" name="" id="modal-cancel" class="modal-btns" />
                <input type="submit" value="Save" name="" class="modal-btns submit-btn" />
            </div>
        </form>
    </div>



    <!-- ------------------------------------------------------------ -->

    <input type="checkbox" id="menu-toggle" />
    <div class="sidebar">
        <div class="side-header">
            <h3>S<span>martizer</span></h3>
        </div>

        <div class="side-content">
            <div class="profile">
                <!-- <div class="profile-img bg-img" style="background-image: url(img/PicsArt_12-02-06.07.08.jpg)"></div> -->
                <div class="profile-img bg-img" style="background-image: url('<?php echo $profilePicture; ?>')"></div>

                <h4 class="userName"><?php echo $firstName . ' ' . $lastName; ?></h4>
                <small>MANUFACTURER</small>
            </div>

            <div class="side-menu">
                <ul>
                    <li title="Devices" class="side-menu--li">
                        <a href="" class="activated">
                            <span class="las la-inbox"></span>
                            <small>Devices</small>
                        </a>
                    </li>
                    <li title="Employees" class="side-menu--li">
                        <a href="">
                            <span class="las la-file-code"></span>
                            <small>Firmwares</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div class="header-content">
                <label for="menu-toggle">
                    <span class="las la-bars"></span>
                </label>

                <button class="changePassword"><span>Change Password &upharpoonleft;&downharpoonright;</span></button>

                <div class="header-menu">
                    <?php if ($sessionAuth['second role']) { ?>

                        <div class="notify-icon switch_account">
                            <span class="notify">Switch Account</span>
                        </div>
                    <?php } ?>
                    <div class="notify-icon update-price">
                        <span>
                            <span class="las la-exchange-alt icn"></span>

                            <span class="icn">$<?php $sql = "SELECT `amount` FROM `pricing`;";
                                                $price = $con->query($sql)->fetch_column();
                                                echo $price; ?></span>
                        </span>
                    </div>

                    <div class="user logout">
                        <span class="las la-power-off"><span>Logout</span></span>
                    </div>
                </div>
            </div>
        </header>

        <!-- ==================#fff DEVICE ====================================================== -->
        <main class="main Device-main ">
            <div class="page-header">
                <h1>Devices</h1>
                <small>Home / Devices</small>
            </div>

            <div class="page-content">
                <div class="records table-responsive">
                    <div class="record-header">
                        <div class="add">
                            <button class="AddDeviceButton">Add Device</button>
                        </div>
                    </div>


                    <div>
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>DEVICES NAME</th>
                                    <th>PRICE</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="deviceTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- ==================#fff Softwares =================================================== -->
        <main class="main Software-main hidden">
            <div class="page-header">
                <h1>Firmwares</h1>
                <small>Home / Firmwares</small>
                <br />
                <br />
                <hr />
                <h1>Release Firmware</h1>

                <form action="manufacturer.php" class="release_form" method="post" enctype="multipart/form-data">
                    <div class="modal-forms">
                        <span>.bin file: </span><input type="file" accept=".bin" name="binFile" id="bin" />
                    </div>

                    <div class="modal-forms">
                        <span>.ino file: </span><input type="file" accept=".ino, .cpp" name="inoFile" id="ino" />
                    </div>

                    <div class="modal-action">

                        <input type="reset" value="Clear" name="" class="modal-btns" />
                        <input type="submit" value="Release" name="submit" class="modal-btns submit-btn" />
                    </div>
                </form>
            </div>

            <div class="page-content">
                <div class="records table-responsive">
                    <div class="record-header">

                        <div class="browse dwld_sw">
                            <a class="download_link" href="" download>
                                <span class="las la-download"></span><br />
                                <span>Download</span>
                            </a>
                            <input type="text" value="Smartizer SZ-1.0.1" id="version_view" disabled />
                            <select name="" id="version_select">
                            </select>
                            <div class="how_to_instruction">
                                <span class="las la-question-circle">?</span>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="code_view">
                        <!-- <pre line-numbers language-c><code class="++ language-c"> -->
                        <pre line-numbers language-c><code class="language-javascript line-numbers codeRenderPage">

              </code></pre>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/manufacturrer.js"></script>

    <?php
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] === 'upload success') {
            echo "<script>createToast('success', 'las la-check', 'Success', '👍 upload success')</script>";
        }

        if ($_GET['msg'] === 'faild to upload') {
            echo "<script>createToast('error', 'las la-exclamation', 'Error', '💥 faild to upload')</script>";
        }
    }
    ?>
</body>

</html>