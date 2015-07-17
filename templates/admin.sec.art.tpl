{if $isAdmin || $isLecturer}<a href="?act=edit,article,{$articleList[articlePos].id}&returntoparent=1"
    ><img src="images/edit.gif" alt="[edit]" width="16" height="16" align="texttop"></a
	><a href="ctrl.php?act=delete,article,{$articleList[articlePos].id}&returntoparent=1"
    ><img src="images/delete.gif" alt="[smazat]" width="16" height="16" align="texttop"></a>{/if}