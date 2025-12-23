<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {

    $userid   = mysqli_real_escape_string($conn, $_POST['userid']);
    $password = md5($_POST['password']); // because DB uses md5

    $sql = "SELECT * FROM admin WHERE userid='$userid' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_name'] = $row['name'];

        header("Location: dashboard.php");
        exit();

    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="title" content="Admin Panel" />
  <meta name="author" content="ColorlibHQ" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
    integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
    crossorigin="anonymous" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
    integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
    crossorigin="anonymous" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
    crossorigin="anonymous" />
  <link rel="stylesheet" href="dist/css/adminlte.css" />
</head>

<body class="login-page bg-body-secondary">
  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <a
          href="#"
          class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover">
          <h1 class="mb-0"><b>Admin</b> Panel</h1>
        </a>
      </div>
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <form action="index.php" method="post">
          <div class="input-group mb-1">
            <div class="form-floating">
              <input id="loginEmail" type="text" name="userid" class="form-control" required />
              <label for="loginEmail">User ID</label>
            </div>
            <div class="input-group-text"><span class="bi bi-envelope"></span></div>
          </div>

          <div class="input-group mb-1">
            <div class="form-floating">
              <input id="loginPassword" type="password" name="password" class="form-control" required />
              <label for="loginPassword">Password</label>
            </div>
            <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
          </div>

          <div class="row">
            <div class="col-4 offset-8">
              <button type="submit" name="login" class="btn btn-primary w-100">
                Sign In
              </button>
            </div>
          </div>
        </form>

        <!-- <div class="social-auth-links text-center mb-3 d-grid gap-2">
            <p>- OR -</p>
            <a href="#" class="btn btn-primary">
              <i class="bi bi-facebook me-2"></i> Sign in using Facebook
            </a>
            <a href="#" class="btn btn-danger">
              <i class="bi bi-google me-2"></i> Sign in using Google+
            </a>
          </div>

          <p class="mb-1"><a href="forgot-password.html">I forgot my password</a></p>
          <p class="mb-0">
            <a href="register.html" class="text-center"> Register a new membership </a>
          </p> -->
      </div>
    </div>
  </div>
</body>

</html>