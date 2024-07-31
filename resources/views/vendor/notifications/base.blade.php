<!DOCTYPE html>
<html>
<head>
    <style>
        /* Fallback CSS (in case of any unsupported email clients) */
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            margin: 0 auto;
            padding: 20px;
            border-radius: 5px;
            background-color: #fff;
        }
        .header {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }
        .header img {
            height: 80px;
            margin-right: 20px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            padding: 20px;
        }
        .footer {
            padding-top: 10px;
            border-top: 3px solid #005EB8;
            border-bottom: 3px solid #DB1F26;
        }
        .footer p {
            font-size: 12px;
            color: #999;
            margin: 5px 0;
        }
        .footer a {
            color: #005EB8;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
<div class="container" style="margin: 0 auto; padding: 20px; border-radius: 5px; background-color: #fff;">
    <div class="header" style="display: flex; align-items: center; padding: 10px 0;">
        <img src="https://www.undp.org/sites/g/files/zskgke326/files/migration/acceleratorlabs/file_6.png" alt="ERA Logo" style="height: 80px; margin-right: 20px;">
        <div>
            <p style="margin: 0; font-size: 14px; padding: 20px;">Electricity Regulatory Authority<br>
                ERA House<br>
                Plot 5C-1, Third Street, Lugogo, Kampala<br>
                P.O. Box 10332 Kampala, Uganda<br>
            </p>
        </div>
        <div>
            <p style="margin: 0; font-size: 14px; padding: 20px;">Office: +256 417 101800, +256 393 260166<br>
                Consumer Affairs Direct line: 0200506000<br>
                Website: <a href="https://www.era.go.ug" style="color: #005EB8;">www.era.go.ug</a><br>
                Twitter: <a href="https://twitter.com/ERA_Uganda" style="color: #005EB8;">@ERA_Uganda</a>
            </p>
        </div>
    </div>
    <div class="content">
        @yield('content')
    </div>
    <div class="footer" style="padding-top: 10px; border-top: 3px solid #005EB8; border-bottom: 3px solid #DB1F26;">
        <p style="font-size: 12px; color: #999; margin: 5px 0;">This email (and attachments to it) is confidential and for the sole use of the person/entity it is addressed to. Check for and fix viruses and the like, if present. The Electricity Regulatory Authority (ERA) accepts no liability arising from this email (or Attachments to it). If you received this email in error please notify our System Administrator. NOTE: Views/opinions in this email (and attachments) are those of the author and do not necessarily represent those of ERA.</p>
    </div>
</div>
</body>
</html>
