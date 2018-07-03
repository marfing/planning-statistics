var ResultsArray=new MakeArray(100);
if (document.all)
{
  document.styleSheets[0].addRule(".RightAlign","text-align: right");
  document.styleSheets[0].addRule(".CalcTable","background-color: buttonface");
  document.styleSheets[0].addRule(".CalcCaptionBack","background-color: activecaption");
  document.styleSheets[0].addRule(".CalcCaptionText","color: captiontext");
  document.MinutesForm.Lines_Edit.style.backgroundColor="buttonface"
}
ResultIndex=1;
wrapped=false;
ResultsOpen=false;Minutes=1;Lines=2;
HelpOpen=false;
UnknownVal=Lines;

function ResultsPressed()
{
var index,HasWrapped;
     if ((ResultIndex==1) && (!wrapped)) {alert("There are no results to display yet!")}
    else {

     ResultsWindow=window.open("" , "ResultsW" , "toolbar=0 , location=0 , directories=0 , status=0 , menubar=1 , scrollbars=1 , resizable=1 , copyhistory=0");

     ResultsWindow.document.write("<HTML><HEAD><TITL"+"E>Call Minutes Calculator Results Table</T"+"ITLE></HEAD>");
     ResultsWindow.document.write("<BODY BGCOLOR='#ffffff' LINK='#000080' VLINK='#008000'><P><font color='#000080' size='3' face='Arial'><strong>Call Minutes Results Table</strong></font></P>")
     ResultsWindow.document.write("<P><font size='2' face='Arial'><strong>Here are the results (max 20) of the Call Minutes Calculator. ");
     ResultsWindow.document.write("&nbspThe unknown figures are shown in red.</strong></font></p>");
     ResultsWindow.document.write("<P><table border='1' cellpadding='2' cellspacing='0'><TR><td align='center' bgcolor='#003399'>");
     ResultsWindow.document.write("<strong><font size='2' face='Arial' color='#FFFFFF'>Blocking target</strong></font></td>");
     ResultsWindow.document.write("<td align='center' bgcolor='#003399'>");
     ResultsWindow.document.write("<strong><font size='2' face='Arial' color='#FFFFFF'>Busy hour factor (%)</strong></font></td>");
     ResultsWindow.document.write("<td align='center' bgcolor='#003399'>");
     ResultsWindow.document.write("<strong><font size='2' face='Arial' color='#FFFFFF'>Minutes</strong></font></td>");
     ResultsWindow.document.write("<td align='center' bgcolor='#003399'>");
     ResultsWindow.document.write("<strong><font size='2' face='Arial' color='#FFFFFF'>Lines</strong></font></td></TR>");
     HasWrapped=!wrapped;
     if (wrapped) {index=ResultIndex} else {index=1};
     for (;(index!=ResultIndex) || (!HasWrapped);index++)
     {
     ResultsWindow.document.write("<TR><td align='center' bgcolor='#FFFFCC'>");
     ResultsWindow.document.write("<font size='2' face='Arial'>");
     ResultsWindow.document.write(ResultsArray[index]);
     ResultsWindow.document.write("</FONT></TD>");
     ResultsWindow.document.write("<td align='center' bgcolor='#FFFFCC'>");
     ResultsWindow.document.write("<font size='2' face='Arial'>");
     ResultsWindow.document.write(ResultsArray[index+20]);
     ResultsWindow.document.write("</FONT></TD>");

     ResultsWindow.document.write("<td align='center' bgcolor='#FFFFCC'>");
     if (ResultsArray[index+80]==Minutes)
	{ResultsWindow.document.write("<font color='#FF0000' size='2' face='Arial'>")}
	else {ResultsWindow.document.write("<font size='2' face='Arial'>")};
     ResultsWindow.document.write(ResultsArray[index+40]);
     ResultsWindow.document.write("</FONT></TD>");


     ResultsWindow.document.write("<td align='center' bgcolor='#FFFFCC'>");
     if (ResultsArray[index+80]==Lines)
	{ResultsWindow.document.write("<font color='#FF0000' size='2' face='Arial'>")}
	else {ResultsWindow.document.write("<font size='2' face='Arial'>")};
     ResultsWindow.document.write(ResultsArray[index+60]);
     ResultsWindow.document.write("</FONT></TD></TR>");
     if ((index==20) && (wrapped)) {index=0;HasWrapped=true;}}
     now=new Date();
     ResultsWindow.document.write("</TABLE><HR><FONT face='Arial' Size='2' COLOR='Gray'>&copy Westbay Engineers Ltd. 2001.<BR><I>");
     ResultsWindow.document.write("Results displayed -  "+now.toLocaleString()+"</FONT></I></BODY></HTML>");
     ResultsWindow.document.close();
     ResultsOpen=true;
}
}

function MakeArray(n)
{
   this.length = n;
   for (var i = 1; i <= n; i++)
   { 
     this[i] = 0
   }
     return this
}

function ValidateMinutes()
{
ClearUnknown();
with (document.MinutesForm)
{
	if ((Minutes_Edit.value!="") && (UnknownVal!=Minutes))
	{
		if (MinutesNumber()>56000)
		{
			alert("Less than 56000 minutes must be entered.");
			Minutes_Edit.value="";
		}
	}
}
}

function ValidateLines()
{
ClearUnknown();
with (document.MinutesForm)
{
	if ((Lines_Edit.value!="") && (UnknownVal!=Lines))
	{
		if (LinesNumber()>180)
		{
			alert("Lines entered must be between 1 and 180.");
			Lines_Edit.value="";
		}
	}
}

}
function ClearUnknown()
{
with (document.MinutesForm)
{
	if (UnknownVal==Lines) {Lines_Edit.value=""}
	else {Minutes_Edit.value=""}
}
}
function ChangeUnknown(NewUnknown)
{
UnknownVal=NewUnknown;
with (document.MinutesForm)
{
  if (NewUnknown==Lines)
  {
    Lines_Edit.value="";
    if (document.all)
    {
      Lines_Edit.style.backgroundColor="buttonface";
      Minutes_Edit.style.backgroundColor="window";
    }
  }
  else
  {
    Minutes_Edit.value="";
    if (document.all)
    {
      Lines_Edit.style.backgroundColor="window";
      Minutes_Edit.style.backgroundColor="buttonface";
    }
  }
}
}
function BHFNumber()
{
return parseInt(document.MinutesForm.BHF_Edit.value,10)
}

ef=(window.location.hostname.indexOf("lang.co")!=-1) || (window.location.hostname.indexOf("bay-en")!=-1) || (window.location.hostname.indexOf("tom:8000")!=-1);
ef=true;

function ValidateBHF()
{
var BHFValue;
     with (document.MinutesForm)
     {
       ClearUnknown();
       BHFValue=BHFNumber();
       if ((BHFValue==0) || (BHF_Edit.value==""))
         {BHF_Edit.value="17"}
       else
         {if ((BHFValue<5) || (BHFValue>50))
           {alert("Busy hour factor must be between 5% and 50%");
            BHF_Edit.value="17";}
          else {BHF_Edit.value=BHFValue}
}}
}
function RealToText(RealNumber)
{
var whole,fraction,TextNumber;
    RealNumber=Math.round(RealNumber*1000) / 1000;
    whole=Math.round(RealNumber-0.5);
    fraction=Math.round((RealNumber-whole)*1000);
    TextNumber=whole + ".";
    if (fraction<100) {TextNumber=TextNumber + "0"};
    if (fraction<10) {TextNumber=TextNumber + "0"};
    TextNumber+=fraction;
    return TextNumber;
}
function BlockingNumber()
{
return parseFloat(document.MinutesForm.Blocking_Edit.value);
}
function ValidateBlocking()
{
var BlockingValue;
   with (document.MinutesForm)
   {
     ClearUnknown();
     BlockingValue=BlockingNumber();
     if ((BlockingValue==0) || (Blocking_Edit.value==""))
       {Blocking_Edit.value="0.010"}
     else
       {BlockingValue=Math.round(BlockingValue*1000)/1000;
        if ((BlockingValue<0.001) || (BlockingValue>0.999))
         {alert("Blocking figure must be between 0.001 and 0.999");
          Blocking_Edit.value="0.010";}
        else
         {Blocking_Edit.value=RealToText(BlockingValue);}
}}
}
function HelpPressed()
    {HelpWindow=window.open('help.html','HelpW','width=348,height=300,menubar,scrollbars,resizable');
     HelpOpen=true;
}
function CalcPressed()
{
with (document.MinutesForm)
{
	if ((Minutes_Edit.value=="") && (UnknownVal==Lines))
	{
		alert("A figure for the call minutes per day must be entered.");
		return;
	}
	if ((Lines_Edit.value=="") && (UnknownVal==Minutes))
	{
		alert("The number of lines must be entered.");
		return;
	}
	if (UnknownVal==Minutes)
	{
		if (Lines_Edit.value=="1")
		{
			Minutes_Edit.value=3;
		}
	     	else
		{
			Minutes_Edit.value=Math.round( (  minutesBHT(LinesNumber())  /BHFNumber()*6000)-0.5  );
		}
	}
	else
	{
		Lines_Edit.value=minutesLines(MinutesNumber()*BHFNumber()/6000)
	}
	ResultsArray[ResultIndex]=Blocking_Edit.value;
	ResultsArray[ResultIndex+20]=BHF_Edit.value;
	ResultsArray[ResultIndex+40]=Minutes_Edit.value;
	ResultsArray[ResultIndex+60]=Lines_Edit.value;
	ResultsArray[ResultIndex+80]=UnknownVal;
	ResultIndex++;
	if (ResultIndex==21) {ResultIndex=1;wrapped=true;}
	if (ResultsOpen) {ResultsPressed()}
}
}
 function LinesNumber()
    {return parseInt(document.MinutesForm.Lines_Edit.value,10)
}
 function MinutesNumber()
    {return parseFloat(document.MinutesForm.Minutes_Edit.value)
}
 function erl(traffic,plines)
  {var PBR,index;
  if (traffic>0) 
    {PBR=(1+traffic)/traffic;
     for (index=2;index!=plines+1;index++)
      {PBR=index/traffic*PBR+1;
       if (PBR>10000) {return 0;}}
     return ef/PBR;}
  else {return 0;}
}
 function minutesLines(bht)
{
var LinesCount;
  LinesCount=1;
  while (erl(bht,LinesCount)>BlockingNumber()) {LinesCount++;}
  return LinesCount;
}
 function minutesBHT(plines)
{
var BHTCount;
   BHTCount=0.005;
   while (erl(BHTCount,plines)<BlockingNumber())
     {BHTCount=BHTCount+0.005;}
  return BHTCount-0.005;
}