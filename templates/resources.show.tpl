<h1><span class="editimg"
  ><a href="ctrl.php?act=edit,section,{$section.Id}"
  ><img src="images/edit.gif" alt="[edit]" width="16" height="16"></a></span
  >{$section.title|escape:"html"}</h1>
{$section.text}
<table border="0" cellspacing="0" cellpadding="4">
<colgroup>
<col width="20">
<col>
</colgroup>
{section name=resourcePos loop=$resourceList}
<tr>
<td valign="top"><img src="images/arrow.gif" width="16" height="16" alt="*">&nbsp;</td>
<td><a href="{$resourceList[resourcePos].url|escape:"html"}"><strong>{$resourceList[resourcePos].title|escape:"html"}</strong></a><br/>
({$resourceList[resourcePos].url|escape:"html"})<br/>
<p>{$resourceList[resourcePos].description|escape:"html"}</p>
</td>
</tr>
{/section}
</table>
