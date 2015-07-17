{* Smarty *}
{config_load file="main.conf"}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$pname}</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link rel=StyleSheet HREF="{#wbase#}/template2.css" TYPE="text/css">
</head>
<body>
    <div id="pagewidth" >
      <div id="outerhead" >
        <div id="innerhead">
          <div id="lefthead" >
            <div class="content_img"><img src="{#wbase#}/{$pdir}/title.jpg" width="469" height="93" border="0" alt="{$title_alt}"></div>
          </div>
          <div id="righthead" >
            <div class="content_img2"><img src="{#wbase#}/{$pdir}/image.png" width="258" height="93" border="0" alt="image"></div>
          </div>
          <div class="clr"></div>
          <!-- close inner and outer -->
        </div>
      </div>
      <div id="header" >
	    {strip}
        <div class="content">
<span id="menu">
<a href="{#wbase#}/katedra/">O katedře</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{#wbase#}/predmety/">Předměty</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{#wbase#}/projekty/">Projekty</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{#wbase#}/vyzkum/">Výzkum</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{#wbase#}/lide/">Lidé</a>
</span>
<span id="flags">
<a href="?lang=cs"><img src="{#wbase#}/_i/flag_cz.gif" width="20" height="11" border="0" alt="[cs]"></a>
&nbsp;&nbsp;
<a href="?lang=en"><img src="{#wbase#}/_i/flag_en.gif" width="20" height="11" border="0" alt="[en]"></a>
</span>
        </div>
        {/strip}
      </div>
      <div id="outer" >
        <div id="inner">
          <div id="leftcol" >
            <div class="content">{include file="`$smarty.server.DOCUMENT_ROOT`/`$smarty.config.wbase`/$pdir/$sname" project="$prjct"}</div>
          </div>
          <div id="maincol" >
            <div class="content">{include file="`$smarty.server.DOCUMENT_ROOT`/`$smarty.config.wbase`/$pdir/$cname"}</div>
          </div>
          <div class="clr"></div>
          <!-- close inner and outer -->
        </div>
      </div>
      <div id="footer" >
        <div class="content">(c) 2003 k611@fd - naposledy změněno {$mtime}</div>
      </div>
      <div class="clr"></div>
    </div>
</body>
</html>
