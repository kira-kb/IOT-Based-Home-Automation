<?php

session_start();

require_once './connectDB.php';

// $cookieAuth = $_COOKIE['auth'];

// $sessionAuth = $_SESSION[$cookieAuth];

// var_dump($sessionAuth);
// echo '<br><br>';
// (var_dump($_POST));
// echo '<br><br>';

$duplicateRoleHtml = '<style>
      * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
      }
      body {
        background-color: #eee;
      }

      .container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 255, 255, 05);
        padding: 4rem;
        text-align: center;
        font-family: sans-serif;
        /* font-weight: bold; */
      }

      h1 {
        margin-bottom: 1rem;
        color: #333;
      }

      form {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
      }
      button {
        padding: 0.7rem 2rem;
        border: none;
        color: #333;
        font-family: sans-serif;
        font-weight: bold;
        cursor: pointer;
        border-radius: 300px;
        transition: all 250ms;
      }
      button:hover {
        background-color: rgba(127, 255, 165, 0.442);
        padding: 01rem 2.3rem;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Login as</h1>
      <form action="login.php" method="post">
        <button type="submit" name="%EMPLOYEEROLE%">
          %EMPLOYEEROLE%
        </button>
        <h3>OR</h3>
        <button type="submit" name="customer">
          Customer
        </button>
      </form>
    </div>
';

function randStr($n)
{
    $randStr = "";
    $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz";
    while ($n > 0) {
        $index = rand(0, strlen($char) - 1);
        $randStr .= $char[$index];
        $n--;
    }
    return $randStr;
}

foreach ($_SESSION as $key) {
    if (!empty($key["end"])) {
        if (time() >= $key["end"]) {
            unset($_SESSION[$key["sessionName"]]);
        }
    }
}

if (isset($_POST['customer'])) {
    die(header("location: customer.php"));
}
if (isset($_POST['manufacturrer'])) {
    die(header("location: manufacturer.php"));
}
if (isset($_POST['admin'])) {
    die(header("location: admin.php"));
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = !empty($_POST["email"]) ? $_POST["email"] : "";
    $pass = !empty($_POST["password"]) ? $_POST["password"] : "";

    $role = '';
    $role2 = '';

    if ((empty($email) || empty($pass))) {
        die(header("location: home/index.php?errMSG=email and password connot be empty. please provide them correctly!"));
    }

    if (!preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $email)) {
        die(header("location: home/index.php?errMSG=invalid email or password!"));
    }

    $sql = "SELECT `device`, `password` FROM `customers` WHERE email = '$email'";

    if (!$res = $con->query($sql)) {
        $deviceName = '';
        $password = '';
        $role2 = '';
    }

    while ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['device'])) {
            $deviceName = $row['device'];
            $password = $row['password'];
            $role2 = 'customer';
        }

    }

    $hashed = md5(trim($pass));
    if (trim($hashed) != trim($password)) {
        $deviceName = '';
        $role2 = '';
    }

    $sql = "SELECT `email`, `password`, `role` FROM `employees` WHERE email = '$email'";

    if (!$res = $con->query($sql)) {
        $deviceName = '';
        $role2 = '';
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $password = $row['password'];
        $role = $row['role'];
    }

    $hashed = md5(trim($pass));
    if (trim($hashed) != trim($password)) {
        $role = '';
    }

    if (empty($role) && empty($role2)) {
        die(header("location: home/index.php?errMSG=invalid email📧 or password🗝️!"));
    }

    $auth = randStr(300);

    // if ($role == 'manufacturrer') $role = 'manufacturer';

    $_SESSION[$auth] = array(
        "deviceName" => $deviceName,
        "end" => time() + 86400,
        "role" => $role,
        "second role" => $role2,
        "email" => $email,
        "sessionName" => $auth,
    );

    setcookie('auth', $auth, time() + 86400);

    if ($role2 && $role) {
        die(str_replace('%EMPLOYEEROLE%', $role, $duplicateRoleHtml));
    } else if ($role2) {
        die(header("location: customer.php"));
    } else if ($role == 'admin') {
        die(header("location: admin.php"));
    } else if ($role == 'manufacturrer') {
        die(header("location: manufacturer.php"));
    }
}
// die('last');
header("location: home/index.php");
exit();
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

// <?php

// session_start();

// require_once './connectDB.php';

// $duplicateRoleHtml = '<style>
//       * {
//         box-sizing: border-box;
//         padding: 0;
//         margin: 0;
//       }
//       body {
//         background-color: #eee;
//       }

//       .container {
//         position: absolute;
//         top: 50%;
//         left: 50%;
//         transform: translate(-50%, -50%);
//         background-color: rgba(255, 255, 255, 05);
//         padding: 4rem;
//         text-align: center;
//         font-family: sans-serif;
//         /* font-weight: bold; */
//       }

//       h1 {
//         margin-bottom: 1rem;
//         color: #333;
//       }

//       form {
//         display: flex;
//         align-items: center;
//         justify-content: center;
//         gap: 1rem;
//       }
//       button {
//         padding: 0.7rem 2rem;
//         border: none;
//         color: #333;
//         font-family: sans-serif;
//         font-weight: bold;
//         cursor: pointer;
//         border-radius: 300px;
//         transition: all 250ms;
//       }
//       button:hover {
//         background-color: rgba(127, 255, 165, 0.442);
//         padding: 01rem 2.3rem;
//       }
// </style>
// <body>
// <div class="container">
//       <h1>Login as</h1>
//       <form action="login.php" method="post">
//         <button type="submit" name="%EMPLOYEEROLE%">
//           %EMPLOYEEROLE%
//         </button>
//         <h3>OR</h3>
//         <button type="submit" name="customer">
//           Customer
//         </button>
//       </form>
//     </div>
// </body>';

// function randStr($n)
// {
//     $randStr = "";
//     $char = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz";
//     while ($n > 0) {
//         $index = rand(0, strlen($char) - 1);
//         $randStr .= $char[$index];
//         $n--;
//     }
//     return $randStr;
// }

// foreach ($_SESSION as $key => $value) {
//     if (!empty($value["end"])) {
//         if (time() >= $value["end"]) {
//             unset($_SESSION[$key]);
//         }
//     }
// }

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
//     $pass = isset($_POST["password"]) ? trim($_POST["password"]) : "";

//     $role = "";
//     $role2 = "";

//     if (isset($_POST['customer'])) {
//         header("location: customer.php");
//         exit();
//     }
//     if (isset($_POST['seller'])) {
//         header("location: sale.php");
//         exit();
//     }
//     if (isset($_POST['manufacturrer'])) {
//         header("location: device.php");
//         exit();
//     }
//     if (isset($_POST['admin'])) {
//         header("location: admin.php");
//         exit();
//     }

//     if (empty($email) || empty($pass)) {
//         die(json_encode(array(
//             "status" => "fail",
//             "message" => "Email and password cannot be empty. Please provide them correctly!",
//         )));
//     }

//     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//         die(json_encode(array(
//             "status" => "fail",
//             "message" => "Invalid email format!",
//         )));
//     }

//     $stmt = $con->prepare("SELECT `device`, `password` FROM `customers` WHERE email = :email");
//     $stmt->bindParam(":email", $email);
//     $stmt->execute();

//     $customerData = $stmt->fetch();
//     if ($customerData) {
//         $deviceName = $customerData['device'];
//         $password = $customerData['password'];
//         $role2 = 'customer';

//         if (md5($pass) !== $password) {
//             $deviceName = "";
//             $role2 = "";
//         }
//     }

//     $stmt = $con->prepare("SELECT `email`, `password`, `role` FROM `employees` WHERE email = :email");
//     $stmt->bindParam(":email", $email);
//     $stmt->execute();

//     $employeeData = $stmt->fetch();
//     if ($employeeData) {
//         $password = $employeeData['password'];
//         $role = $employeeData['role'];

//         if (md5($pass) !== $password) {
//             $role = "";
//         }
//     }

//     if (empty($role) && empty($role2)) {
//         die(json_encode(array(
//             "status" => "fail",
//             "message" => "Invalid email or password!",
//         )));
//     }

//     $auth = randStr(300);

//     $_SESSION[$auth] = array(
//         "deviceName" => $deviceName,
//         "end" => time() + 86400,
//         "role" => $role,
//         "second role" => $role2,
//         "email" => $email,
//         "sessionName" => $auth,
//     );

//     setcookie('auth', $auth, time() + 86400);

//     if ($role2 && $role) {
//         die(str_replace('%EMPLOYEEROLE%', $role, $duplicateRoleHtml));
//     } else if ($role2) {
//         header("Location: customer.php");
//         exit();
//     } else if ($role == 'admin') {
//         header("Location: admin.php");
//         exit();
//     } else if ($role == 'manufacturer') {
//         header("Location: device.php");
//         exit();
//     } else if ($role == 'seller') {
//         header("Location: sale.php");
//         exit();
//     }
// }

// header("Location: home/index.php");
// exit();
