<!DOCTYPE HTML>
<!-- (c) 2004,2007,2008 jprk -->
<html>
<head>
<title> {$lecture.code} / {include file=$maincolumntitle}</title>
<xmeta http-equiv="Content-Type" content="text/html; charset=xutf-8">
<!-- feed search engines with reasonable data -->
<meta name="keywords" lang="cs" content="systémy, procesy, matematické modelování">
<meta name="keywords" lang="en" content="systems, processes, mathematical modelling">
<meta name="description" lang="cs" content="Modelování systémů a procesů">
<meta name="description" lang="en" content="Systems and processes">
<!-- Bootstrap -->
<link href="{$BASE_DIR}/css/bootstrap.css" rel="stylesheet" media="screen">
<!-- fonts -->
<link href='//fonts.googleapis.com/css?family=Open+Sans:700|Titillium+Web&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<!-- styles for the application -->
<link href="{$BASE_DIR}/css/style.css" rel="stylesheet" title="formal" type="text/css">
<link href="{$BASE_DIR}/ex/{$lecture.id}/metadata.css" rel="stylesheet" title="formal" type="text/css">
<link href="{$BASE_DIR}/css/stylist.css" rel="stylesheet" title="formal" type="text/css">
{include file=$htmlareaheader}
{include file=$calendarheader}
</head>
<body>
<!-- body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0" -->

<div class="container">
    <div class="row">
        <div id="lecture-title" class="col-md-10 col-xs-12">
            <h1>{cmslink act="show" obj="home" id={$lecture.id} text="{$lecture.code}&nbsp;&ndash;&nbsp;{$lecture.title}"}</h1>
        </div>
        <div class="col-md-2 text-right">
            {cmslink act="show" obj="login" id="42" text="login"} | <a href="">logout</a> |
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-right bg-primary wide-line">
            uživatel: {if $isAdmin == 1 || $isLecturer == 1}{cmslink act="show" obj="user" id={$uid} text={$login}}{elseif $isStudent == 1}{cmslink act="show" obj="student" id={$uid} text={$login}}{else}{$login}{/if} |
        </div>
    </div>
    <div class="row hidden-sm hidden-xs">
        <div class="col-md-2 singleimg"><img src="http://placehold.it/200x80" class="img-responsive hidden-sm"></div>
        <div class="col-md-2 singleimg"><img src="http://placehold.it/200x80" class="img-responsive hidden-sm"></div>
        <div class="col-md-2 singleimg"><img src="http://placehold.it/200x80" class="img-responsive hidden-sm"></div>
        <div class="col-md-2 singleimg"><img src="http://placehold.it/200x80" class="img-responsive hidden-sm"></div>
        <div class="col-md-2 singleimg"><img src="http://placehold.it/200x80" class="img-responsive hidden-sm"></div>
        <div class="col-md-2 singleimg"><img src="http://placehold.it/200x80" class="img-responsive hidden-sm"></div>
    </div>
    <div class="row">
        <div class="col-md-2 no-padding-left">
        {include file=$leftcolumn}
        </div>
        <div class="col-md-10 main">
{* news *}
{if isset($newsList)}
            <div class="row">
                <div class="col-md-6">
                    <h2 class="bg-info no-margin-top">Novinky</h2>
{include file="news.tpl"}
                </div>
                <div class="col-md-6">
                    <img src="{$BASE_DIR}/ex/{$lecture.id}/news.png" width="240" height="158" alt="" border="0">
                </div>
            </div>
{/if}
            {* page title *}
            <h2 class="bg-info no-margin-top">{include file=$maincolumntitle}</h2>
{* main text of the page comes here *}
{include file=$maincolumn}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 bg-primary wide-line">
            <small>{if isset($section) && isset($section.lastmodified)}
            Poslední změna obsahu: {$section.lastmodified|date_format:"%d.%m.%Y %H:%M:%S"}.
            {/if}
            Vzniklo díky podpoře grantu FRVŠ 1344/2007{$lecture.thanks}.
            </small>
        </div>
    </div>
</div>
<script src="//code.jquery.com/jquery-latest.js"></script>
<script src="{$BASE_DIR}/js/bootstrap.js"></script>
{include file=$calendarfooter}
</body>
</html>
