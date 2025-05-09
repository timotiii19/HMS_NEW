<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HMSHomepage</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .background {
            background: url('../images/banner5.png') no-repeat center center fixed; 
            background-size: full;
            background-position: center;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        

        .top-bar {
            position: absolute;
            top: 50px;
            right: 50px;
        }

        .login-button {
            width: 150px;
            height: 65px;
            margin-right: 40px;
            padding: 10px 20px;
            background-color: #ffffffcc;
            color: #333;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #ff6b97;
            color: white;
        }

        .homepage-text {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
        }

        .homepage-text h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .homepage-text p {
            font-size: 24px;

    </style>
</head>
<body>
    <div class="background">
        <div class="top-bar">
            <a href="login.php">
                <button class="login-button">Login</button>
            </a>
        </div>
        <div class="homepage-text">
            <h1>Welcome to Our Hospital System</h1>
            <p>Your health is our top priority.</p>
        </div>
    </div>
</body>
</html>
   
