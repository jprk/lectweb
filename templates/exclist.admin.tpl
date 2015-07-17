<h2>Bodování studentů</h2>
<p>
{if $exerciseList}
<table class="admintable" border="0" cellpadding="2" cellspacing="1">
<thead>
<tr class="newobject">
<th>Den</th>
<th>Od-do</th>
<th>Místnost</th>
<th>Cvičící</th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>
{section name=excPos loop=$exerciseList}
{if $smarty.section.excPos.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td class="center">{$exerciseList[excPos].day.name}</td>
<td class="center">{$exerciseList[excPos].from|date_format:"%H:%M"}&nbsp;-&nbsp;{$exerciseList[excPos].to|date_format:"%H:%M"}</td>
<td class="center">{$exerciseList[excPos].room}</td>
<td class="center">{$exerciseList[excPos].lecturer.firstname} {$exerciseList[excPos].lecturer.surname}</td>
<td class="center" style="height: 3.2ex;"
  ><a href="?act=show,exercise,{$exerciseList[excPos].id}"
    ><img src="images/famfamfam/application_view_detail.png" alt="[ukázat]"
          title="ukázat detail cvičení"></a
  > <a href="?act=show,exercise,{$exerciseList[excPos].id}&displaynames=true"
    ><img src="images/famfamfam/group.png" alt="[seznam]"
          title="ukázat seznam studentů na cvičení"></a
  > <a href="?act=edit,points,{$exerciseList[excPos].id}&type=exc"
    ><img src="images/famfamfam/award_star_add.png" alt="[body]"
          title="bodování studentů"></a
  ></td>
</tr>
{/section}
</tbody>
</table>
{else}
<p>Tento předmět ještě nemá přiřazena žádná cvičení.</p>
{/if}
{if $noteList}
{include file="notes.tpl"}
{/if}
