<h1>Seznam ulo�en�ch novinek</h1>
<table border="1" cellpadding="4" cellspacing="0">
<tr>
<td colspan="2">P�idat novinku</td>
<td width="32" class="smaller" 
  ><a href="ctrl.php?act=edit,news,0"><img src="images/add.gif" alt="[p�idat novinku]" width="16" height="16"></a></td>
</tr>
{if $newsList}
<tr>
<th>Text</th>
<th>Datum</th>
<th>&nbsp;</th>
</tr>
{section name=nId loop=$newsList}
{if $smarty.section.nId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td>{$newsList[nId].title}</td>
<td class="smaller">{$newsList[nId].datefrom|date_format:"%d.%m.%Y"}</td>
<td width="32" class="smaller" valign="middle"
  ><a href="ctrl.php?act=edit,news,{$newsList[nId].id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="ctrl.php?act=delete,news,{$newsList[nId].id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
{/if}
</table>
<hr size="1" color="black"/>
<p>
<a href="?act=admin,exclist,1">zp�t na spr�vu p�edm�tu</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="?act=delete,login,42">odhl�sit</a>
</p>