<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Under Maintenance | Green Park</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fc;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .maintenance-container {
            width: 100%;
            max-width: 650px;
            text-align: center;
            background: #ffffff;
            padding: 52px 36px;
            border: 1px solid #e5eaf3;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(15, 35, 75, 0.08);
        }

        .brand-name {
            font-size: 23px;
            font-weight: 800;
            color: #173f91;
            letter-spacing: 0.5px;
            margin-bottom: 32px;
        }

        .illustration {
            width: 100px;
            height: 100px;
            margin: 0 auto 28px;
            border-radius: 28px;
            background: #eaf1ff;
            color: #2459c4;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .illustration svg {
            width: 56px;
            height: 56px;
        }

        .status {
            display: inline-block;
            padding: 8px 15px;
            background: #fae8be;
            color: #3d0101;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 1.2px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: clamp(28px, 5vw, 40px);
            line-height: 1.2;
            font-weight: 800;
            color: #172554;
            margin-bottom: 18px;
        }

        .description {
            max-width: 480px;
            margin: 0 auto;
            color: #64748b;
            font-size: 16px;
            line-height: 1.8;
        }

        .notice {
            margin-top: 30px;
            padding: 17px;
            border-radius: 12px;
            background: #f7f9fd;
            color: #475569;
            font-size: 14px;
            line-height: 1.7;
        }

        .notice strong {
            color: #1e40af;
        }

        .footer {
            margin-top: 34px;
            padding-top: 22px;
            border-top: 1px solid #edf0f5;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.8;
        }

        @media (max-width: 480px) {
            .maintenance-container {
                padding: 36px 22px;
                border-radius: 16px;
            }

            .brand {
                margin-bottom: 32px;
            }

            .description {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <main class="maintenance-container">
        <!-- <div class="illustration">
            <img src="{{asset('./assets/img/favicon.png')}}" alt="Maintenance Illustration" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        </div> -->
        <div class="brand-name" >
          GREEN PARK
        </div>
        <div class="status">SCHEDULED MAINTENANCE</div>
        <h1>We'll Be Back Soon!</h1>
        <p class="description"> Green Park Website is currently undergoing scheduled maintenance to improve system performance and provide you with a better experience. </p>

        <div class="notice">
            <strong>Thank you for your patience.</strong><br>Our team is working to restore the service as soon as possible. Please try again later.
        </div>

        <div class="footer">
            &copy; 2026 Green Park. All rights reserved.<br> We appreciate your understanding.
        </div>
    </main>
</body>
</html>