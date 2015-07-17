<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td colspan="3">Homepage</td>
<td width="40" class="smaller" 
  ><a href="ctrl.php?act=edit,home,42"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></td>
</tr>
{section name=sectionPos loop=$sections}
<tr>
  {section name=indent loop=$sections[sectionPos].indents}
  <td width="16" height="16"><img src="images/{$sections[sectionPos].indents[indent]}" width="16" height="16"></td>
  {/section}
<td colspan="{$sections[sectionPos].numIndents}">{$sections[sectionPos].title}</td>
<td width="40" class="smaller"  valign="middle"
  ><a href="ctrl.php?act=edit,section,{$sections[sectionPos].Id}"><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a
  ><a href="ctrl.php?act=delete,section,{$sections[sectionPos].Id}"><img src="images/delete.gif" alt="[delete]" width="16" height="16"></a></td>
</tr>
{/section}
</table>