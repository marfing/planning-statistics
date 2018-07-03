if (document.all)
{
  document.styleSheets[0].addRule(".RightAlign","text-align: right");
  document.styleSheets[0].addRule(".CalcTable","background-color: buttonface");
  document.styleSheets[0].addRule(".CalcCaptionBack","background-color: activecaption");
  document.styleSheets[0].addRule(".CalcCaptionText","color: captiontext");
  document.erlangb.Lines_Edit.style.backgroundColor="buttonface"
}

  BHT=1; blocking=2; lines=3;
  BHTValue=0; BlockingValue=0; LinesValue=0;
  unknownval=lines;
  ResultsOpen=false;
  HelpOpen=false;
  var ResultsArray=new MakeArray(80);
  ResultIndex=1;
  wrapped=false;
  function ChangeUnknown(NewUnknown)
{
    with (document.erlangb)
    {
      unknownval=NewUnknown;
      if (NewUnknown==BHT)
      {
        BHT_Edit.value="";
        if (document.all)
        {
          Blocking_Edit.style.backgroundColor="window";
          Lines_Edit.style.backgroundColor="window";
          BHT_Edit.style.backgroundColor="buttonface";
        }
      }
      if (NewUnknown==blocking)
      {
        Blocking_Edit.value="";
        if (document.all)
        {
          Blocking_Edit.style.backgroundColor="buttonface";
          Lines_Edit.style.backgroundColor="window";
          BHT_Edit.style.backgroundColor="window";
        }
      }
      if (NewUnknown==lines)
      {
        Lines_Edit.value="";
        if (document.all)
        {
          Blocking_Edit.style.backgroundColor="window";
          Lines_Edit.style.backgroundColor="buttonface";
          BHT_Edit.style.backgroundColor="window";
        }
      }
    }
  
}

function ValidateLines()
{
  var LinesValue;
  with (document.erlangb)
  {
    ClearUnknown();
    if ((Lines_Edit.value!="") && (unknownval !=lines))
    {
      LinesValue=LinesNumber()
      if (LinesValue==0)
      {
        Lines_Edit.value=""
      }
      else
      {
        if ((LinesValue<1) || (LinesValue>180))
        {
          alert("Lines figure must be between 1 and 180");
          Lines_Edit.value="";
        }
        else
        {
          Lines_Edit.value=LinesValue
        }
      }
    }
  }
}

function ValidateBHT()
{
  var BHTValue;
  with (document.erlangb)
  {
    ClearUnknown();
    if ((BHT_Edit.value!="") && (unknownval!=BHT))
    {
      BHTValue=BHTNumber();
      if (BHTValue==0)
      {
        BHT_Edit.value=""
      }
      else
      {
        BHTValue=Math.round(BHTValue*1000)/1000;
        if ((BHTValue<0.1) || (BHTValue>180))
        {
          alert("Busy Hour traffic figure must be between 0.1 and 180");
          BHT_Edit.value="";
        }
        else 
        {
          BHT_Edit.value=RealToText(BHTValue);
        }
      }
    }
  }
}
 function ValidateBlocking()
{
var BlockingValue;
     with (document.erlangb)
     {ClearUnknown();
     if ((Blocking_Edit.value!="") && (unknownval!=blocking))
      {BlockingValue=BlockingNumber();
       if (BlockingValue==0)
         {Blocking_Edit.value=""}
       else
         {BlockingValue=Math.round(BlockingValue*1000)/1000;
          if ((BlockingValue<0.001) || (BlockingValue>0.999))
           {alert("Blocking figure must be between 0.001 and 0.999");
            Blocking_Edit.value="0.010";}
          else
           {Blocking_Edit.value=RealToText(BlockingValue);}
}}}
}
 function ResultsPressed()
{
var index,HasWrapped;
     if ((ResultIndex==1) && (!wrapped)) {alert("There are no results to display yet!")}
    else {

//   Specifying size sometimes causes problems in IE4
//   ResultsWindow=window.open('','ResultsW','height=250,width=348,menubar,scrollbars,resizable');

     ResultsWindow=window.open("" , "ResultsW" , "toolbar=0 , location=0 , directories=0 , status=0 , menubar=1 , scrollbars=1 , resizable=1 , copyhistory=0");

     ResultsWindow.document.write("<HTML><HEAD><TITL"+"E>Erlang B Calculator</T"+"ITLE></HEAD>");
     ResultsWindow.document.write("<BODY BGCOLOR='#ffffff' LINK='#000080' VLINK='#008000'><P><font color='#000080' size='3' face='Arial'><strong>Erlang B Results Table</strong></font></P>")
     ResultsWindow.document.write("<P><font size='2' face='Arial'><strong>Here are the results (max 20) of the ");
     ResultsWindow.document.write("Erlang B Calculator. The unknown figures are shown in red.</strong></font></p>");
     ResultsWindow.document.write("<P><table border='1' cellpadding='2' cellspacing='0' width='300'><TR><td align='center' bgcolor='#003399'><font size='2'");
     ResultsWindow.document.write("face='Arial' color='#FFFFFF'><strong>B.H.T.</strong></font></td>");
     ResultsWindow.document.write("<td align='center' bgcolor='#003399'><font size='2'");
     ResultsWindow.document.write("face='Arial' color='#FFFFFF'><strong>Blocking</strong></font></td>");
     ResultsWindow.document.write("<td align='center' bgcolor='#003399'><font size='2'");
     ResultsWindow.document.write("face='Arial' color='#FFFFFF'><strong>Lines</strong></font></td></TR>");
     HasWrapped=!wrapped;
     if (wrapped) {index=ResultIndex} else {index=1};
     for (;(index!=ResultIndex) || (!HasWrapped);index++)
     {
     ResultsWindow.document.write("<TR><td align='center' bgcolor='#FFFFCC'>");
     if (ResultsArray[index+60]==BHT)
	{ResultsWindow.document.write("<font color='#FF0000' size='2' face='Arial'>")}
	else {ResultsWindow.document.write("<font size='2' face='Arial'>")};
     ResultsWindow.document.write(ResultsArray[index]);
     ResultsWindow.document.write("</FONT></TD>");
     ResultsWindow.document.write("<td align='center' bgcolor='#FFFFCC'>");
     if (ResultsArray[index+60]==blocking)
	{ResultsWindow.document.write("<font color='#FF0000' size='2' face='Arial'>")}
	else {ResultsWindow.document.write("<font size='2' face='Arial'>")};
     ResultsWindow.document.write(ResultsArray[index+20]);
     ResultsWindow.document.write("</FONT></TD>");
     ResultsWindow.document.write("<td align='center' bgcolor='#FFFFCC'>");
     if (ResultsArray[index+60]==lines)
	{ResultsWindow.document.write("<font color='#FF0000' size='2' face='Arial'>")}
	else {ResultsWindow.document.write("<font size='2' face='Arial'>")};
     ResultsWindow.document.write(ResultsArray[index+40]);
     ResultsWindow.document.write("</FONT></TD></TR>");
     if ((index==20) && (wrapped)) {index=0;HasWrapped=true;}}
     now=new Date();
     ResultsWindow.document.write("</TABLE><HR><FONT face='Arial' Size='2' COLOR='Gray'>&copy Westbay Engineers Ltd. 2001.<BR><I>");
     ResultsWindow.document.write("Results displayed -  "+now.toLocaleString()+"</FONT></I></BODY></HTML>");
     ResultsWindow.document.close();
     ResultsOpen=true;
}
}
 function ClearUnknown()
    {with (document.erlangb)
     {if (unknownval==BHT) {BHT_Edit.value=""}
     if (unknownval==blocking) {Blocking_Edit.value=""}
     if (unknownval==lines) {Lines_Edit.value=""}
}}
 function EmptyBHT()
    {if (document.erlangb.BHT_Edit.value=="") {return true}
     else {return false}
}
 function EmptyBlocking()
    {if (document.erlangb.Blocking_Edit.value=="") {return true}
     else {return false}
}
 function EmptyLines()
    {if (document.erlangb.Lines_Edit.value=="") {return true}
     else {return false}
}
  ef=(window.location.hostname.indexOf("lang.co")!=-1) || (window.location.hostname.indexOf("bay-en")!=-1);
  ef=true;
  function HelpPressed()
{
HelpWindow=window.open('help.html','HelpW','height=300,width=348,menubar,scrollbars,resizable');
     HelpOpen=true;
}
 function CalcPressed()
    {var error=false;
     with (document.erlangb) {if (unknownval==blocking)
        {if (EmptyLines() || EmptyBHT()) {alert("Error - The BHT and Lines fields must be filled in.");error=true;}
     else {Blocking_Edit.value=RealToText(ErlangB(BHTNumber(),LinesNumber()))}}
    if (unknownval==lines)
        {if (EmptyBHT() || EmptyBlocking()) {alert("Error - The BHT and Blocking fields must be filled in.");error=true;}
     else {Lines_Edit.value=ErlangBLines(BHTNumber(),BlockingNumber())}}
    if (unknownval==BHT)
        {if (EmptyLines() || EmptyBlocking()) {alert("Error - The Blocking and Lines fields must be filled in.");error=true;}
     else {BHT_Edit.value=RealToText(ErlangBBHT(BlockingNumber(),LinesNumber()))}}
    if (!error)
    {ResultsArray[ResultIndex]=RealToText(BHT_Edit.value);
    ResultsArray[ResultIndex+20]=RealToText(Blocking_Edit.value);
    ResultsArray[ResultIndex+40]=Lines_Edit.value;
    ResultsArray[ResultIndex+60]=unknownval;
    ResultIndex++;
    if (ResultIndex==21) {ResultIndex=1;wrapped=true;}
    if (ResultsOpen) {ResultsPressed()}}
}}
 function RealToText(RealNumber)
    {var whole,fraction,TextNumber;
    RealNumber=Math.round(RealNumber*1000) / 1000;
    whole=Math.round(RealNumber-0.5);
    fraction=Math.round((RealNumber-whole)*1000);
    TextNumber=whole + ".";
    if (fraction<100) {TextNumber=TextNumber + "0"};
    if (fraction<10) {TextNumber=TextNumber + "0"};
    TextNumber+=fraction;
    return TextNumber;
}
 function BHTNumber()
    {return parseFloat(document.erlangb.BHT_Edit.value)
}
 function LinesNumber()
    {return parseInt(document.erlangb.Lines_Edit.value,10)
}
 function BlockingNumber()
    {return parseFloat(document.erlangb.Blocking_Edit.value)
}
 function ErlangB(traffic,plines)
  {var PBR,index;
  if (traffic>0) 
    {PBR=(1+traffic)/traffic;
     for (index=2;index!=plines+1;index++)
      {PBR=index/traffic*PBR+1;
       if (PBR>10000) {return 0;}}
     return ef/PBR;}
  else {return 0;}
}
 function ErlangBLines(bht,MaximumBlocking)
 {var LinesCount;
  LinesCount=1;
  while (ErlangB(bht,LinesCount)>MaximumBlocking) {LinesCount++;}
  return LinesCount;
}
 function ErlangBBHT(MaximumBlocking,plines)
 {var BHTCount;
   BHTCount=0.05;
   while (ErlangB(BHTCount,plines)<MaximumBlocking)
     {BHTCount=BHTCount+0.05;}
  return BHTCount-0.05;
}
 function MakeArray(n) {
   this.length = n;
   for (var i = 1; i <= n; i++) { 
     this[i] = 0 }
     return this
}
// 
 
