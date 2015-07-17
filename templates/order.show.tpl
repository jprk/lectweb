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
	
	if ( form.name.value.length  == 0 ) s += "Jm�no nesm� b�t pr�zdn�.\n";
	if ( form.phone.value.length == 0 ) s += "Mus�te zadat Va�e telefonn� ��slo.\n";
	
	// E-mail
	e = form.email.value;

	if ( e.length == 0 )
	{
		s += "Mus� b�t vypln�na va�e e-mail adresa.\n";
	}
	else if ( e.indexOf (".") <= 2 || e.indexOf ("@") <= 0 )
	{
		s += "Zadan� e-mail adresa nen� platn�.\n";
	}

	// Company name		
	if ( form.company.value.length == 0 ) s += "N�zev firmy je povinn� �daj.\n";

	// I�O
	e = form.ico.value;

	if ( e.length == 0 )
	{
		s += "I�O Va�� firmy je povinn� �daj.\n";
	}
	else if ( regexpSearch (e, _reICO))
	{
		s += "I�O ('" + e + "') nesm� obsahovat nic jin�ho, ne� ��slice.\n";
	}

	// DI�
	e = form.dic.value;

	if ( e.length == 0 )
	{
		s += "DI� Va�� firmy je povinn� �daj.\n";
	}
	else if ( ! regexpSearch (e, _reDIC))
	{
		s += "DI� ('" + e + "') nesm� obsahovat nic jin�ho, ne� k�d zem� a ��slice.\n";
		s += "Pro DI� lze ale pou��t i starou notaci 123-4567890.\n";
	}

	if ( form.addr1.value.length == 0 && form.addr2.value.length == 0 ) s += "Nen� vypln�na adresa Va�� firmy.\n";

	if ( form.postid.value.length == 0 ) s += "PS� je povinn� �daj.\n";
	if ( form.city.value.length   == 0 ) s += "M�sto je povinn� �daj.\n";

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
<h1>Objedn�vka</h1>
<form name="offerform" action="ctrl.php?act=save,order,42" method="post" onSubmit="return check(this);">
<p>
<table border="0" width="100%">
<colgroup>
<col>
<col>
<col class="wide">
</colgroup>
<tr class="order"><td class="otitle">Jm�no:</td           ><td colspan="2"><input type="text" name="name"     class="wide"></td></tr>
<tr class="order"><td class="otitle">E-mail:</td          ><td colspan="2"><input type="text" name="email"    class="wide"></td></tr>
<tr class="order"><td class="otitle">Telefon:</td         ><td colspan="2"><input type="text" name="phone"    size="20"   ></td></tr>
<tr class="order"><td class="otitle">Fax:</td             ><td colspan="2"><input type="text" name="fax"      size="20"   ></td></tr>
<tr class="order"><td class="otitle">Pozice:</td          ><td colspan="2"><input type="text" name="position" class="wide"></td></tr>
<tr class="order"><td class="otitle">N�zev&nbsp;firmy:</td><td colspan="2"><input type="text" name="company"  class="wide"></td></tr>
<tr class="order"><td class="otitle">I�O:</td             ><td colspan="2"><input type="text" name="ico"      size="8"    ></td></tr>
<tr class="order"><td class="otitle">DI�:</td             ><td colspan="2"><input type="text" name="dic"      size="12"   ></td></tr>
<tr class="order"><td class="otitle">Adresa:</td          ><td colspan="2"><input type="text" name="addr1"    class="wide"></td></tr>
<tr class="order"><td class="otitle">&nbsp;</td           ><td colspan="2"><input type="text" name="addr2"    class="wide"></td></tr>
<tr class="order"><td class="otitle">PS�/M�sto:</td       ><td><input type="text" name="postid" size="8"></td><td><input type="text" name="city" class="wide"></td></tr>
<tr class="order"><td class="otitle">St�t:</td            ><td colspan="2"><input type="text" name="state"    class="wide"></td></tr>
<tr class="order"><td class="otitle">Pozn�mka:</td        ><td colspan="2"><textarea name="comment" class="wide"></textarea></td></tr>
</table>
<p>
Dovolujeme si upozornit, �e ceny v GBP a USD se p�epo��taj� na K� v�dy dle aktu�ln�ho kursu
anglick� libry a americk�ho dolaru v den objedn�n�. N�e uveden� tabulka p�edpokl�d� kurs
{$settings.gbpczk}&nbsp;K�/GBP a {$settings.usdczk}&nbsp;K�/USD. Uveden� ceny neobsahuj�
baln�, po�tovn� po �R, a u zbo��, jeho� ceny nejsou uvedeny v K�, d�le DPH 5% u&nbsp;knih a 19%
u CD-ROM.
</p>
<table border="0" cellspacing="0" cellpadding="2">
<tr>
<th>&nbsp;</th>
<th>Ks</th>
<th>Titul</th>
<th>Cena</th>
<th>Po�tovn�</th>
<th style="text-align: right;">Orienta�n�<br/>cena<br/>v&nbsp;CZK</th>
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
<td colspan="5"><strong>&nbsp;Orienta�n� celkov� cena:</strong></td>
<td class="pricenum"><span id="cmstotal">0</span><br/>CZK</td>
</tr>
</table>
<input type="submit" value="Odeslat objedn�vku">
</form>
<p>
Dovolujeme si upozornit, �e ceny v GBP a USD se p�epo��taj� na K� v�dy dle aktu�ln�ho kursu
anglick� libry a americk�ho dolaru v den objedn�n�. V��e uveden� tabulka p�edpokl�d� kurs
{$settings.gbpczk}&nbsp;K�/GBP a {$settings.usdczk}&nbsp;K�/USD. Uveden� ceny neobsahuj�
baln�, po�tovn� po �R, a u zbo��, jeho� ceny nejsou uvedeny v K�, d�le DPH 5% u&nbsp;knih a 19%
u CD-ROM.
</p>
