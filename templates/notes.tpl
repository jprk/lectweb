<h2>Poznámky</h2>
{section name=nId loop=$noteList}
<div class="newsitem">
<div class="newsbody">
<span class="newstime">[&nbsp;{$noteList[nId].date|date_format:"%d.%m.%Y"}&nbsp;/&nbsp;{$noteList[nId].author.firstname}&nbsp;{$noteList[nId].author.surname}&nbsp;]</span><br/>
{$noteList[nId].text}
</div>
</div>
{/section}
