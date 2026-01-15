// Javascript date picker from Accommodation Direct
// v1.0 - April 2009
// By Oliver Bryant
// Feel free to use and distribute.  Link back to www.CapeTown-Direct.com if you find it useful!

cl_n=1;
_nc=18;
if (typeof caldaystart=='undefined')
	caldaystart=0;

// calformat = 1 : dd/mm/yyyy
// calformat = 2 : yyyy-mm-dd
if (typeof calformat=='undefined')
	calformat=1;
else {
	if (calformat=='dd/mm/yyyy')
		calformat=1;
	if (calformat=='yyyy-mm-dd')
		calformat=2;
	}
	

function doscroll(ww) {
	doscrollhelper(ww,0);
}
s_i=10;
function doscrollhelper(ww,_scr) {
	if (_scr<210) {
		_scr+=s_i;
		if (ww=='l')
			g_o('fc_all').scrollLeft=g_o('fc_all').scrollLeft+s_i;
		else
			g_o('fc_all').scrollLeft=g_o('fc_all').scrollLeft-s_i;
			
		setTimeout('doscrollhelper(\'' + ww + '\',' + _scr + ')',10);	
	}
	else {
		if (ww=='l')
			g_o('fc_all').scrollLeft=g_o('fc_all').scrollLeft+1;
		else
			g_o('fc_all').scrollLeft=g_o('fc_all').scrollLeft-1;	
	}
}

function g_o(objID)
{
    if (document.getElementById) {return document.getElementById(objID);}
    else if (document.all) {return document.all[objID];}
    else if (document.layers) {return document.layers[objID];}
}

function checkClick(e) {
	e?evt=e:evt=event;
	CSE=evt.target?evt.target:evt.srcElement;
	musthide=true;
	for(var tj=0;tj<_nc;tj++) {
		if (g_o('fc_'+tj))
				if (isChild(CSE,g_o('fc_'+tj))) {
					musthide=false;
					break;
				}
	}
	
	if (isChild(CSE,g_o('b_a'))|isChild(CSE,g_o('b_s')))
		musthide=false;
	
	if (musthide) {
		if (g_o('fc_all'))
			g_o('fc_all').style.visibility='hidden';
		g_o('b_a').style.visibility='hidden';
		g_o('b_s').style.visibility='hidden';
		hss(1,'');
	}
}

var _sl=new Array(_nc);

function isChild(s,d) {
	while(s) {
		if (s==d) 
			return true;
		s=s.parentNode;
	}
	return false;
}

function Left(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function Top(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

var css_calmain=verifyStyle('.calmain');
var css_calheader=verifyStyle('.calheader');
var css_caldayheader=verifyStyle('.caldayheader');
var css_caldaynormal=verifyStyle('.caldaynormal');

function drawcalendar() {
	if (css_calmain)
		caltext='<div class="calmain" style="float:left;width:211px" id="fc_' + cl_n + '"><div style="border-right:1px solid #DEDEDE">';
	else
		caltext='<div style="float:left;width:211px;" id="fc_' + cl_n + '"><div style="border-right:1px solid #DEDEDE;background:#FFFFFF">';
		
	caltext+='<div style="cursor:pointer;float:left;width:17px"></div>';
	caltext+='<div style="cursor:pointer;float:right;width:17px"></div>';
	
	if (css_calheader)
		caltext+='<div class="calheader" id="mns_' + cl_n + '"></div>';
	else
		caltext+='<div style="font:bold 14px/18px Arial;text-align:center;color:#FFFFFF;background:#62895d;padding:5px" id="mns_' + cl_n + '"></div>';		 	

	caltext+='<div class="dvdt"></div>';
	
	for(var i=0;i<7;i++)
		if (css_caldayheader)
			caltext+='<div class="caldayheader">' + _dow[(i+caldaystart)%7] + '</div>';
		else
			caltext+='<div style="font:bold 14px/25px Arial;color:#9abb96;background:#FFFFFF;width:30px;float:left;text-align:center;">' + _dow[(i+caldaystart)%7] + '</div>';
	
	caltext+='<div class="dvdt"></div>';
	for(var kk=1;kk<=6;kk++) {
		for(var tt=1;tt<=7;tt++) {
			num=7 * (kk-1) - (-tt);
			if (css_caldaynormal) 
				caltext+='<div style="width:30px;height:25px;float:left;text-align:center"><div class"caldaynormal" id="b' + num + '_' + cl_n + '" ></div></div>';
			else
				caltext+='<div style="width:30px;height:25px;float:left;text-align:center"><div style="font:bold 11px/25px Arial;background:#FFFFFF;color:#444444;text-decoration:none;text-align:center;width:30px;height:25px" id="b' + num + '_' + cl_n + '" ></div></div>';
		}
		caltext+='<div class="dvdt"></div>';
	}
	caltext+='</div></div>';
	cl_n++;
	
	return caltext;
}


// Draw 3 calendars
function drawcalendars() {
	mydiv=document.createElement('div');
	mydiv.id='fc_all';
	mydiv.style.visibility='hidden';
	mydiv.style.position='absolute';
	mydiv.style.width='420px';
	mydiv.style.border='1px solid #62895d';
	mydiv.style.zIndex=500;
	//mydiv.style.background='#FF6600';
	mydiv.style.overflow='hidden';
	document.body.appendChild(mydiv);	
	c_text='';
	for(var tj=1;tj<_nc;tj++)
		c_text+=drawcalendar();
	
	document.getElementById('fc_all').innerHTML='<div style="width:3587px">' + c_text + '</div>';
	
	
		
	for(var tj=1;tj<_nc;tj++) {
		document.all?g_o('fc_' + tj).attachEvent('onclick',cs_click):g_o('fc_' + tj).addEventListener('click',cs_click,false);
		document.all?g_o('fc_' + tj).attachEvent('onmouseover',cs_over):g_o('fc_' + tj).addEventListener('mouseover',cs_over,false);
		document.all?g_o('fc_' + tj).attachEvent('onmouseout',cs_out):g_o('fc_' + tj).addEventListener('mouseout',cs_out,false);
		prepcalendar('',ccm,ccy,tj);
		}
}

document.all?window.attachEvent('onload',drawcalendars):window.addEventListener('load',drawcalendars,false);

// Calendar buttons
document.write('<div id="b_s" style="z-index:1000;position:absolute;visibility:hidden;cursor:pointer;float:left;width:20px;height:21px" onclick="csubm()"></div>');
document.write('<div id="b_a" style="z-index:1000;position:absolute;visibility:hidden;cursor:pointer;float:right;width:20px;height:21px" onclick="caddm()"></div>');

document.all?document.attachEvent('onclick',checkClick):document.addEventListener('click',checkClick,false);


// Calendar script
var now = new Date;
var sccm=now.getMonth();
var sccy=now.getFullYear();
var ccm=now.getMonth();
var ccy=now.getFullYear();

function positioncals() {
	hss(1,'');
	// Decide whether to place above or below input box
	// Check y-coord of bottom of calendar
	if ((getWinHeight()+getScrollTop()) < (Top(updobj)+updobj.offsetHeight+g_o('fc_all').offsetHeight)) {
		g_o('fc_all').style.top=Top(updobj)-g_o('fc_all').offsetHeight+'px';
		hss(0,'above');
		}
	else {
		g_o('fc_all').style.top=Top(updobj)+updobj.offsetHeight+'px';
		hss(0,'below');
		}
		
	if ((getWinWidth()+getScrollLeft()) < (Left(updobj)+g_o('fc_all').offsetWidth))
		g_o('fc_all').style.left=Left(updobj)-g_o('fc_all').offsetWidth+updobj.offsetWidth+'px';		
	else
		g_o('fc_all').style.left=Left(updobj)+'px';
	
	
	g_o('b_s').style.left=Left(g_o('fc_all'))+4+'px';
	g_o('b_s').style.top=Top(g_o('fc_all'))+6+'px';
	g_o('b_a').style.left=Left(g_o('fc_all'))+g_o('fc_all').offsetWidth-g_o('b_s').offsetWidth-1+'px';
	g_o('b_a').style.top=Top(g_o('fc_all'))+6+'px';
	
	if (_calcurrent==0)
		g_o('b_s').style.visibility='hidden';
	else
		g_o('b_s').style.visibility='visible';
	if (_calcurrent==(_nc-3))
		g_o('b_a').style.visibility='hidden';
	else
		g_o('b_a').style.visibility='visible';	
	
	g_o('fc_all').style.visibility='visible';
	
}

function getDateFromCell(c_id) {
	nc_c=c_id.substring(c_id.indexOf('_')+1);
	c_index=c_id.substring(1,c_id.indexOf('_'));
	return(calvalarr[nc_c][c_index]);
}

var updobj;
var pchid='';

_calcurrent='';

function lcs(ielem) {
	updobj=ielem;
	if (document.getElementById('fc_all')) {		
		if (pchid!='')
			f_cps(g_o(pchid));
		
		//curioustext='';
		if (ielem.value!='')
			for(var tjk=1;tjk<_nc;tjk++) {
				for(var tj=0;tj<=42;tj++) {
					//curioustext+=tjk + ':' + tj +'<br>';
					if (calvalarr[tjk][tj]==ielem.value) {
						pchid='b' + tj + '_' + tjk;
						f_hds(g_o(pchid));
						
						//scroll calendar
						g_o('fc_all').scrollLeft=(tjk==(_nc-1))?(tjk-2)*211:(tjk-1)*211;
						
						tjk=_nc;
						break;
						}
				}
			}
		
		_calcurrent=parseInt(g_o('fc_all').scrollLeft/211);
		
		positioncals();
	}
	else { // If not loaded then keep delaying a call every 1 second
		setTimeout('lcs(updobj)',500);
	}
}

function evtTgt(e)
{
	var el;
	if(e.target)el=e.target;
	else if(e.srcElement)el=e.srcElement;
	if(el.nodeType==3)el=el.parentNode; // defeat Safari bug
	return el;
}
function EvtObj(e){if(!e)e=window.event;return e;}

function cs_over(e) {
	if (evtTgt(EvtObj(e)).id.substring(0,1)=='b') {
		EvtObj(e).cancelBubble=true;
		if (verifyStyle('.caldaymouseover'))
			evtTgt(EvtObj(e)).className='caldaymouseover';
		else {
			evtTgt(EvtObj(e)).style.background='#ffbf7a';
			evtTgt(EvtObj(e)).style.color='#FFFFFF';
			evtTgt(EvtObj(e)).style.textDecoration='underline';
			evtTgt(EvtObj(e)).style.cursor='pointer';
		}
	}
}

function cs_out(e) {
	if (evtTgt(EvtObj(e)).id.substring(0,1)=='b') {
		EvtObj(e).cancelBubble=true;
		if (verifyStyle('.caldaynormal'))
			evtTgt(EvtObj(e)).className='caldaynormal';
		else
			f_cps(evtTgt(EvtObj(e)));
	}
}
function cs_click(e) {
	if (evtTgt(EvtObj(e)).id.substring(0,1)=='b') {
		EvtObj(e).cancelBubble=true;
		updobj.value=getDateFromCell(evtTgt(EvtObj(e)).id);
		
		g_o('fc_all').style.visibility='hidden';
		g_o('b_s').style.visibility='hidden';
		g_o('b_a').style.visibility='hidden';
	
		hss(1,'');
	}
}

var _dow=new Array('S','M','T','W','T','F','S');
var _mn=new Array('January','February','March','April','May','June','July','Aug','September','October','November','December');
var _mnn=new Array('31','28','31','30','31','30','31','31','30','31','30','31');
var _mnl=new Array('31','29','31','30','31','30','31','31','30','31','30','31');
var calvalarr=new Array(_nc);
for(var tj=1;tj<_nc;tj++)
	calvalarr[tj]=new Array(42);

function f_cps(obj) {
	if (verifyStyle('.caldaynormal')) {
		obj.className='caldaynormal';	
		}
	else {
		obj.style.background='#FFFFFF';
		obj.style.color='#444444';
		obj.style.textDecoration='none';
		obj.style.textAlign='center';
		obj.style.width='30px';
		obj.style.height='25px';
	}
}

function f_cpps(obj) {
	if (verifyStyle('.caldaypast')) {
		obj.className='caldaypast';
		}
	else {
		obj.style.textDecoration='line-through';
		obj.style.color='#ABABAB';
	}
}

function f_hds(obj) {
	if (verifyStyle('.caldayselected'))
		obj.className='caldayselected';
	else {
		obj.style.color='#FFFFFF';
		obj.style.background='#ABABAB';
	}
}

// cn : calendar number
// prepcalendar handles updating the nth calendar with appropriate values
var _now=new Date();
var _sd=_now.getDate();
function prepcalendar(hd,in_cm,in_cy,cn) {
	cm=in_cm;
	cy=in_cy;
	// Rest of routine works correctly assuming correct cm and cy
	cm=in_cm+(cn-1);
	if (cm < 0) {
		cm=12+cm;
		cy=cy-1;
	}
	if (cm > 11) {
		cm=cm-12;
		cy=cy-(-1);
	}
	
	td=new Date();
	td.setDate(1);
	td.setFullYear(cy);
	td.setMonth(cm);
	cd=td.getDay();
	
	if (cd < caldaystart)
		cd+=7;
		
	g_o('mns_' + cn).innerHTML=_mn[cm]+ ' ' + cy;
	marr=((cy%4)==0)?_mnl:_mnn;
	for(var d=1;d<=42;d++) {
		if (((d+caldaystart) >= (cd -(-1))) && ((d+caldaystart)<=cd-(-marr[cm]))) {
			dip=((d-cd+caldaystart < _sd)&&(cm==sccm)&&(cy==sccy));
			
			if (dip) {
				f_cpps(g_o('b'+d+ '_' + cn));
				g_o('b'+d+ '_' + cn).onmouseover=stub;
				g_o('b'+d+ '_' + cn).onmouseout=stub;
				g_o('b'+d+ '_' + cn).onclick=stub;
			}

			g_o('b'+d+ '_' + cn).innerHTML=d-cd+caldaystart;
			dd=rightstr('0'+(d-cd+caldaystart),2);
			mm=rightstr('0'+(cm-(-1)),2);
			
				calvalarr[cn][d]=''+cy+'-'+mm+'-'+dd;
		
		}
		else {
			g_o('b'+d+ '_' + cn).onmouseover=stub;
			g_o('b'+d+ '_' + cn).onmouseout=stub;
			g_o('b'+d+ '_' + cn).onclick=stub; }
	}
}

function stub(e) {
	EvtObj(e).cancelBubble=true;
}

function rightstr(str, n){
    if (n <= 0)
       return "";
    else if (n > String(str).length)
       return str;
    else {
       var iLen = String(str).length;
       return String(str).substring(iLen, iLen - n);
    }
}

function caddm() {
	if (_calcurrent < (_nc-3)) {
		_calcurrent++;
		doscroll('l');
		g_o('b_s').style.visibility='visible';
		if (_calcurrent==(_nc-3))
			g_o('b_a').style.visibility='hidden';	
	}
	
}

function csubm() {
	if (_calcurrent>0) {
		_calcurrent--;
		doscroll('r');
		g_o('b_a').style.visibility='visible';
		if (_calcurrent==0)
			g_o('b_s').style.visibility='hidden';			
	}
}

function hss(o,op) {
	// Hide custom select list above or below
	if ((op=='above')|(op==''))
		if (typeof selectListHideAbove!='undefined') {
			_hsaa=selectListHideAbove.split(',');
			for(tt=0;tt<_hsaa.length;tt++) {
				if (g_o(_hsaa[tt]))
					g_o(_hsaa[tt]).style.visibility=(o==0)?'hidden':'visible';
			}
		}
		
	if ((op=='below')|(op==''))
		if (typeof selectListHideBelow!='undefined') {
			_hsab=selectListHideBelow.split(',');
			for(tt=0;tt<_hsab.length;tt++) {
				if (g_o(_hsab[tt]))
					g_o(_hsab[tt]).style.visibility=(o==0)?'hidden':'visible';
			}
		}
}

function verifyStyle(selector) {
//    var rules;
//    var haveRule = false;
//    if (typeof document.styleSheets != "undefined") {   //is this supported
//        var cssSheets = document.styleSheets;
//        outerloop:
//        for (var i = 0; i < cssSheets.length; i++) {

             //using IE or FireFox/Standards Compliant
//            rules =  (typeof cssSheets[i].cssRules != "undefined") ? cssSheets[i].cssRules : cssSheets[i].rules;

//             for (var j = 0; j < rules.length; j++) {
//                 if (rules[j].selectorText == selector) {
//                         haveRule = true;
//                        break outerloop;
//                 }
//            }//innerloop

//        }//outer loop
//    }//endif
//    return haveRule;
return false;
}

function getWinHeight()
{
	if(window.innerHeight) return window.innerHeight;
	if(document.documentElement.clientHeight) return document.documentElement.clientHeight;
	if(document.body.clientHeight) return document.body.clientHeight;
	return 0;
}

function getWinWidth()
{
	if(window.innerWidth) return window.innerWidth;
	if(document.documentElement.clientWidth) return document.documentElement.clientWidth;
	if(document.body.clientWidth) return document.body.clientWidth;
	return 0;
}

function getScrollTop()
{
	if(document.documentElement.scrollTop) return document.documentElement.scrollTop;
	if(document.body.scrollTop) return document.body.scrollTop;
	if(window.pageYOffset) return window.pageYOffset;
	return 0;
}

function getScrollLeft()
{
	if(document.documentElement.scrollLeft) return document.documentElement.scrollLeft;
	if(document.body.scrollLeft) return document.body.scrollLeft;
	if(window.pageXOffset) return window.pageXOffset;
	return 0;
}
