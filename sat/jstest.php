<?php


function JSTest( string $uuid )
{

    $csrf_token  = csrf_token();

    $new_uuid_route = route( 'sattest.jstest.newuuid' );
    $new_csrf_route = route( 'sattest.jstest.newcsrf', $uuid );

    return <<<HTML
<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{$csrf_token}" />

  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto+Mono" />
  <link rel="stylesheet" type="text/css" href="/sat/sat.css" />

  <script type="text/javascript" src="/sat/jquery-3.6.4.min.js"></script>
  <script type="text/javascript" src="/sat/js.cookie.min.js"></script>
  <script type="text/javascript" src="/sat/aes.js"></script>

  <script type="text/javascript" src="/sat/CookieEncrypter.js"></script>
  <script type="text/javascript" src="/sat/PayFlowForm.js"></script>

<style>
#logs
{
    margin:         20px 10px;
    padding:        10px;
    height:         600px;
    width:          600px;
    font-family:    'Roboto Mono', monospace;
    font-size:      12px;
    border:         2px solid blue;
}
</style>

<script>
$(document).ready(function() {

    CookieEncrypter.init( PayFlowForm.cookie_key, window.location.pathname );

    //
    // buttons
    //

    $( '#set_cookie_btn' ).click(function() { PayFlowForm.setCookie(); });
    $( '#get_cookie_btn' ).click(function() { PayFlowForm.getCookie(); });
    $( '#del_cookie_btn' ).click(function() { PayFlowForm.delCookie(); });
    $( '#new_uuid_btn'   ).click(function() { window.location.href='{$new_uuid_route}'; });
    $( '#new_csrf_btn'   ).click(function() { window.location.href='{$new_csrf_route}'; });

});
</script>

</head>
<body>

<h3>JS Test</h3>

<button id="set_cookie_btn">Set Cookie</button> &nbsp;
<button id="get_cookie_btn">Get Cookie</button> &nbsp;
<button id="del_cookie_btn">Del Cookie</button> &nbsp;
<button id="new_uuid_btn">New UUID Route</button>
<button id="new_csrf_btn">New CSRF Token</button>

<div id="logs"></div>

</body>
</html>
HTML;

}
