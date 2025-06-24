<!DOCTYPE html>
<html>
<head>
    <?php get_favicon_tag(); ?>
</head>

<body>
<div>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Raleway', sans-serif;
            color: #2b2b2b;
            background: #efefef;
            font-size: 22px;
            line-height: 32px;
        }
        a {
            color: #1f1f1f;
        }
    </style>

    <div style="
        width: 650px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 100vh;
        text-align: center;
">

        <div>
            <h1>
                Site not published yet
            </h1>
        </div>

        @php
        $websiteUrl = 'https://microweber.com/';
        $brandName = 'Microweber';
        if (isset($branding['brand_name'])) {
            $brandName = $branding['brand_name'];
        }
        if (isset($branding['website_manager_url'])) {
            $websiteUrl = $branding['website_manager_url'];
        }
        @endphp
        <div>
            <p>
                This site is currently not published yet.
                <br />
                If you're the owner of this website, login to
                <a href="{{$websiteUrl}}">
                    {{ $brandName }}
                </a> and upgrade your plan.
            </p>
        </div>

    </div>

</div>
</body>
</html>
