{section name=nId loop=$newsList}
<div class="panel panel-info">
    <div class="panel-heading"><h4>{$newsList[nId].title}</h4></div>
    <div class="panel-body">{$newsList[nId].text}</div>
    <div class="panel-footer text-right">[&nbsp;{$newsList[nId].datefrom|date_format:"%d.%m.%Y %H:%M"}&nbsp;/&nbsp;{$newsList[nId].author.firstname}&nbsp;{$newsList[nId].author.surname}&nbsp;]</div>
</div>
{/section}
