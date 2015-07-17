{if $isAdmin || $isLecturer}&nbsp;<a href="?act=edit,file,{$sectionFileList[filePos].id}&returntoparent=1"
    ><img style="float: none;" src="images/edit.gif"
          alt="[edit]" width="16" height="16" align="texttop"></a
	 ><a href="?act=delete,file,{$sectionFileList[filePos].id}&returntoparent=1"
    ><img style="float: none;" src="images/delete.gif"
          alt="[smazat]" width="16" height="16" align="texttop"></a>{/if}