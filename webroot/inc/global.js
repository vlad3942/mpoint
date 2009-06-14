/**
 * @fileoverview	This files holds miscelanous global functions which are used by several of the AJAX Client APIs.
 					Its functions include factories for producing XML HTTP and XSL Transformation objects as well as
 					a global report function.
 * @author Jonatan Evald Buus
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Defines the debug mode of the package, this makes the Client ouput messages to the tag with id="debug"
 *
 * 1 - Output Errors
 * 2 - Output Warnings
 * 3 - Output Errors and Warnings
 * 4 - Output Notices
 * 5 - Output Errors and Notices
 * 6 - Output Warnings and Notices
 * 7 - Output Errors, Warnings and Notices
 * 8 - Output Debug Info
 * 9 - Output Errors and Debug Info
 * 10 - Output Warnings and Debug Info
 * 11 - Output Errors, Warnings and Debug Info
 * 12 - Output Notices and Debug Info
 * 13 - Output Errors, Notices and Debug Info
 * 14 - Output Warnings, Notices and Debug Info
 * 15 - Output Errors, Warnings, Notices and Debug Info
 *
 * @type integer
 */
var iDEBUG_MODE = 3;

/**
 * Factory for producing an XML HTTP object
 * The function returns an Active X Object for MSIE 5.0+
 *
 * @return	HTTP Object on Success, false on Error
 * @type 	HTTP Object
 */
function httpProducer()
{
	var obj_HTTP = null;
	// XML HTTP Request present
    if ( (typeof window.XMLHttpRequest) != "undefined")
    {
        obj_HTTP = new XMLHttpRequest();
    }
    // Microsoft Internet Explorer
    else if (window.ActiveXObject != false)
    {
        // There are several versions of IE's Active X control, use the most recent one available
        var xmlVersions = new Array("MSXML2.XMLHttp.6.0",
        							"MSXML2.XMLHttp.5.0",
        							"MSXML2.XMLHttp.4.0",
        							"MSXML2.XMLHttp.3.0",
        							"MSXML2.XMLHttp",
        							"Microsoft.XMLHTTP");
		// Attempt to instantiate Active X object
        for (var i=0; i<xmlVersions.length; i++)
        {
        	// Attempt to initialise Active X object
            try
            {
                var obj_HTTP = new ActiveXObject(xmlVersions[i]);   
            }
            catch (e)
            {
                // Ignore - continue looping
            }
        }
    }
    // XML HTTP not supported
    else
    {
    	obj_HTTP = false;
    }

    return obj_HTTP;
}

/**
 * Factory for producing an XSL Transformation object
 * The function returns an Active X Object for MSIE 5.0+
 *
 * @return	XSLT Object on Success, false on Error
 * @type 	HTTP Object
 */
function xsltProducer()
{
	var obj_XSLT = null;
	
	// XML Serializer present
	if ( (typeof window.XMLSerializer) != "undefined")
	{
		obj_XSLT = new XSLTProcessor();
	}
	// Microsoft Internet Explorer
    else if (window.ActiveXObject != false)
	{
		
	}
	 // XSL Transformation not supported
    else
    {
    	obj_XSLT = false;
    }
	
	return obj_XSLT;
}

function domProducer(xml)
{
	// Load XML into DOM Document
	try
	{
		// Firefox, Mozilla, Opera, etc.
		var obj_XML = new DOMParser().parseFromString(xml, "text/xml");
	}
	catch (e)
	{
		try
		{
		// There are several versions of IE's Active X control, use the most recent one available
	        var xmlVersions = new Array("MSXML2.DOMDocument.5.0",
	        							"MSXML2.DOMDocument.4.0",
	        							"MSXML2.DOMDocument.3.0",
	        							"MSXML2.DOMDocument",
	        							"Microsoft.XmlDom");
			// Attempt to instantiate Active X object
	        for (var i=0; i<xmlVersions.length; i++)
	        {
	        	// Attempt to initialise Active X object
	            try
	            {
	                var obj_XML = new ActiveXObject(xmlVersions[i]);   
	            }
	            catch (e)
	            {
	                // Ignore - continue looping
	            }
	        }
			obj_XML.async = false;
			obj_XML.resolveExternals = true;
			obj_XML.setProperty("AllowDocumentFunction", true);
			obj_XML.setProperty("AllowXsltScript", true);
			obj_XML.loadXML(xml);
		}
		catch (e)
		{
			report(1, "Unable to instantiate DOM Parser for Browser");
		}
	}
	
	return obj_XML;
}

/**
 * Global function for writing output for error or debug purposes.
 * The method can be called with a code identifying the type of report:
 * 1 - Error
 * 2 - Warning
 * 4 - Notice
 * 8 - Debug
 * The method uses the global variable: iDEBUG_MODE to determine whether a report should be displayed or not
 * Please note, for the function to work, the page MUST contain an element with id="debug"
 */
function report(code, msg)
{
	try
	{
		if ( (iDEBUG_MODE&code) == code)
		{
			if (document.getElementById("debug").innerHTML != "") { document.getElementById("debug").innerHTML += "<hr />"; }
			switch (code)
			{
			case (1):
				document.getElementById("debug").innerHTML += "ERROR - ";
				break;
			case (2):
				document.getElementById("debug").innerHTML += "WARNING - ";
				break;
			case (4):
				document.getElementById("debug").innerHTML += "NOTICE - ";
				break;
			case (8):
				document.getElementById("debug").innerHTML += "DEBUG - ";
				break;
			default:
				document.getElementById("debug").innerHTML += "UNKNOWN - ";
				break;
			}
			msg = msg.replace(/</g, "&lt;");
			msg = msg.replace(/>/g, "&gt;");
			msg = msg.replace(/&/g, "&amp;");
			msg = msg.replace(/\n/g, "<br />");
			
			document.getElementById("debug").innerHTML += msg;
		}
	}
	catch (e)
	{
		alert("Code: "+ code +" Message:"+ msg);
	}
}

/**
 * Recursively dumps all properties of an Object using a Confirm box
 * This function is intended to ease debugging of Javascript objects
 *
 * @param	{object} obj		Object to dump properties of
 * @param	{object} oParent	Object's parent, this is used for allowing recurssion if a property is in itself an object
 */
function dumpProps(obj, oParent)
{
	// Go through all the properties of the passed-in object
	for (var i in obj)
	{
		/**
		 * if a parent (2nd parameter) was passed in, then use that to
		 * build the message. Message includes i (the object's property name)
		 * then the object's property value on a new line
		 */
		if (oParent)
		{
			var msg = oParent + "." + i + "\n" + obj[i];
		}
		else
		{
			var msg = i + "\n" + obj[i];
		}
		/**
		 * Display the message. If the user clicks "OK", then continue. If they
		 * click "CANCEL" then quit this level of recursion
		 */
		if (confirm(msg) == false) { return; }

		// If this property (i) is an object, then recursively process the object
		if (typeof obj[i] == "object")
		{
			if (oParent) { dumpProps(obj[i], oParent + "." + i); } else { dumpProps(obj[i], i); }
		}
	}
}

/**
 * Indicates the currently selected menu option by changing the CSS Class of
 * the provided element and clearing the CSS class of all siblings
 *
 * @param	{object} obj 	Menu Option Element where user just clicked
 * @param	{string} css 	New CSS class for the currently selected menu option
 */
function selectMenu (obj, css)
{
	// Clear CSS class for all following siblings
	var oSibling = obj.parentNode.nextSibling;
	
	while (oSibling != null)
	{
		for (var i=0; i<oSibling.childNodes.length; i++)
		{
			oSibling.childNodes[i].className = '';
		}
		oSibling = oSibling.nextSibling;
	}
	
	// Clear CSS class for all previous siblings
	oSibling = obj.parentNode.previousSibling;
	while (oSibling != null)
	{
		for (var i=0; i<oSibling.childNodes.length; i++)
		{
			oSibling.childNodes[i].className = '';
		}
		oSibling = oSibling.previousSibling;
	}
	
	// Change CSS class for current menu option
	obj.className = css;
}

/**
 * Handles the population of a child dropdown depending on what option has been selected in the parent dropdown
 * Please note: In order to use it after data has been sent to the server, the function should be called onload
 *				and any PHP Session variables passed to it as input
 *
 * @param object oSel	Child dropdown Object
 * @param array aData 	Multi-Dimensional array of data to use for populating the Child dropdown
 * @param integer pid	Option just selected in the Parent dropdown
 * @param integer cid	Selected option in the Child dropdown
 */
function populateChild(oSel, aData, pid, cid)
{
	// Valid Selection made in Parent dropdown
	if(pid > 0 || pid != "")
	{
		// Clear Child dropdown
		oSel.options.length = 1;
		
		var i = 1;
		// Populate Child dropdown with values from data array depending on the selection in the Parent dropdown
		for (id in aData[pid])
		{
			oSel.options[i] = new Option(aData[pid][id], id);
			// User previously selected current child option
			if(id == cid) { oSel.options[i].selected = true; }
			i++;
		}
		// Enabled Child dropdown
		oSel.disabled = false;
	}
	// Invalid or No selection made in Parent dropdown
	else
	{
		// Clear Child dropdown
		oSel.options.length = 1;
		// Disable Child dropdown
		oSel.disabled = true;
	}
}

function loadGraph(oForm, oGraph, type)
{
	var aProcessedTags = new Array();
	var sQueryString = "";
	
	var iSiteID = 0;
	
	try
	{
		for (var i=0; i<oForm.length; i++)
		{
			// Tag is valid
			if (oForm.elements[i].name != null && oForm.elements[i].name != "" && oForm.elements[i].name != "undefined")
			{
				// Tag has not been processed yet
				if (aProcessedTags[oForm.elements[i].name] == "undefined" || aProcessedTags[oForm.elements[i].name] == null)
				{
					var oElems = oForm[oForm.elements[i].name];
					/**
					 * Tag consists of multiple elements, i.e. a checkbox
					 * Create a container using the tag name and add each value as an item
					 */
					if (oElems.length > 1)
					{
						for (var n=0; n<oElems.length; n++)
						{
							if(oElems[n].checked == true || oElems[n].selected == true)
							{
								sQueryString += '%26'+ oForm.elements[i].name +'='+ oElems[n].value;
							}
						}
						aProcessedTags[oForm.elements[i].name] = oElems.length;
					}
					// Tag exists only once
					else if (oForm.elements[i].checked == true || oForm.elements[i].selected == true || oForm.elements[i].type != "checkbox")
					{
						sQueryString += '%26'+ oForm.elements[i].name +'='+ oForm.elements[i].value;
						if (oForm.elements[i].name == "site-id") { iSiteID = oForm.elements[i].name.value; }
						aProcessedTags[oForm.elements[i].name] = 1;
					}
				}
			}
		}
		sQueryString = sQueryString.substring(3);
	}
	catch (e)
	{
		// Ignore
	}
	
	var html = '';
	html = '<object id="charts" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" align="" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0">';
	html += '<param name="movie" value="../img/charts.swf?library_path=../img/charts_library&amp;xml_source=/'+ type +'/sys/get_stat.php?'+ sQueryString +'" />';
	html += '<param name="quality" value="high" />';
	html += '<param name="bgcolor" value="#fff" />';
	html += '<embed src="../img/charts.swf?library_path=../img/charts_library&amp;xml_source=/'+ type +'/sys/get_stat.php?'+ sQueryString +'"';
	html += ' quality="high"';
	html += ' bgcolor="#fff"';
	html += ' id="charts"';
	html += ' align=""';
	html += ' swLiveConnect="true"';
	html += ' type="application/x-shockwave-flash"';
	html += ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
	html += '</embed>';
	html += '</object>';
	
	oGraph.innerHTML = html;
}

function exportData(oForm, oGraph, type)
{
	var aProcessedTags = new Array();
	var sQueryString = "";
	
	var iSiteID = 0;
	
	try
	{
		for (var i=0; i<oForm.length; i++)
		{
			// Tag is valid
			if (oForm.elements[i].name != null && oForm.elements[i].name != "" && oForm.elements[i].name != "undefined")
			{
				// Tag has not been processed yet
				if (aProcessedTags[oForm.elements[i].name] == "undefined" || aProcessedTags[oForm.elements[i].name] == null)
				{
					var oElems = oForm[oForm.elements[i].name];
					/**
					 * Tag consists of multiple elements, i.e. a checkbox
					 * Create a container using the tag name and add each value as an item
					 */
					if (oElems.length > 1)
					{
						for (var n=0; n<oElems.length; n++)
						{
							if(oElems[n].checked == true || oElems[n].selected == true)
							{
								sQueryString += '%26'+ oForm.elements[i].name +'='+ oElems[n].value;
							}
						}
						aProcessedTags[oForm.elements[i].name] = oElems.length;
					}
					// Tag exists only once
					else if (oForm.elements[i].checked == true || oForm.elements[i].selected == true || oForm.elements[i].type != "checkbox")
					{
						sQueryString += '&'+ oForm.elements[i].name +'='+ oForm.elements[i].value;
						if (oForm.elements[i].name == "site-id") { iSiteID = oForm.elements[i].name.value; }
						aProcessedTags[oForm.elements[i].name] = 1;
					}
				}
			}
		}
		sQueryString = sQueryString.substring(1);
	}
	catch (e)
	{
		// Ignore
	}
	
	location.href = '/'+ type +'/sys/export_stat.php?'+ sQueryString;
}