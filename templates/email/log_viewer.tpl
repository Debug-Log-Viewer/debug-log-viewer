<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Debug Log Viewer</title>
    <style>
        html {
            font-family: Helvetica, Arial, sans-serif;
            color: #414141;
        }

        .container {
            max-width: 540px;
            margin: 0 auto;
        }

        .wrapper {
            box-sizing: border-box;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }

        .wrapper .logo {
            margin-top: -100px;
        }

        h2 {
            font-size: 18px;
            font-weight: 700;
        }

        p {
            font-size: 14px;
            text-align: center;
        }

        table tr td p {
            text-align: left;
            padding-left: 10px;
        }

        p:first-child {
            text-align: left;
        }

        table {
            width: 100%;
        }

        table,
        th,
        td {
            border: 1px solid #b4b4b4;
            border-collapse: collapse;
            padding: 10px;
        }

        .footer p {
            font-size: 12px;
            text-align: center;
        }

        a {
            color: #aea6da;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer {
            background: linear-gradient(#2e82ef, #361cc1);
            padding: 24px;
            color: #fff;
        }

        .footer a {
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <p>Hi!</p>
            <p>Monitoring of the Debug Log Viewer plugin reports about serious problems detected on your website {{dlv_website}}</p>

            <table border=1>
                {{dlv_summary}}
            </table>

            <p>Pay attention to the problems to ensure the best experience for visitors to your website. Thank you for using the Debug Log Viewer!</p>
        </div>

        <div class="footer">
            <p>
                Have any questions? Write us <a href="mailto:sanchoclo@gmail.com">sanchoclo@gmail.com</a>
            </p>
            <p>Your Debug Log Viewer team</p>
        </div>
    </div>
</body>

</html>
