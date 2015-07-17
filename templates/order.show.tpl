{literal}
<script language="JavaScript">

var _total = 0.0;
var _pcs = new Array();
var _reICO = new RegExp ("\\D");
var _reDIC = new RegExp ("^[A-Z][A-Z]\\d+$|^\\d{3}-\\d+$");
var _reTel = new RegExp ("[^-+ 0-9]");

function regexpSearch ( string, re )
{
	return ( string.search(re) == -1 ) ? 0 : 1;
}


function check ( form )
{
	var s = "";
	var e;
	
	if ( form.name.value.length  == 0 ) s += "Jméno nesmí být prázdné.\n";
	if ( form.phone.value.length == 0 ) s += "Musíte zadat Vaše telefonní èíslo.\n";
	
	// E-mail
	e = form.email.value;

	if ( e.length == 0 )
	{
		s += "Musí být vyplnìna vaše e-mail adresa.\n";
	}
	else if ( e.indexOf (".") <= 2 || e.indexOf ("@") <= 0 )
	{
		s += "Zadaná e-mail adresa není platná.\n";
	}

	// Company name		
	if ( form.company.value.length == 0 ) s += "Název firmy je povinný údaj.\n";

	// IÈO
	e = form.ico.value;

	if ( e.length == 0 )
	{
		s += "IÈO Vaší firmy je povinný údaj.\n";
	}
	else if ( regexpSearch (e, _reICO))
	{
		s += "IÈO ('" + e + "') nesmí obsahovat nic jiného, než èíslice.\n";
	}

	// DIÈ
	e = form.dic.value;

	if ( e.length == 0 )
	{
		s += "DIÈ Vaší firmy je povinný údaj.\n";
	}
	else if ( ! regexpSearch (e, _reDIC))
	{
		s += "DIÈ ('" + e + "') nesmí obsahovat nic jiného, než kód zemì a èíslice.\n";
		s += "Pro DIÈ lze ale použít i starou notaci 123-4567890.\n";
	}

	if ( form.addr1.value.length == 0 && form.addr2.value.length == 0 ) s += "Není vyplnìna adresa Vaší firmy.\n";

	if ( form.postid.value.length == 0 ) s += "PSÈ je povinný údaj.\n";
	if ( form.city.value.length   == 0 ) s += "Mìsto je povinný údaj.\n";

	if ( s != "" )
	{
		alert (s);
		return false;
	}
	
	return true;	
}

function getProperties ( obj )
{
	var s = "";
	for ( i in obj )
	{
		s += "x." + i + "='" + obj[i] + "'\n";
	}
	alert (s);
}

function addoffer (checker, eid, amount)
{
	var elem = document.getElementById("cmsp"+eid);

	//getProperties(elem);
	
	if ( checker.checked )
	{
		_total += amount;
		elem.disabled = false;
		if ( elem.value <= 0 ) elem.value = 1;
	}
	else
	{
		elem.disabled = true;
		_total -= amount;
	}
	
	_pcs[eid] = elem.value;
	_total = Math.round ( 100 * _total ) / 100;
	document.getElementById("cmstotal").childNodes[0].nodeValue = "" + _total ;
}

function changeoffer ( field, eid, amount )
{
	if ( field.value < 1 ) field.value = 1;
	_total += amount * ( field.value - _pcs[eid] );
	_pcs[eid] = field.value;
	document.getElementById("cmstotal").childNodes[0].nodeValue = "" + _total ;
}

</script>
{/literal}
<h1>Objednávka</h1>
<form name="offerform" action="ctrl.php?act=save,order,42" method="post" onSubmit="return check(this);">
<p>
<table border="0" width="100%">
<colgroup>
<col>
<col>
<col class="wide">
</colgroup>
<tr class="order"><td class="otitle">Jméno:</td           ><td colspan="2"><input type="text" name="name"     class="wide"></td></tr>
<tr class="order"><td class="otitle">E-mail:</td          ><td colspan="2"><input type="text" name="email"    class="wide"></td></tr>
<tr class="order"><td class="otitle">Telefon:</td         ><td colspan="2"><input type="text" name="phone"    size="20"   ></td></tr>
<tr class="order"><td class="otitle">Fax:</td             ><td colspan="2"><input type="text" name="fax"      size="20"   ></td></tr>
<tr class="order"><td class="otitle">Pozice:</td          ><td colspan="2"><input type="text" name="position" class="wide"></td></tr>
<tr class="order"><td class="otitle">Název&nbsp;firmy:</td><td colspan="2"><input type="text" name="company"  class="wide"></td></tr>
<tr class="order"><td class="otitle">IÈO:</td             ><td colspan="2"><input type="text" name="ico"      size="8"    ></td></tr>
<tr class="order"><td class="otitle">DIÈ:</td             ><td colspan="2"><input type="text" name="dic"      size="12"   ></td></tr>
<tr class="order"><td class="otitle">Adresa:</td          ><td colspan="2"><input type="text" name="addr1"    class="wide"></td></tr>
<tr class="order"><td class="otitle">&nbsp;</td           ><td colspan="2"><input type="text" name="addr2"    class="wide"></td></tr>
<tr class="order"><td class="otitle">PSÈ/Mìsto:</td       ><td><input type="text" name="postid" size="8"></td><td><input type="text" name="city" class="wide"></td></tr>
<tr class="order"><td class="otitle">Stát:</td            ><td colspan="2"><input type="text" name="state"    class="wide"></td></tr>
<tr class="order"><td class="otitle">Poznámka:</td        ><td colspan="2"><textarea name="comment" class="wide"></textarea></td></tr>
</table>
<p>
Dovolujeme si upozornit, že ceny v GBP a USD se pøepoèítají na Kè vždy dle aktuálního kursu
anglické libry a amerického dolaru v den objednání. Níže uvedená tabulka pøedpokládá kurs
{$settings.gbpczk}&nbsp;Kè/GBP a {$settings.usdczk}&nbsp;Kè/USD. Uvedené ceny neobsahují
balné, poštovné po ÈR, a u zboží, jehož ceny nejsou uvedeny v Kè, dále DPH 5% u&nbsp;knih a 19%
u CD-ROM.
</p>
<table border="0" cellspacing="0" cellpadding="2">
<tr>
<th>&nbsp;</th>
<th>Ks</th>
<th>Titul</th>
<th>Cena</th>
<th>Poštovné</th>
<th style="text-align: right;">Orientaèní<br/>cena<br/>v&nbsp;CZK</th>
</tr>
{section name=orderPos loop=$orderList}
{if $smarty.section.orderPos.iteration is even}
<tr class="rowA">
{else}
<tr class="rowB">
{/if}
<td valign="middle"><input id="cmso{$smarty.section.orderPos.iteration}" type="checkbox" name="orders[{$smarty.section.orderPos.iteration}]" value="{$orderList[orderPos].Id}"      onclick="addoffer(this,{$smarty.section.orderPos.iteration},{$orderList[orderPos].price2});"></td>
<td valign="middle"><input id="cmsp{$smarty.section.orderPos.iteration}" type="text"     name="pieces[{$smarty.section.orderPos.iteration}]" value="1" size="2" disabled="disabled" onchange="changeoffer(this,{$smarty.section.orderPos.iteration},{$orderList[orderPos].price2});"></td>
<td>
<p class="otitle">{$orderList[orderPos].title|escape:"html"}<input type="hidden" name="names[{$smarty.section.orderPos.iteration}]" value="{$orderList[orderPos].title|escape:"html"}">
<p class="oauthor">{$orderList[orderPos].author|escape:"html"}
</td>
<td class="pricenum">{$orderList[orderPos].price}<br/>{$orderList[orderPos].currency}</td>
{if $orderList[orderPos].currency == "GBP"}
<td class="pricenum">{$settings.mailgbp}<br/>GBP</td>
{else}
<td class="pricenum">0.00<br/>CZK</td>
{/if}
<td class="pricenum">{$orderList[orderPos].price2}<br/>CZK</td>
</tr>
{/section}
<tr class="ototal">
<td colspan="5"><strong>&nbsp;Orientaèní celková cena:</strong></td>
<td class="pricenum"><span id="cmstotal">0</span><br/>CZK</td>
</tr>
</table>
<input type="submit" value="Odeslat objednávku">
</form>
<p>
Dovolujeme si upozornit, že ceny v GBP a USD se pøepoèítají na Kè vždy dle aktuálního kursu
anglické libry a amerického dolaru v den objednání. Výše uvedená tabulka pøedpokládá kurs
{$settings.gbpczk}&nbsp;Kè/GBP a {$settings.usdczk}&nbsp;Kè/USD. Uvedené ceny neobsahují
balné, poštovné po ÈR, a u zboží, jehož ceny nejsou uvedeny v Kè, dále DPH 5% u&nbsp;knih a 19%
u CD-ROM.
</p>
