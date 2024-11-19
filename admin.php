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

if (!($sessionAuth['role'] == 'admin') || !preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $sessionAuth['email'])) {
    unset($_SESSION[$cookieAuth]);
    setcookie('auth', '', time() - 86400);
    header('location: login.php');
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

// #fff employee list

$employeeObject = '[';

$sellers = 0;
$manufacturrers = 0;
$admins = 0;
$allEmployees = 0;

$i = 1;

$sql = "SELECT * FROM `employees`";

if ($result = $con->query($sql)) {
    while ($row = mysqli_fetch_assoc($result)) {

        if ($row['role'] == 'seller') {
            $sellers++;
        }

        if ($row['role'] == 'admin') {
            $admins++;
        }

        if ($row['role'] == 'manufacturrer') {
            $manufacturrers++;
        }

        $employeeObject .= '{"id": "' . $row['id'] . '", "firstName": "' . $row['firstName'] . '", "lastName": "' . $row['lastName'] . '", "email": "' . $row['email'] . '", "sallary":' . $row['sallary'] . ' , "profilePic":"' . $row['profile picture'] . '", "role": "' . $row['role'] . '", "gender": "' . $row['gender'] . '"},';

        $allEmployees = $i;

        $i++;
    }
}

// #fff  customer and device list

$i = 1;

$deviceOptionObject = '[';
$deviceObject = '[';
$customerObject = '[';

$allDevices = 0;
$activeDevices = 0;
$inactiveDevices = 0;
$suspendedDevices = 0;
$allCustomers = 0;

$sql = "SELECT `amount` FROM `pricing`;";
$price = $con->query($sql)->fetch_column();

$sql = "SELECT `id`, `device`, `email`, `password`, `addedBy`, `status`, `price` FROM `customers`";

if ($result = $con->query($sql)) {

    while ($row = mysqli_fetch_assoc($result)) {

        if ($row['status'] == 'active') {
            $status = 'pause';
            $title = 'Suspend';
            $activeDevices++;
        } else {
            $status = 'play';
            $title = 'Activate';
        }

        if ($row['status'] == 'inactive') {
            $inactiveDevices++;
        }

        if ($row['status'] == 'suspend') {
            $suspendedDevices++;
        }

        $deviceObject .= '{"device": "' . $row['device'] . '", "email": "' . $row['email'] . '", "status": "' . $row['status'] . '", "price": "' . $row['price'] . '", "action": "' . $status . '", "title": "' . $title . '"},';

        $customerObject .= '{"email":"' . $row['email'] . '", "device": "' . $row['device'] . '", "status": "' . $row['status'] . '", "id": "' . $row['id'] . '"},';

        $allCustomers = $i;
        $allDevices = $i;

        $i++;
    }

    $sql = "SELECT `deviceName` FROM `device` WHERE `deviceStatus` = 'inactive'";
    if ($result = $con->query($sql)) {

        while ($row = mysqli_fetch_assoc($result)) {

            // $deviceObject .= '{"device":"' . $row['deviceName'] . '", "status": "inactive", "price": ' . $price . ', "action": "shopping-bag", "title": "Sell"},';
            $deviceObject .= '{"device":"' . $row['deviceName'] . '", "status": "inactive", "price": ' . $price . ', "action": "trash", "title": "Sell"},';
            $deviceOptionObject .= '"' . $row['deviceName'] . '", ';

            $inactiveDevices++;
            $allDevices++;

            $i++;
        }
    }
}

// #fff transaction

$transactionObject = '[';
$earn = 0;
$loss = 0;

$sql = "SELECT * FROM `transactions`";

if ($result = $con->query($sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['type'] == 'sell') {
            $earn += $row['amount'];
        } else if ($row['type'] == 'buy') {
            $loss += $row['amount'];
        }

        $transactionObject .= '{"amount": "' . $row['amount'] . '", "reason": "' . $row['reason'] . '", "type": "' . $row['type'] . '", "description": "' . $row['description'] . '"},';
    }
}

$deviceObject .= ']';
$deviceOptionObject .= ']';

$customerObject .= ']';
$employeeObject .= ']';

$transactionObject .= '{"loss": ' . $loss . ', "earn": ' . $earn . ', "ballance":' . $earn - $loss . ', "currentPrice": ' . $price . '}';
$transactionObject .= ']';

// #fff feedbacks
$feedbacks = '[';
$sql = "SELECT *FROM feedbacks ORDER BY `date`, `status` DESC";
if ($result = $con->query($sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $feedbacks .= json_encode($row) . ',';
    }
}
$feedbacks .= ']';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        unset($_SESSION[$cookieAuth]);
        setcookie('auth', '', time() - 86400);
        response("fail", "logedout");
    }

    if (!isset($_POST['identify']) || empty($_POST['identify']) || !preg_match("/[a-zA-Z]+/", sanitizeInput($_POST['identify']))) {
        response("fail", "cann't identify what kind of action you're doing!");
    }

    $identify = sanitizeInput($_POST['identify']);

    if ($identify == "addDevice") {
        $deviceName = sanitizeInput($_POST['deviceName']);

        $sql = ("INSERT INTO `device`(`id`, `deviceName`, `led1`, `led2`, `led3`, `led4`, `led5`, `led6`, `deviceStatus`)
                 VALUES ('','$deviceName',0, 0, 0, 0, 0, 0, 'inactive')");

        if ($con->query($sql)) {
            response("success", "device added");
        } else {
            response("fail", "cann't add device something wen't wrong");
        }
    } elseif ($identify == "addEmployee") {

        $firstName = sanitizeInput($_POST['firstName']);
        $lastName = sanitizeInput($_POST['lastName']);
        $gender = sanitizeInput($_POST['gender']);
        $email = sanitizeInput($_POST['email']);
        $password = sanitizeInput($_POST['password']);
        $role = sanitizeInput($_POST['role']);
        $sallary = sanitizeInput($_POST['sallary']);

        $password = md5($password);

        $profilePic;

        if ($gender == 'M') {
            $profilePic = './img/default/male avatar.jpg';
        }

        if ($gender == 'F') {
            $profilePic = './img/default/female avatar.jpeg';
        }

        if ($role != "admin" && $role != "manufacturrer") {
            response("fail", "choose *-ADMIN-* or *-MANUFACTURRER-* !!!");
        }

        $sql = ("INSERT INTO `employees`(`id`, `firstName`, `lastName`, `email`, `gender`, `password`, `sallary`, `profile picture`, `role`)
                VALUES ('', '$firstName', '$lastName', '$email', '$gender', '$password', '$sallary', '$profilePic', '$role')");

        if ($con->query($sql)) {
            response("success", "employee added");
        } else {
            response("fail", "cann't add employee something wen't wrong");
        }
    } elseif ($identify == "addCustomer") {
        $deviceName = trim($_POST['deviceName']);
        $email = sanitizeInput($_POST['email']);
        $password = sanitizeInput($_POST['password']);

        $password = md5($password);

        $sql = ("SELECT `deviceName` FROM `device` WHERE `deviceStatus` = 'inactive'");

        if ($res = $con->query($sql)) {
            while ($row = mysqli_fetch_assoc($res)) {

                if ($deviceName != $row['deviceName']) {
                    response('fail', "there is no device called * $deviceName *");
                }

                $sql = "SELECT `amount` FROM `pricing`;";
                $price = $con->query($sql)->fetch_column();

                $sql = ("INSERT INTO `customers`(`id`, `device`, `email`, `password`, `addedBy`, `status`, `price`)
                 VALUES ('', '$deviceName', '$email', '$password', 'company', 'active', $price)");

                if (!$con->query($sql)) {
                    response('fail', "cann't sell the device");
                }

                $date = date("y-m-d H:i:s");
                $sql = "INSERT INTO `transactions`(`id`, `amount`, `reason`, `type`, `description`, `date`) VALUES ('','$price','$deviceName','sell','sell','$date')";

                $con->query($sql);

                $sql = ("UPDATE `device` SET `deviceStatus`='active' WHERE `deviceName`='$deviceName'");

                if (!$con->query($sql)) {
                    response("warning", "device sold but it is inactive");
                }

                response("success", "new transaction added");
            }
        } else {
            response("fail", "cann't sale the device");
        }
    } elseif ($identify == "addTransaction") {
        $amount = sanitizeInput($_POST['price']);
        $reason = sanitizeInput($_POST['reason']);
        $type = sanitizeInput($_POST['type']);
        $description = sanitizeInput($_POST['description']);
        $date = date("y-m-d H:i:s");

        $sql = ("INSERT INTO `transactions`(`id`, `amount`, `reason`, `type`, `description`, `date`)
                 VALUES ('', '$amount', '$reason', '$type', '$description', '$date')");

        if (!$con->query($sql)) {
            response("fail", "cann't add transaction something wen't wrong");
        }

        response("success", "new transaction added");
    } elseif ($identify == "updatePrice") {
        $amount = sanitizeInput($_POST['price']);
        // $type = sanitizeInput($_POST['type']);

        $sql = ("UPDATE `pricing` SET `amount`='$amount' WHERE `id` = 1");

        if ($con->query($sql)) {
            response("success", "price updated");
        } else {
            response("fail", "cann't update price something wen't wrong");
        }
    } elseif ($identify == "pageInfo") {
        $dashboardObject = '{"employeeAnlytics": {"sellers": ' . $sellers . ', "manufacturrers": ' . $manufacturrers . ', "admins": ' . $admins . ', "allEmployees": ' . $allEmployees . '},';
        $dashboardObject .= '"devicdAnalytics": {"activeDevices": ' . $activeDevices . ', "inactiveDevices": ' . $inactiveDevices . ', "suspendedDevices": ' . $suspendedDevices . ', "allDevices":' . $allDevices . '}}';

        die("{\"deviceList\":" . str_replace(", ]", "]", $deviceOptionObject) . ", \"devices\":" . str_replace(",]", "]", $deviceObject) . ", \"customers\":" . str_replace(",]", "]", $customerObject) . ", \"employees\":" . str_replace(",]", "]", $employeeObject) . ", \"transactions\":" . str_replace(",]", "]", $transactionObject) . ", \"feedback\":" . str_replace(",]", "]", $feedbacks) . ", \"dashboard\": " . $dashboardObject . '}');
    } elseif ($identify == "SAD") {

        $item = sanitizeInput($_POST['item']);
        if (empty($item)) {
            die("item is empty");
        }

        if (isset($_POST['type']) && isset($_POST['type']) && sanitizeInput($_POST['type'] == 'emp')) {
            $sql = "DELETE FROM `employees` WHERE `id` = '$item'";

            if ($con->query($sql)) {
                die();
            } else {
                response("fail", "cann't delete employee!");
            }
        }

        $sql = "SELECT `status` FROM`customers` WHERE `device` = '$item'";

        if ($result = $con->query($sql)) {
            if ($data = $result->fetch_column()) {
                if ($data == 'active') {
                    $sql = "UPDATE `device` SET `deviceStatus`='suspend' WHERE `deviceName` = '$item'";
                    if ($con->query($sql)) {
                        $sql = "UPDATE `customers` SET `status`='suspend' WHERE `device` = '$item'";

                        if ($con->query($sql)) {
                            response("success", $item . " suspended!");
                        } else {
                            $sql = "UPDATE `device` SET `deviceStatus`='active' WHERE `deviceName` = '$item'";
                            $con->query($sql);
                        }
                    }
                } else if ($data == 'suspend') {
                    $sql = "UPDATE `device` SET `deviceStatus`='active' WHERE `deviceName` = '$item'";
                    if ($con->query($sql)) {
                        if ($con->query($sql)) {
                            $sql = "UPDATE `customers` SET `status`='active' WHERE `device` = '$item'";

                            if ($con->query($sql)) {
                                response("success", $item . " activated!");
                            } else {
                                $sql = "UPDATE `device` SET `deviceStatus`='suspend' WHERE `deviceName` = '$item'";
                                $con->query($sql);
                            }
                        }
                    }
                } else {
                    response("fail", "cann't update '$item'!");
                }
            } else {

                $sql = "SELECT `deviceStatus` FROM `device` WHERE `deviceStatus` = 'inactive' AND `deviceName` = '$item'";

                if ($result = $con->query($sql)) {
                    if ($data = $result->fetch_column()) {

                        $sql = "DELETE FROM `device` WHERE `deviceStatus` = 'inactive' AND `deviceName` = '$item'";

                        if ($result = $con->query($sql)) {
                            response("success", $item . " is deleted!");
                        } else {
                            response("fail", $item . " is not a valid name!");
                        }
                    }
                }
            }
        }
    } elseif ($identify == "editEmployee") {

        if (
            !preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $_POST['email']) || empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['role'])
            || empty($_POST['sallary']) || empty($_POST['gender'])
        ) {
            response("fail", "please fill all the blank!");
        }
        $email = sanitizeInput($_POST['email']);

        $firstName = sanitizeInput($_POST['firstName']);
        $lastName = sanitizeInput($_POST['lastName']);
        $gender = sanitizeInput($_POST['gender']);
        $id = sanitizeInput($_POST['id']);
        $role = sanitizeInput($_POST['role']);
        $sallary = sanitizeInput($_POST['sallary']);

        $sql = "UPDATE `employees` SET `firstName`='$firstName',`lastName`='$lastName',`email`='$email',`gender`='$gender',`sallary`='$sallary',`role`='$role' WHERE `id` = '$id'";

        if ($con->query($sql)) {
            response("success", $firstName . ' ' . $lastName . " updated!");
        } else {
            response("fail", $firstName . ' ' . $lastName . " is not updated!");
        }
    } elseif ($identify == "editCustomer") {
        // die(json_encode($_POST));
        $email = sanitizeInput($_POST['email']);
        $id = sanitizeInput($_POST['id']);
        $sql = "UPDATE `customers` SET `email`='$email' WHERE `id` = '$id'";

        if ($con->query($sql)) {
            response("success", "customer updated!");
        } else {
            response("fail", "customer is not updated!");
        }
    } elseif ($identify == 'unread') {
        $id = sanitizeInput($_POST['item']);
        $sql = "UPDATE `feedbacks` SET `status`='readed' WHERE `id` = $id";

        if ($con->query($sql)) {
            response('success', "{\"feedback\":" . str_replace(",]", "]", $feedbacks) . "}");
        }
    } elseif ($identify == 'Remove Feedback') {
        $id = sanitizeInput($_POST['item']);
        $sql = "DELETE FROM `feedbacks` WHERE `id` = $id";
        if ($con->query($sql)) {
            response('success', "{\"feedback\":" . str_replace(",]", "]", $feedbacks) . "}");
        }
    } elseif ($identify == 'Share to Testimonial') {
        $id = sanitizeInput($_POST['item']);
        // $sql = "UPDATE `feedbacks` SET `status`='readed' WHERE `id` = $id";
        $sql = "SELECT `testimonial` FROM `feedbacks` WHERE `id` = $id";

        $res = $con->query($sql)->fetch_column();
        $changeTo = '';

        $res == 'false' ? $changeTo = 'true' : $changeTo = 'false';

        $sql = "UPDATE `feedbacks` SET `testimonial`='$changeTo' WHERE `id` = $id";

        if ($con->query($sql)) {
            response('success', "{\"feedback\":" . str_replace(",]", "]", $feedbacks) . "}");
        }
    } elseif ($identify == 'Add to Favorites') {
        $id = sanitizeInput($_POST['item']);

        $sql = "SELECT `favorite` FROM `feedbacks` WHERE `id` = $id";

        $res = $con->query($sql)->fetch_column();
        $changeTo = '';

        $res == 'false' ? $changeTo = 'true' : $changeTo = 'false';

        // response('success', "changetp: $changeTo");

        $sql = "UPDATE `feedbacks` SET `favorite`='$changeTo' WHERE `id` = $id";

        // $sql = "SELECT `favorite`, `testimonial` FROM `feedbacks` WHERE `id`";
        // $sql = "UPDATE `feedbacks` SET `favorite`='true' WHERE `id` = $id";
        if ($con->query($sql)) {
            response('success', "{\"feedback\":" . str_replace(",]", "]", $feedbacks) . "}");
        }
    } elseif ($identify == 'Change Password') {
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
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="css/adminStyle.css" />
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" />
</head>

<body>
    <div class="notifications"></div>

    <div class="overlay hidden"></div>
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
                <div class="profile-img bg-img" style="background-image: url('<?php echo $profilePicture; ?>')"></div>


                <h4 class="userName"><?php echo $firstName . ' ' . $lastName; ?></h4>
                <small>ADMIN</small>
            </div>

            <div class="side-menu">
                <ul>
                    <li title="Dashboard" class="side-menu--li">
                        <a href="" class="activated">
                            <span class="las la-home"></span>
                            <small>Dashboard</small>
                        </a>
                    </li>
                    <li title="Devices" class="side-menu--li">
                        <a href="">
                            <span class="las la-inbox"></span>
                            <small>Devices</small>
                        </a>
                    </li>
                    <li title="Employees" class="side-menu--li">
                        <a href="">
                            <span class="las la-user-alt"></span>
                            <small>Employees</small>
                        </a>
                    </li>
                    <li title="Customers" class="side-menu--li">
                        <a href="">
                            <span class="las la-users"></span>
                            <small>Customers</small>
                        </a>
                    </li>
                    <li title="Feedbaks" class="side-menu--li">
                        <a href="">
                            <span class="las la-comment"></span>
                            <small>Feedbacks</small>
                        </a>
                    </li>
                    <li title="Transactions">
                        <a href="">
                            <span class="las la-exchange-alt"></span>
                            <small>Transactions</small>
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
                <!-- <div class="changePassword"><span>Change Password &upharpoonleft;&downharpoonright;</span></div> -->
                <div class="header-menu">

                    <?php if ($sessionAuth['second role']) { ?>

                        <div class="notify-icon">
                            <!-- <span class="las la-bell"></span> -->
                            <span class="notify">Switch Account</span>
                        </div>
                    <?php } ?>

                    <div class="user logout">
                        <span class="las la-power-off"><span>Logout</span></span>
                    </div>
                </div>
            </div>
        </header>

        <!-- ==================#fff ADMIN DASHBOARD ============================================= -->
        <main class="main Dashboard-main">
            <div class="page-header">
                <h1>Dashboard</h1>
                <small>Home / Dashboard</small>
            </div>

            <div class="page-content">
                <!-- ---------------------------------------------------------------- -->
                <div class="analytics">
                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__cp"></h2>
                            <span class="las la-dolar"></span>
                        </div>
                        <div class="card-progress">
                            <small>Current Price</small>
                            <div class="card-indicator">
                                <div class="indicator one"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__ballance"></h2>
                            <span class="las la-sell"></span>
                        </div>
                        <div class="card-progress">
                            <small>Ballance</small>
                            <div class="card-indicator">
                                <div class="indicator one"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__comments"></h2>
                            <span class="las la-income"></span>
                        </div>
                        <div class="card-progress">
                            <small>Comments</small>
                            <div class="card-indicator">
                                <div class="indicator two"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__customers"></h2>
                            <span class="las la-feedback"></span>
                        </div>
                        <div class="card-progress">
                            <small>Customers</small>
                            <div class="card-indicator">
                                <div class="indicator one"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__devices"></h2>
                            <span class="las la-dolar"></span>
                        </div>
                        <div class="card-progress">
                            <small>Devices</small>
                            <div class="card-indicator">
                                <div class="indicator two"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__ad"></h2>
                            <span class="las la-sell"></span>
                        </div>
                        <div class="card-progress">
                            <small>Active Devices</small>
                            <div class="card-indicator">
                                <div class="indicator one"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__iad"></h2>
                            <span class="las la-income"></span>
                        </div>
                        <div class="card-progress">
                            <small>Inactive Devices</small>
                            <div class="card-indicator">
                                <div class="indicator four"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__sd"></h2>
                            <span class="las la-feedback"></span>
                        </div>
                        <div class="card-progress">
                            <small>Suspended Devices</small>
                            <div class="card-indicator">
                                <div class="indicator three"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__employees"></h2>
                            <span class="las la-dolar"></span>
                        </div>
                        <div class="card-progress">
                            <small>Employees</small>
                            <div class="card-indicator">
                                <div class="indicator two"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__manufacturrers"></h2>
                            <span class="las la-sell"></span>
                        </div>
                        <div class="card-progress">
                            <small>Device Manufacturrers</small>
                            <div class="card-indicator">
                                <div class="indicator two"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__sellers"></h2>
                            <span class="las la-income"></span>
                        </div>
                        <div class="card-progress">
                            <small>Device Sellers</small>
                            <div class="card-indicator">
                                <div class="indicator two"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="dashboard__version"></h2>
                            <span class="las la-feedback"></span>
                        </div>
                        <div class="card-progress">
                            <small>Software Version</small>
                            <div class="card-indicator">
                                <div class="indicator one"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ----------------------------------------------------------------------------- -->
            </div>
        </main>
        <!-- ==================#fff DEVICE ====================================================== -->
        <main class="main Device-main hidden">
            <div class="page-header">
                <h1>Devices</h1>
                <small>Home / Devices</small>
            </div>

            <div class="page-content">
                <div class="records table-responsive">
                    <div class="record-header">
                        <!-- <div class="add">
                            <button class="AddDeviceButton">Add Device</button>
                        </div> -->

                        <div class="browse">
                            <input type="search" id="deviceSearch" placeholder="Search" class="record-search" />
                            <select name="" id="deviceFilter">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspend">Suspend</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>DEVICES NAME</th>
                                    <th>PRICE</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="deviceTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <!-- =================#FFF EMPLOYEES ==================================================== -->
        <main class="main Employee-main hidden">
            <div class="page-header">
                <h1>Employees</h1>
                <small>Home / Employees</small>
            </div>

            <div class="page-content">
                <div class="records table-responsive">
                    <div class="record-header">
                        <div class="add">
                            <button>Add Employee</button>
                        </div>

                        <div class="browse">
                            <input type="search" id="employeeSearch" placeholder="Search" class="record-search" />
                            <select name="" id="employeeFilter">
                                <option value="">All</option>
                                <option value="manufacturrer">Manufacturrers</option>
                                <option value="admin">Admins</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EMPLOYEE NAME</th>
                                    <th>SALLARY</th>
                                    <th>ROLE</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="employeeTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <!-- ================#FFF CUSTOMER ====================================================== -->
        <main class="main Customer-main hidden">
            <div class="page-header">
                <h1>Customers</h1>
                <small>Home / Customers</small>
            </div>

            <div class="page-content">
                <div class="records table-responsive">
                    <div class="record-header">
                        <!-- <div class="add">
                            <button>Sell Device</button>
                        </div> -->

                        <div class="browse">
                            <input type="search" id="customerSearch" placeholder="Search" class="record-search" />
                            <select name="" id="customerFilter">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="suspend">Suspend</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>CUSTOMER EMAILS</th>
                                    <th>DEVICE STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="customerTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <!-- ================#FFF FEEDBACK ====================================================== -->
        <main class="main Feedback-main hidden">
            <div class="page-header">
                <h1>Feedbacks</h1>
                <small>Home / Feedbacks</small>
            </div>

            <div class="page-content">
                <div class="records table-responsive">
                    <div class="record-header">
                        <div class="browse">
                            <input type="search" placeholder="Search" id="feedback-search" />
                            <select name="" id="feedback-filter">
                                <option value="">All</option>
                                <option value="Foverites">Foverites</option>
                                <option value="Testimonial">Testimonial</option>
                                <option value="Unreaded">Unreaded</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="feedback-box">
                <div class="feedback-list">
                    <h3>FEEDBACKERS</h3>
                    <ul>
                    </ul>
                </div>
                <div class="feedback-view">
                    <h3>Comments</h3>
                    <div class="feedback-msg">

                    </div>
                    <div class="feedback-actions">
                        <span title="Add to Favorites" class="las la-bookmark feedActions fav"></span>
                        <span title="Share to Testimonial" class="las la-share feedActions tes"></span>
                        <span title="Remove Feedback" class="las la-trash feedActions"></span>
                        <!-- <span title="Add to Favorites" class="las la-bookmark feedActions fav">&Ffr;</span>
                        <span title="Share to Testimonial" class="las la-share feedActions tes">&curvearrowright;</span>
                        <span title="Remove Feedback" class="las la-trash feedActions">&boxtimes;</span> -->
                    </div>
                </div>
            </div>
        </main>
        <!-- ================#FFF TRANSACTION =================================================== -->
        <main class="main Transaction-main hidden">
            <div class="page-header">
                <h1>Transactions</h1>
                <small>Home / Transactions</small>
            </div>

            <div class="page-content">
                <div class="analytics">
                    <div class="card selling">
                        <div class="card-head">
                            <h2 class="currentPrice__transaction"></h2>
                            <span class="las la-dolar"></span>
                        </div>
                        <div class="card-progress">
                            <small>Selling Price</small>
                            <!-- ------------------------------------------------------------ -->
                            <button class="update-price">
                                <span class="las la-arrow-down"></span>.<span class="las la-arrow-up"></span>
                            </button>
                            <div class="card-indicator">
                                <div class="indicator two"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="loss__transaction"></h2>
                            <span class="las la-sell"></span>
                        </div>
                        <div class="card-progress">
                            <small>Lost</small>
                            <!-- ------------------------------------------------------------ -->
                            <div class="card-indicator">
                                <div class="indicator four"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="earn__transaction"></h2>
                            <span class="las la-income"></span>
                        </div>
                        <div class="card-progress">
                            <small>Earning</small>
                            <!-- ------------------------------------------------------------ -->
                            <div class="card-indicator">
                                <div class="indicator one"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2 class="ballance__transaction"></h2>
                            <span class="las la-feedback"></span>
                        </div>
                        <div class="card-progress">
                            <small>Ballance</small>
                            <!-- ------------------------------------------------------------ -->
                            <div class="card-indicator">
                                <div class="indicator three"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="records table-responsive">
                    <div class="record-header">
                        <div class="add">
                            <button>Add Transaction</button>
                        </div>

                        <div class="browse">
                            <input type="search" id="transactionSearch" placeholder="Search" class="record-search" />
                            <select name="" id="transactionFilter">
                                <option value="">All</option>
                                <option value="sell">Earning</option>
                                <option value="buy">Loss</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>REASON</th>
                                    <th>AMOUNT</th>
                                    <th>TYPE</th>
                                </tr>
                            </thead>
                            <tbody class="transactionTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>


    <div class="modal2 hidden">
        <div class="btn--close-modal">&times;</div>
        <h2 class="modal__header">Change Password</h2>

        <form class="modal__form" method="post" action="admin.php">
            <input type="hidden" name="identify" value="changePassword" />
            <label>🗝️</label>
            <input type="Password" name="password" class="p" placeholder="***************" />
            <label>Confirm 🗝️</label>
            <input type="password" name="confirmPassword" placeholder="***************" class="cp" />
            <!-- <button>Change password 🗝️</button> -->
            <input type="submit" value="Change password 🗝️">
        </form>
    </div>

    <script type="module" src="js/adminScript.js"></script>
</body>

</html>