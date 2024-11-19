<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update page</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            background-color: #eee;
            width: 100dvw;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: #333;
        }

        ul {
            padding: 15px;
            box-shadow: 0 0 10px #fff;
            list-style-type: none;
            border-radius: 10px;
        }

        li:first-child::before {
            /* list-style-type: square; */
            /* background-color: #fff; */
            content: '🌟';
            margin-right: 10px;
            background-color: #333;
            padding: 2px;
        }

        li:first-child {
            margin-left: -35px;
            background-color: rgb(51, 51, 51, .4);
            padding: 2px;
        }

        li {
            margin-bottom: 10px;
        }

        li>a {
            font-weight: bold;
        }

        span {
            background-color: #fff;
            padding: 5px;
            color: #333;
        }
    </style>
</head>

<body>
    <h1>Update your <span>SMARTIZER</span> to the latest version</h1>
    <div>&curvearrowleft;<a href="home/index.php"> Back to home page</a></div><br>
    <div>🌟 = latest version</div>
    <ul>
        <?php
        require_once './connectDB.php';
        $sql = "SELECT `firmware` FROM `firmwares` ORDER BY `date`DESC";

        if ($res = $con->query($sql)) {
            while ($firm = $res->fetch_assoc()) {

                $firmware = str_replace('firmwares/', 'Smartizer SZ-', $firm['firmware']);

                echo '<li><a href="' . $firm['firmware'] . '" download="' . $firmware . '">' . $firmware . '</a></li>';
            }
            die();
        } else die("<h2>Sorry cann't fetch firmwares form the server!!</h2>")
        ?>
    </ul>
</body>

</html>