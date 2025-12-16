<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login - K Lush Blosom</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap');

    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f8cdda 0%, #1d2b64 100%);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #444;
    }
    .login-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 16px;
      width: 360px;
      box-shadow: 0 10px 30px rgba(255, 192, 203, 0.3);
      text-align: center;
      position: relative;
    }
    .login-container::before {
      content: "";
      position: absolute;
      top: -40px;
      left: calc(50% - 40px);
      width: 80px;
      height: 80px;
      background: #f8cdda;
      border-radius: 50%;
      filter: blur(35px);
      z-index: -1;
    }
    h2 {
      margin-bottom: 24px;
      font-weight: 500;
      font-size: 1.8rem;
      color: #b83b5e;
      letter-spacing: 1px;
    }
    label {
      display: block;
      text-align: left;
      margin-bottom: 6px;
      font-weight: 500;
      color: #555;
      font-size: 0.9rem;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #f8cdda;
      border-radius: 12px;
      font-size: 1rem;
      transition: 0.3s ease;
      outline: none;
      color: #444;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #b83b5e;
      box-shadow: 0 0 8px rgba(184, 59, 94, 0.5);
    }
    button {
      width: 100%;
      background: #b83b5e;
      border: none;
      border-radius: 14px;
      padding: 14px 0;
      color: white;
      font-weight: 600;
      font-size: 1.1rem;
      margin-top: 20px;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(184, 59, 94, 0.6);
      transition: background 0.3s ease;
    }
    button:hover {
      background: #e75480;
      box-shadow: 0 8px 20px rgba(231, 84, 128, 0.7);
    }
    .error {
      background-color: #ffdde0;
      color: #b83b5e;
      padding: 10px;
      border-radius: 10px;
      margin-bottom: 15px;
      font-weight: 600;
      font-size: 0.9rem;
      text-align: center;
    }
    /* Responsive */
    @media (max-width: 400px) {
      .login-container {
        width: 90vw;
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Admin Login</h2>
    <?php
    if (isset($_SESSION['login_error'])) {
        echo '<div class="error">'.htmlspecialchars($_SESSION['login_error']).'</div>';
        unset($_SESSION['login_error']);
    }
    ?>
    <form action="loginHandler.php" method="post" autocomplete="off">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autofocus>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
      <button type="submit">Log In</button>
    </form>
  </div>

</body>
</html>
