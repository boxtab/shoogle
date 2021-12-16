<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Shoogle Front</title>
    <link rel="stylesheet" type="text/css" href="./css/fonts-gilroy.css">
    <link rel="stylesheet" type="text/css" href="./css/invite.css">
    {{-- <link rel="stylesheet" href="css/style.css">--}}

    <style>
        /*
        @font-face {
            font-family: 'Gilroy';
            src: url( {{asset('fonts/Gilroy-egular.eot')}} );
            src: local('Gilroy Regular'), local('Gilroy-Regular'),
            url({{ asset('fonts/Gilroy-Regular.eot?#iefix')}}) format('embedded-opentype'),
            url({{asset('fonts/Gilroy-Regular.woff2')}}) format('woff2'),
            url({{asset('fonts/Gilroy-Regular.woff')}}) format('woff'),
            url({{asset('fonts/Gilroy-Regular.ttf')}}) format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url({{asset('Gilroy-Bold.eot')}});
            src: local('Gilroy Bold'), local('Gilroy-Bold'),
            url({{asset('fonts/Gilroy-Bold.eot?#efix')}}) format('embedded-opentype'),
            url({{asset('fonts/Gilroy-Bold.woff2')}}) format('woff2'),
            url({{asset('fonts/Gilroy-Bold.woff')}}) format('woff'),
            url({{asset('fonts/Gilroy-Bold.ttf')}}) format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url('{{asset('fonts/Gilroy-lack.eot')}}');
            src: local('Gilroy Black'), local('Gilroy-Black'),
            url({{asset('fonts/Gilroy-Black.eot?#iefix')}}) format('embedded-opentype'),
            url({{asset('fonts/Gilroy-Black.woff2')}}) format('woff2'),
            url({{asset('fonts/Gilroy-Black.woff')}}) format('woff'),
            url({{asset('fonts/Gilroy-Black.ttf')}}) format('truetype');
            font-weight: 900;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url({{asset('fonts/Gilroy-emibold.eot')}});
            src: local('Gilroy Semibold'), local('Gilroy-Semibold'),
            url({{asset('fonts/Gilroy-Semibold.eot?#iefix')}}) format('embedded-opentype'),
            url({{asset('fonts/Gilroy-Semibold.woff2')}}) format('woff2'),
            url({{asset('fonts/Gilroy-Semibold.woff')}}) format('woff'),
            url({{asset('fonts/Gilroy-Semibold.ttf')}}) format('truetype');
            font-weight: 600;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url({{asset('fonts/Gilroy-xtrabold.eot')}});
            src: local('Gilroy Extrabold'), local('Gilroy-Extrabold'),
            url({{asset('fonts/Gilroy-Extrabold.eot?#iefix')}}) format('embedded-opentype'),
            url({{asset('fonts/Gilroy-Extrabold.woff2')}}) format('woff2'),
            url({{asset('fonts/Gilroy-Extrabold.woff')}}) format('woff'),
            url({{asset('fonts/Gilroy-Extrabold.ttf')}}) format('truetype');
            font-weight: 800;
            font-style: normal;
        }
*/
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- <div class="section"> -->
            <div class="logo">
                <a href="https://shoogle.io" title="Shoogle"><img src="{{asset('images/invite/logo.png')}}" alt="Shoogle"></a>
            </div>
            <div class="social">
                <a class="in" href="https://www.linkedin.com/company/shoogleapp/" title="Linkedin"><img src="{{asset('images/invite/linkedin.png')}}" alt="Linkedin"></a>
                <a class="insta" href="https://www.instagram.com/shoogleapp/" title="Instagram"><img src="{{asset('images/invite/instagram.png')}}" alt="Instagram"></a>
            </div>
            <!-- </div> -->
        </div>
        <div class="hero" style="background-image: url( {{asset('images/invite/sky.png')}} )">
            <div class="section">
                <div class="text">
                    <div class="title">
                        Super <br>
                        awesome
                    </div>
                    <div class="description">
                        Welcome to shoogle. <br>
                        Communities for the <br>
                        likeminded.
                    </div>

                </div>
                <div class="image">
                    <img src="{{asset('images/invite/phone.png')}}" alt="Mobile App">
                </div>
            </div>
        </div>
        <div class="list">
            <div class="section">
                <h2>Make the most of your experience</h2>
                <div class="columns">
                    <div class="item">
                        <div class="ico plus">
                            <img src="{{asset('images/invite/ico-plus.png')}}" alt="Create">
                        </div>
                        <div class="text">
                            Create and join <br>
                            shoogles that <br>
                            contribute to your <br>
                            wellbeing.
                        </div>
                    </div>
                    <div class="item">
                        <div class="ico list">
                            <img src="{{asset('images/invite/ico-list.png')}}" alt="Share Progress">
                        </div>
                        <div class="text">
                            Share progress, <br>
                            buddy up and <br>
                            support each <br>
                            other.
                        </div>
                    </div>
                    <div class="item">
                        <div class="ico envelop">
                            <img src="{{asset('images/invite/ico-envelop.png')}}" alt="Rewards">
                        </div>
                        <div class="text">
                            Give and receive <br>
                            rewards for positive <br>
                            interactions.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="section">
                <h2>Download shoogle</h2>
                <div class="app-row">
                    <div class="app-store">
                        <a href="https://apps.apple.com/gb/app/shoogle/id1587699258#?platform=ipad" title="App Store"><img src="{{asset('images/invite/app-store.png')}}" alt="App Store"></a>
                    </div>
                    <div class="google-play">
                        <a href="https://play.google.com/store/apps/details?id=com.shoogle.shoogleapp.shoogleapp&hl=en&gl=US" title="Google play"><img src="{{asset('images/invite/google-play.png')}}" alt="Google play"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>