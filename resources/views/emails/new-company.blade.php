<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Shoogle Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./css/new-company.css">
</head>

<body>
    <div class="container">
        <h1>Welcome to shoogle</h1>
        <h2>Supercharge your wellbeing & performance <br>
            We help busy employees connect with and work on their wellbeing together, using our mobile app.</h2>
        <div class="button-row">
            <a class="button" href="https://shoogle.io" title="Learn more">Learn more</a>
        </div>
        <div class="dividered-section">
            <div class="divider"></div>
            <ul class="list">
                <li>
                    <div class="img">
                        <img src="{{asset('images/new-company/img-01.png')}}" alt="Shoogle Image">
                    </div>
                    <div class="text">
                        <div class="title">Create goals</div>
                        <div class="description">
                            Our system monitors and controls incoming and outgoing network traffic
                        </div>
                    </div>
                    <a href="https://shoogle.io" class="ico" title="Create goals">
                        <img src="{{asset('images/new-company/arrow.png')}}" alt="Shoogle Arrow">
                    </a>
                </li>
                <li>
                    <div class="img">
                        <img src="{{asset('images/new-company/img-02.png')}}" alt="Shoogle Image">
                    </div>
                    <div class="text">
                        <div class="title">Join communities</div>
                        <div class="description">
                            Data mining, text analytics, business intelligence and data visualization
                        </div>
                    </div>
                    <a href="https://shoogle.io" class="ico" title="Join communities">
                        <img src="{{asset('images/new-company/arrow.png')}}" alt="Shoogle Arrow">
                    </a>
                </li>
                <li>
                    <div class="img">
                        <img src="{{asset('images/new-company/img-03.png')}}" alt="Shoogle Image">
                    </div>
                    <div class="text">
                        <div class="title">Buddy up</div>
                        <div class="description">
                            Information is encoded and can only be accessed with the encryption key
                        </div>
                    </div>
                    <a href="https://shoogle.io" class="ico" title="Buddy up">
                        <img src="{{asset('images/new-company/arrow.png')}}" alt="Shoogle Arrow">
                    </a>
                </li>
                <li>
                    <div class="img">
                        <img src="{{asset('images/new-company/img-04.png')}}" alt="Shoogle Image">
                    </div>
                    <div class="text">
                        <div class="title">Get to work</div>
                        <div class="description">
                            Safeguarding important information from corruption, compromise or loss
                        </div>
                    </div>
                    <a href="https://shoogle.io" class="ico" title="Get to work">
                        <img src="{{asset('images/new-company/arrow.png')}}" alt="Shoogle Arrow">
                    </a>
                </li>
            </ul>
            <div class="app-row">
                <div class="app-store">
                    <a href="https://apps.apple.com/gb/app/shoogle/id1587699258#?platform=ipad" title="App Store"><img src="{{asset('images/new-company/app-store.png')}}" alt="App Store"></a>
                </div>
                <div class="google-play">
                    <a href="https://play.google.com/store/apps/details?id=com.shoogle.shoogleapp.shoogleapp&hl=en&gl=US" title="Google play"><img src="{{asset('images/new-company/google-play.png')}}" alt="Google play"></a>
                </div>
            </div>
            <div class="divider"></div>
            <div class="footer">
                <div class="f-text">
                    (c) {{ now()->year }} shoogle. All rights reserved.
                </div>
                <div class="f-social">
                    <a class="fb" href="https://www.facebook.com/shoogleapp" title="Facebook"><img src="{{asset('images/new-company/ico-facebook.png')}}" alt="Facebook"></a>
                    <a class="tw" href="https://twitter.com/search?q=%23shoogleapp&src=typed_query&f=top" title="Twitter"><img src="{{asset('images/new-company/ico-twitter.png')}}" alt="Twitter"></a>
                    <a class="insta" href="https://www.instagram.com/shoogleapp/" title="Instagram"><img src="{{asset('images/new-company/ico-instagram.png')}}" alt="Instagram"></a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>