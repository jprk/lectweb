{if $isAdmin || $isLecturer}
    <a class="btn btn-primary" type="button" href="?payload={$lecture.code|strtolower}/node/edit/section/0">
        <span class="glyphicon glyphicon-plus"></span>
    </a>
    <a class="btn btn-primary" href="?payload={$lecture.code|strtolower}/node/edit/section/0&parent={$section.id}">
        <span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-chevron-right"></span>
    </a>
    <a class="btn btn-primary" href="?payload={$lecture.code|strtolower}/node/edit/file/0&objid={$section.id}&type=1&returntoparent=1">
        <span class="glyphicon glyphicon-paperclip"></span>
    </a>
    <a class="btn btn-primary" href="?payload={$lecture.code|strtolower}/node/edit/section/{$section.id}">
        <span class="glyphicon glyphicon-pencil"></span>
    </a>
    <a class="btn btn-primary" href="?payload={$lecture.code|strtolower}/node/delete,section,{$section.id}&returntoparent=1">
        <span class="glyphicon glyphicon-trash"></span>
    </a>
<!--  ><img src="images/new.gif"
    alt="[nová podsekce]" title="založit novou podsekci" width="16" height="16"></a
  ><a href="?act=edit,article,0&parent={$section.id}&type=4&returntoparent=1"
  ><img src="images/newarticle.gif"
    alt="[nový článek]" title="vložit nový článek" width="16" height="16"></a
  ><a href="?act=edit,file,0&objid={$section.id}&type=1&returntoparent=1"
  ><img src="images/newfile.gif"
    alt="[nový soubor]" title="vložit nový soubor" width="16" height="16"></a
  ><a href="?act=edit,section,{$section.id}"
  ><img src="images/edit.gif"
    alt="[edit]" title="editovat text této stránky" width="16" height="16"></a
  ><a href="?act=delete,section,{$section.id}&returntoparent=1"
  ><img src="images/delete.gif"
    alt="[delete]" title="smazat tuto stránku" width="16" height="16"></a
  ></span>-->
{/if}
