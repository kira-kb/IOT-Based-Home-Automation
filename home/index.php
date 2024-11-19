<?php

$msg = "";

if (isset($_GET['errMSG'])) {
    if (!empty($_GET['errMSG'])) {
        $msg = $_GET['errMSG'];
    }
}

require "../connectDB.php";

$sql = "SELECT * FROM `feedbacks` WHERE `testimonial` = 'true'";

$testimonial = false;

$middle = '';

if ($res = $con->query($sql)) {
    if ($res->num_rows > 0) {
        $testimonial = true;

        while ($row = mysqli_fetch_assoc($res)) {

            $template = '<div class="slide">
                <div class="testimonial">
                    <h5 class="testimonial__header">%SUBJECT%!</h5>
                    <blockquote class="testimonial__text">%MESSAGE%</blockquote>
                    <address class="testimonial__author">

                        <h6 class="testimonial__name">
                            <a href="mailto:%EMAIL%"
                                class="testimonial__name">%EMAIL%</a>
                        </h6>

                    </address>
                </div>
                </div>';

            $placeholder = str_replace("%SUBJECT%", $row['subject'], $template);
            $placeholder = str_replace("%MESSAGE%", $row['message'], $placeholder);
            $placeholder = str_replace("%EMAIL%", $row['email'], $placeholder);
            $placeholder = str_replace("%EMAIL%", $row['email'], $placeholder);

            $middle .= $placeholder;

            // die($middle);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/home.css" />
    <title>Smartize your home with us</title>

    <script defer src="../js/home.js"></script>
</head>

<body>
    <header class="header">
        <nav class="nav">
            <h1 class="nav__logo">Smartizer</h1>
            <a class="nav__link updt" href="../software release.php">UPDATE</a>
            <ul class="nav__links">
                <li class="nav__item">
                    <a class="nav__link" href="#section--1">Features</a>
                </li>
                <li class="nav__item">
                    <a class="nav__link" href="#section--2">Operations</a>
                </li>
                <?php if ($testimonial) {
                    echo '
                <li class="nav__item">
                    <a class="nav__link" href="#section--3">Testimonials</a>
                </li>';
                } ?>
            </ul>

            <ul class="nav__links for__mobile">
                <li class="nav__item">
                    <a class="nav__link" href="#section--1">Features</a>
                </li>
                <li class="nav__item">
                    <a class="nav__link" href="#section--2">Operations</a>
                </li>
                <?php if ($testimonial) {
                    echo '
                <li class="nav__item">
                    <a class="nav__link" href="#section--3">Testimonials</a>
                </li>';
                }
                ?>
            </ul>

            <a class="nav__link nav__link--btn btn--show-modal" href="#">Login</a>
        </nav>

        <div class="header__title">
            <h1>
                When
                <span class="highlight">SMARIZER</span>
                meets<br />
                <span class="highlight">HOUSES</span>
            </h1>
            <h4>A simpler SMARTIZER experience for a simpler life.</h4>
            <button class="btn--text btn--scroll-to">Learn more &DownArrow;</button>
            <img src="../img/default/hero.png" class="header__img" alt="Minimalist bank items" />
        </div>
    </header>

    <section class="section" id="section--1">
        <div class="section__title">
            <h2 class="section__description">Features</h2>
            <h3 class="section__header">
                Everything you need in a modern house and more.
            </h3>
        </div>

        <div class="features">
            <img src="../img/default/bluetooth.png" alt="Computer" class="features__img lazy-img" />
            <div class="features__feature">
                <div class="features__icon">
                    <svg>
                        <use xlink:href="../img/default/icons.svg#icon-bluetooth"></use>
                    </svg>
                </div>
                <h5 class="features__header">BLUETOOTH</h5>
                <p>
                    This Bluetooth-enabled device puts smart home control at your fingertips. Pair it with your
                    smartphone for effortless control of lights, locks, or thermostats, all without a separate hub or
                    complex setup.
                </p>
            </div>

            <img src="../img/default/wifi.png" alt="Plant" class="features__img lazy-img img__second" />
            <div class="features__feature features__second">
                <div class="features__icon">
                    <svg>
                        <use xlink:href="../img/default/icons.svg#icon-wifi"></use>
                    </svg>
                </div>
                <h5 class="features__header">WIFI</h5>
                <p>
                    Unleash smart home convenience with this Wi-Fi enabled device. Ditch the hub and multiple remotes!
                    Control lights, locks, thermostats - all from your phone with a single app or a single link.
                </p>
            </div>

            <img src="../img/default/ir remote.png" alt="Credit card" class="features__img lazy-img" />
            <div class="features__feature">
                <div class="features__icon">
                    <svg>
                        <use xlink:href="../img/default/icons.svg#icon-radio"></use>
                    </svg>
                </div>
                <h5 class="features__header">IR REMOTE</h5>
                <p>
                    Transform your home without a hub! This deviceunlocks IR remote control smart control for TVs, fans,
                    and
                    more. Ditch the clutter and unify your entertainment with a single, powerful remote.
                </p>
            </div>
        </div>
    </section>

    <section class="section" id="section--2">
        <div class="section__title">
            <h2 class="section__description">Operations</h2>
            <h3 class="section__header">
                Everything as simple as possible, but no simpler.
            </h3>
        </div>

        <div class="operations">
            <div class="operations__tab-container">
                <button class="btn operations__tab operations__tab--1 operations__tab--active" data-tab="1">
                    <span>01</span>How To Bluetooth
                </button>
                <button class="btn operations__tab operations__tab--2" data-tab="2">
                    <span>02</span>How To WiFi
                </button>
                <button class="btn operations__tab operations__tab--3" data-tab="3">
                    <span>03</span>How To Ir
                </button>
            </div>
            <div class="operations__content operations__content--1 operations__content--active">
                <div class="operations__icon operations__icon--1">
                    <svg>
                        <use xlink:href="../img/default/icons.svg#icon-bluetooth"></use>
                    </svg>
                </div>
                <h5 class="operations__header">How did it work with bluetooth.</h5>
                <p>
                    <b>Turn on, Bluetooth.</b> Activate Bluetooth on your device and put it in pairing mode.
                    <br>
                    <b>Search and pair.</b> Use your phone or another device to find nearby Bluetooth device. Choose
                    the device and follow any pairing prompts (like entering a code).
                    <br>
                    <b>Use the APP.</b> Once paired, open the app and connect it, onece it is connected it automatically
                    connect for other days.
                </p>
            </div>

            <div class="operations__content operations__content--2">
                <div class="operations__icon operations__icon--2">
                    <svg>
                        <use xlink:href="../img/default/icons.svg#icon-wifi"></use>
                    </svg>
                </div>
                <h5 class="operations__header">
                    <!-- Controlling your SMARTIZER with WiFi around the globe. -->
                    Wi-Fi magic for your smart home! Here's how it works.
                </h5>
                <p>
                    <b>Connecting to WiFi:</b> Press and hold the portal, The device hosts a hotspot with your device
                    name,
                    <br> &DDotrahd; connect to the network and goto <a target="_blank"
                        href="http//:192.168.1.2">192.168.1.2</a>,
                    <br> &DDotrahd; choose wifi
                    credential and device automatically seeks out your Wi-Fi network, connect it,
                    <br> &DDotrahd; if it is connected wifi signal led get turned on.
                    <br>
                    <b>Interface:</b> <a href="resource.php" target="_blank">Download</a> the companion app on your
                    phone or
                    tablet or <a href="" class="btn--show-modal">login</a>.
                    <br>
                    <b>Smart Control:</b> You can use the app or webpage to control the device from anywhere in the
                    workd. No need to be right next to it!
                </p>
            </div>
            <div class="operations__content operations__content--3">
                <div class="operations__icon operations__icon--3">
                    <svg>
                        <use xlink:href="../img/default/icons.svg#icon-radio"></use>
                    </svg>
                </div>
                <h5 class="operations__header">
                    No longer need your smart phone.
                </h5>
                <p>
                    The device works with IR Remote you can choose your smart phone or IR Remote.
                </p>
            </div>
        </div>
    </section>

    <?php
    $upper = '<section class="section" id="section--3">
        <div class="section__title section__title--testimonials">
            <h2 class="section__description">Not sure yet?</h2>
            <h3 class="section__header">
                All SMARTIZER users are already making their lifes simpler.
            </h3>
        </div>
        <div class="slider">';

    $lowwer = '
            <button class="slider__btn slider__btn--left">&larr;</button>
            <button class="slider__btn slider__btn--right">&rarr;</button>
            <div class="dots"></div>
        </div>
    </section>';

    echo $upper . $middle . $lowwer;

    ?>

    <section class="section section--sign-up">
        <div class="section__title">
            <h3 class="section__header">
                The best day to smartize your home was one year ago. The second best is
                today!
            </h3>
        </div>
        <button class="btn btn--show-modal">Login</button>
    </section>

    <footer class="footer">
        <ul class="footer__nav">
            <!-- <li class="footer__item">
                <a class="footer__link" href="#">About</a>
            </li>
            <li class="footer__item">
                <a class="footer__link" href="#">Pricing</a>
            </li>
            <li class="footer__item">
                <a class="footer__link" href="#">Terms of Use</a>
            </li>
            <li class="footer__item">
                <a class="footer__link" href="#">Privacy Policy</a>
            </li>
            <li class="footer__item">
                <a class="footer__link" href="#">Careers</a>
            </li>
            <li class="footer__item">
                <a class="footer__link" href="#">Blog</a>
            </li>
            <li class="footer__item">
                <a class="footer__link" href="#">Contact Us</a>
            </li> -->
            <li class="footer__item">
                <a class="footer__link" href="#section--1">Features</a>
            </li>
            <li class="footer__item">
                <a class="footer__link" href="#section--2">Operations</a>
            </li>
        </ul>

        <h1 class="footer__logo">Smartizer</h1>
        <p class="footer__copyright">
            &copy; Copyright by
            <a href="mailto:kirubelbewket@gmail.com" class="footer__link" target="_blank">kirubelbewket@gmail.com</a>.
            design for IoT Based Home Automation.
        </p>
    </footer>

    <!-- <div class="modal"> -->
    <div class="modal hidden">
        <button class="btn--close-modal">&times;</button>
        <h2 class="modal__header">
            well comeback <br />
            <span class="highlight">Smartizer</span>
        </h2>

        <!-- <div class="info"> -->
        <div class="info hidden">
            <span class="x">&times;</span>
            <span class="loginMSG"></span>
        </div>

        <form class="modal__form" action="../login.php" method="post">
            <label>Email</label>
            <input type="email" name="email" placeholder="email address" />
            <label>Password</label>
            <input type="password" name="password" placeholder="***********" />
            <div class="resetPassword"><a href="../resetPassword.php">Forget Password?.</a></div>
            <button class="btn">Login &rarr;</button>
        </form>
    </div>

    <div class="overlay hidden"></div>

    <script>
        let errMSG = '';
    </script>

    <?php echo "<script>errMSG = '$msg'; </script>"; ?>

    <script>
        if (errMSG) {
            document.querySelector('.modal').classList.remove('hidden')
            document.querySelector('.info').classList.remove('hidden');
            document.querySelector('.overlay').classList.remove('hidden');
            document.querySelector('.loginMSG').textContent = errMSG;
        }

        document.querySelector('.info').querySelector('.x').addEventListener('click', () => document.querySelector('.info')
            .classList.add('hidden'))
    </script>
</body>

</html>