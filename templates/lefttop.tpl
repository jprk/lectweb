<h1>Populární stránky</h1>
<table border="0" cellspacing="0" cellpadding="0">
{foreach from=$toplist item=item}
<tr class="{$item.class}"><td valign="baseline">&raquo;&nbsp;</td><td class="{$item.class}" valign="baseline"><a href="ctrl.php?act=show,section,{$item.Id}">{$item.mtitle|escape:"html"}</a> <span class="count">({$item.count}x)</span></td></tr>
{/foreach}
</table>