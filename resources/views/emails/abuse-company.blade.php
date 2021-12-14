<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Shoogle Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
{{--    <link rel="stylesheet" type="text/css" href="./css/new-company.css">--}}
</head>

<body>
<div class="container">
    <h1>You have received a complaint</h1>
    <h2>Hello dear company administrator {{$companyAdminName}}</h2>
    <div class="dividered-section">
        <ul class="list">
            <li>
                <div class="text">
                    <div class="title"><strong>A complaint was received from a user</strong></div>
                    <div class="description">
                        {{$fromUserName}}
                    </div>
                </div>

                <div class="text">
                    <div class="title"><strong>Date and time of complaint</strong></div>
                    <div class="description">
                        {{$dateAbuseTextFormat}}
                    </div>
                </div>

                <div class="text">
                    <div class="title"><strong>Complained about user</strong></div>
                    <div class="description">
                        {{$toUserName}}
                    </div>
                </div>

                <div class="text">
                    <div class="title"><strong>The text of the message complained about</strong></div>
                    <div class="description">
                        {{$messageText}}
                    </div>
                </div>
            </li>
        </ul>
        <div class="footer">
            <div class="f-text">
                (c) <?php echo date("Y"); ?> shoogle. All rights reserved.
            </div>
            <div class="f-social">
                <a class="fb" href="#" title="Facebook"><img src="{{asset('images/new-company/ico-facebook.svg')}}" alt="Facebook"></a>
                <a class="tw" href="#" title="Twitter"><img src="{{asset('images/new-company/ico-twitter.svg')}}" alt="Twitter"></a>
                <a class="insta" href="#" title="Instagram"><img src="{{asset('images/new-company/ico-instagram.svg')}}" alt="Instagram"></a>
            </div>
        </div>
    </div>
</div>
</body>

</html>
