<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../DATABASE/Database.php';
date_default_timezone_set("Asia/Manila");

class User
{

    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }

    public function login($email, $password, $rememberMe)
    {

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        var_dump($result->num_rows);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            var_dump("Session variables being set");

            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['logged_in'] = true;

            var_dump($_SESSION);

            if ($rememberMe) {
                setcookie("email", $email, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
                setcookie("rememberMe", $rememberMe, time() + (86400 * 30), "/");
            } else {
                setcookie("email", $email, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
                setcookie("rememberMe", "", time() + (86400 * 30), "/");
            }
            return "success";
        } else {
//            $stmt->bind_param("s", $email);
//            $stmt->execute();

            return "Invalid username or password!";
        }
    }
}

$error = "";
$success = "";
$email = "";
$password = "";
$rememberMe = false;

if (isset($_COOKIE["rememberMe"]) && $_COOKIE["rememberMe"]) {
    if (isset($_COOKIE["email"]) && isset($_COOKIE["password"])) {
        $email = $_COOKIE["email"];
        $password = $_COOKIE["password"];
        $rememberMe = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new Database();
    $user = new User($db);

    $email = $_POST["email"];
    $password = $_POST["password"];
    $rememberMe = isset($_POST["rememberMe"]);

    $result = $user->login($email, $password, $rememberMe);
    if ($result === "success") {
        $success = "Signed in Successfully!";
        header("Location: ../HOME/dashboard.php");
        exit();
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Login</title>

    <!-- Custom fonts for this template-->
    <link href="../../FRONTEND/ASSETS/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../FRONTEND/ASSETS/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                </div>
                                <form class="user" action="login.php" method="POST">
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user"
                                               id="exampleInputEmail" name="email" aria-describedby="emailHelp"
                                               placeholder="Enter Email Address..."
                                               value="<?= $rememberMe ? htmlspecialchars($email) : '' ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user"
                                               id="exampleInputPassword" name="password" placeholder="Password"
                                               value="<?= $rememberMe ? htmlspecialchars($password) : '' ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input"
                                                   id="customCheck" name="rememberMe"
                                                    <?= $rememberMe ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="register.php">Create an Account!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Bootstrap core JavaScript-->
<script src="../../FRONTEND/ASSETS/vendor/jquery/jquery.min.js"></script>
<script src="../../FRONTEND/ASSETS/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="../../FRONTEND/ASSETS/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="../../FRONTEND/ASSETS/js/sb-admin-2.min.js"></script>

</body>

</html>
