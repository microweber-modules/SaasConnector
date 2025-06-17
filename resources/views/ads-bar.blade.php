<head>
    <?php get_favicon_tag(); ?>
</head>

<style>
    @import url("https://fonts.cdnfonts.com/css/milliard");

    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        font-family: "Milliard", sans-serif;
    }

    .mw-ads-holder {
        background: #fff;
        z-index: 99999;
        padding: 4px 7px;
        position: absolute;
        min-height: 40px;
        border: 0;
        left: 0;
        right: 0;
        top: 0;
        width: 100%;
        overflow: hidden;
        border-top: 1px solid #f1f3f4;
        color: #2d2d2d;
        font-size: 14px;
    }

    .mw-ads-holder p {
        margin: 0;
        padding: 12px 0;
    }

    .mw-ads-holder a.mw-ads-link {
        color: #1717ff;
        text-decoration: none;
        border: 1px solid #1717ff;
        border-radius: 3px;
        padding: 3px 12px;
        font-size: 13px;
        margin-inline-start: 5px;
    }
</style>

<div class="mw-ads-holder">
    <p>
        @if($isLiveEdit)
            This is your website preview. To start edit your site you must activate it first.
        @else
            This website is not activated. Please activate your website.
        @endif
        <a href="{{ $saasUrl }}" class="mw-ads-link" target="_blank">
            Activate in {{ $brandName }}
        </a>
    </p>
</div>
