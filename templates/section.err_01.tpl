<p>
Snažíte se zobrazit údaje o neexistujícím předmětu.
</p>
{if $lectureList}
<p>
Máme založeny tyto weby předmětů: 
</p>
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<tr class="adminlisthead">
<th style="width: 6ex;">Kód</th>
<th style="width: 80%; text-align: left;">&nbsp;Název</th>
<th style="width: 16px;">&nbsp;</th>
</tr>
{section name=aId loop=$lectureList}
{if $smarty.section.aId.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td align="center">&nbsp;{$lectureList[aId].code}&nbsp;</td>
<td>&nbsp;{$lectureList[aId].title}</td>
<td width="16" class="smaller" valign="middle"
  ><a href="?act=show,home,{$lectureList[aId].id}"><img src="images/famfamfam/application_go.png" title="přepnout" alt="[přepnout]" width="16" height="16"></a
  ></td>
</tr>
{/section}
</table>
{else}
<p>
Ještě nejsou založeny žádé weby předmětů. 
</p>
{/if}

