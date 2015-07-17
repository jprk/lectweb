<p>
Následujícím studentům bylo odevzdávání úlohy <em>{$subtask.title}</em>
prodlouženo do {$dateto}:
</p>
<ul>
{section name=sId loop=$studentList}
<li>{$studentList[sId].surname} {$studentList[sId].firstname} ({$studentList[sId].yearno}/{$studentList[sId].groupno})</li>
{/section}
</ul>
<p>
Dále můžete pokračovat libovolnou akcí z administrativního menu vlevo.
</p>
