<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HMSHomepage</title>
    <style>
        
        *{
          margin: 0;
          padding: 0;
          box-sizing: border-box;}

        body, html {
            height: 100%;
            font-family: 'Segoe UI', sans-serif;
        }

        .background {
            background: url('../images/banner5.png') no-repeat center center fixed; 
            background-size: cover;
            background-position: center;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        .tagline{
            font-family: 'Roboto', sans-serif;
            font-size: 100px;

        }

        .headline{
            font-family: 'Montserrat', sans-serif;
            font-size: 48px;
            font-weight: 700;

        }

        .subtext{
            font-family: 'arial', sans-serif;
            font-size: 16px;
            line-height: 1.5;

        }


        .top-bar {
            position: absolute;
            top: 50px;
            right: 50px;
        }

        .login-button {
            width: 150px;
            height: 65px;
            margin-right: 100px;
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
            top: 60%;
            left: 43%;
            transform: translate(-50%, -50%);
            text-align: left;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .homepage-text h1 {
            font-size: 50px;
            margin-bottom: 10px;
            margin-top: 10px;

        }

        .homepage-text p {
            font-size: 14px;
            margin-right: 290px;

        }
        .homepage-text at {
            font-size: 24px;
            margin-right: 60px;

        }

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
            <at class="tagline">Compassion in Care, Excellence in Healing</at>
            <h1 class="headline">Leading the way in medical excellence</h1>
            <p class="subtext">CHART Memorial Hospital has been recognized as one of the Top Healthcare Institutions, known for its advanced integration of cutting-edge technology, streamlined processes, and state-of-the-art medical systems. Our commitment to operational excellence and data-driven decision-making ensures the highest quality of patient care, supported by advanced analytics and intelligent healthcare solutions.</p>
        </div>
    </div>
</body>
</html>