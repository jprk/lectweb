var den;
var mesiac;
var rok;

//mesiace = new Array("janu�r","febru�r","marec","apr�l","m�j","j�n","j�l","august","september","okt�ber","november","december");
var mesiace = new Array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");
var dni = new Array("po","út","st","čt","pá","so","ne");
// No calendar window opened
var calWindow = null;

function openCalendar(frm,fld)
{
	/* Check the status of the global reference to the calendar window. */
  if ( calWindow == null || calWindow.close )
  {
    /* No window present or it has been already cloase. Open it. */
    calWindow = window.open( "calendar/calendar.html", "cal",
                             "width=300,height=200,status=no" );
  }
  else
  {
    /* The window exists somewhere. Focus it. */
    calWindow.focus();
  }
	
  /* Now that the window has opened, store the value of the form field
     referenced by `frm` and `fld` into new `dateField` property of the
     opener window. */
  calWindow.opener.dateField = eval ( "document." + frm + "." + fld );
}

function initCalendar()
{
  if (!rok && !mesiac && !den && window.opener.dateField.value)
  {
	d = window.opener.dateField.value.split(".");
	den = d[0];
	mesiac = d[1]-1;
	rok = d[2];
  }
  if ((!rok && !mesiac && !den) || (isNaN(rok) || isNaN(mesiac) || isNaN(den)))
  {
	initDate = new Date();
	den = initDate.getDate();
	mesiac = initDate.getMonth();
	rok = initDate.getYear();
	if ( rok < 1000 )
	{
	  if (rok < 70 )
      {
        rok = 2000+rok;
      }
	  else
      {
        rok = 1900+rok;
      }
	}
  }

  if (mesiac > 11) { mesiac = 0;  rok++; }
  if (mesiac < 0)  { mesiac = 11; rok--; }
	
  if (document.getElementById)
  {
	cnt = document.getElementById("content");
  }
  else if (document.all)
  {
	cnt = document.all["content"];
  }
	
  cnt.innerHTML = "";
  str = "";
  str += "<table><tr><th><a href='#' onclick='mesiac--; initCalendar();'>&laquo;</a> " + mesiace[mesiac] + " <a href='#' onclick='mesiac++; initCalendar();'>&raquo;</a></th><th><a href='#' onclick='rok--; initCalendar();'>&laquo;</a> " + rok + " <a href='#' onclick='rok++; initCalendar();'>&raquo;</a></th></tr></table>";

  str += "<table><tr>";
  for (i = 0; i < dni.length; i++)
  {
	str += "<th>" + dni[i] + "</th>";
  }
  
  var firstDay = new Date(rok, mesiac, 1).getDay();
  if (firstDay == 0) {firstDay = 7;}
  var lastDay = new Date(rok, mesiac+1, 0).getDate();

  str += "<tr>";
	
  dayInWeek = 0;	
  for (i = 1; i < firstDay; i++)
  {
	str += "<td>&nbsp;</td>";
	dayInWeek++;
  }
  for (i = 1; i <= lastDay; i++)
  {
	if (dayInWeek == 7)
	{
	  str += "</tr><tr>";
	  dayInWeek = 0;
	}
	dayInWeek++;
	dzero = ( i < 10 ) ? "0" : "";
	mzero = ( mesiac < 9 ) ? "0" : "";
    actVal = dzero + i + "." + mzero + (mesiac+1) + "." + rok;
	str += "<td><a href='#' onclick='returnDate(\"" + actVal + "\");'>" + i + "</a></td>"
  }
  for (i = dayInWeek; i < 7; i++)
  {
	str += "<td>&nbsp;</td>";
  }
	
  str += "</tr></table>";

  cnt.innerHTML = str;
}

function returnDate(d)
{
  window.opener.dateField.value = d;
  window.close();
}