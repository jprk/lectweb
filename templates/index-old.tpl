{* Smarty *}
{config_load file="main.conf"}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>{$pname}</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link REL=StyleSheet HREF="{#wbase#}/k611base.css" TYPE="text/css">
</head>
<body>
<div id="main">
<table border=0 cellspacing=0 cellpadding=0 width="750">
<tr>
<td>
<table border=0 cellspacing=0 cellpadding=0 width="750">
<tr>
<td class="title"><div id="title"><img src="{#wbase#}/{$pdir}/title.jpg" width="469" height="93" border="0" alt="{$title_alt}"></div></td>
<td class="image"><div id="image"><img src="{#wbase#}/{$pdir}/image.png" width="258" height="93" border="0" alt="image"></div></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
{strip}
<div id="navigation">
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
</td>
</tr>
<tr>
<td>
<table border=0 cellspacing=0 cellpadding=0 width="750" >
<tr>
<td class="shortcuts" width="150"><div id="shortcuts">{include file="`$smarty.server.DOCUMENT_ROOT`/`$smarty.config.wbase`/$pdir/$sname" project="$prjct"}</div></td>
<td width="650"><div id="content">{include file="`$smarty.server.DOCUMENT_ROOT`/`$smarty.config.wbase`/$pdir/$cname"}</div></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<div id="footer">(c) 2003 k611@fd - naposledy změněno {$mtime}</div>
</td>
</tr>
</table>
</div>
</body>
</html>
