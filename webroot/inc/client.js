/**
 * @fileoverview	This files holds general functionality for communicating with a Webserver using the HTTP Object
 *					Furthermore, it provided methods for handling the server response according to the different types
 *					of XML Based protcols:
 *					- Input Protocols
 *					- Form Protocols
 *					- Page Protocols
 *					- Command Protocols
 *					- Status Protocols
 *					- Re-Cache Protocols
 *
 * @link http://iemendo.cydev.biz/files/gui_protocols.pdf
 * @author Jonatan Evald Buus
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Constructer for creating a new Cache object
 *
 * @constructor
 *
 * @class		The Cache class handles general communication with the server using AJAX
 *				and allows the Client to hold several pages cached internally in memory by
 *				loading the XML Documents and corresponding XSL Stylesheets
 *
 * @param	{string} name		Name of Object that the class is instantiated as
 * @param	{string} xmlPath	Path to the XML Document on the server
 *								(optional)
 * @param	{string} xslPath	Path to the XSL Stylesheet on the server
 *								(optional)
 * @return	Instantiated object of class Cache
 * @type	Cache
 */
function Cache(name, xmlPath, xslPath)
{
	/**
	 * HTTP object used for fetching the documents from the server
	 *
	 * @private
	 *
	 * @type object
	 */
	this._obj_HTTP = null;
	
	/**
	 * Name of object the Class was instantiated as.
	 * This allows the class methods to easily use Javascript functions such as setTimeout and setInterval
	 *
	 * @private
	 *
	 * @type string
	 */
	this._sName = name;
	/**
	 * Path to the XML document on the server.
	 * This is generally a PHP file as most XML documents are written dynamically
	 *
	 * @private
	 *
 	 * @type string
	 */
	this._sXMLPath = xmlPath;
	/**
	 * Path to the XSL stylesheet on the server as defined in the XML document
	 *
	 * @private
	 *
 	 * @type string
	 */
	this._sXSLPath = xslPath;
	
	/**
	 * DOM Object with the fetched XML document
	 *
	 * @private
	 *
 	 * @type DOM Document
	 */
	this._obj_XML = null;
	/**
	 * The XML Document in string format, mainly intended for debug purposes
	 *
	 * @private
	 *
 	 * @type string
	 */
	this._xml = "";
	/**
	 * DOM Object with the fetched XSL stylesheet
	 *
	 * @private
	 *
 	 * @type DOM Document
	 */
	this._obj_XSL = null;
	/**
	 * The XSL Stylesheet in string format, mainly intended for debug purposes
	 *
	 * @private
	 *
 	 * @type string
	 */
	this._sXSL = "";
	
	/**
	 * Loads an XML Document or XSL Stylesheet from the server via HTTP
	 * The method performs an asychronious HTTP GET Request to the provided path
	 *
	 * @member Cache
	 *
	 * @param {string} path 	Path to Document on the Server
	 */
	Cache.prototype.load = function (path)
	{
		/*
		 * Synonym for the "this" pointer as the XML HTTP call back method cannot reference "this"
		 */
		var obj_This = this;
		
		/**
		 * Private callback method for handling a change to the state when loading the XML document
		 * The method will call the set method from class: Cache and pass the HTTP Object
		 *
		 * @member Cache
		 *
		 * @see Cache#set()
		 *
		 * @private
		 */
		var _callback = function ()
		{
			report(8, "Retrieving XML, state = "+ obj_This._obj_HTTP.readyState);
		    if (obj_This._obj_HTTP.readyState == 4)
		    {
		    	// Request succeeded
		        if (obj_This._obj_HTTP.status == 200)
		        {
		        	report(8, "XML Doc:"+ obj_This._obj_HTTP.responseText);
		        	obj_This.set(obj_This._obj_HTTP);
		        }
		        // Error: Problem retrieving XML data from server
		        else
		        {
		        	report(2, "Problem retrieving XML data: "+ obj_This._obj_HTTP.status +" from: "+ path);
		        }
		    }
		}
		
		obj_This._obj_HTTP = httpProducer();
		obj_This._obj_HTTP.onreadystatechange = _callback;
		obj_This._obj_HTTP.open("GET", path, true);
		obj_This._obj_HTTP.send("");
	}
	
	/**
	 * Sets the object's internal variables once the HTTP request has been completed
	 * If the interntal XML Document has not yet been set, the method assumes that the retrived data is an
	 * XML Document, it then attempts to find and load the corresponding XSL Stylesheet
	 * If the internal XML Document already has been set, the method assumes that the retrieved data is
	 * the documents corresponding XSL Stylesheet
	 *
	 * @member Cache
	 *
	 * @param {object} obj 	HTTP Object with the retrieved XML Document or XSL Stylesheet from the server
	 */
	Cache.prototype.set = function (obj)
	{
		if (this._obj_XML == null)
		{
			this._obj_XML = obj.responseXML;
			this._xml = obj.responseText
			if (this.xslPath == null) { this._sXSLPath = this.findXSL(this._obj_XML); }
			report(8, "XSL Path: "+ this._sXSLPath);
			if (this._sXSLPath != null && this._sXSLPath != "") { this.load(this._sXSLPath); }
		}
		else
		{
			this._obj_XSL = obj.responseXML;
			this._sXSL = obj.responseText
		}
	}
	
	/**
	 * Attempts to find the XSL Stylesheet defined the in XML document.
	 * If the stylesheet definition is found, the URL is saved in xslPath
	 *
	 * @member Cache
	 *
	 * @param {object} oXML 	DOM Object with the XML Document from which the XSL Stylesheet URL should be fetched
	 * @return string			Path to the XML Document's corresponding XSL Stylesheet on the Server
	 * @type string
	 */
	Cache.prototype.findXSL = function (oXML)
	{
		var path = "";
		// Parse XML document to find XSL stylesheet information
		for (var i=0; i<oXML.childNodes.length; i++)
		{
			if (oXML.childNodes.item(i).nodeType == 7 && oXML.childNodes.item(i).nodeName == "xml-stylesheet")
			{
				var aAttributes = oXML.childNodes.item(i).nodeValue.split(" ");
				
				for (var j=0; j<aAttributes.length; j++)
				{
					var a = aAttributes[j].split("=");
					if (a[0] == "href")
					{
						path = a[1].replace(/\"/g, "");
						j = aAttributes.length;
						i = oXML.childNodes.length;
					}
				}
			}
		}
		
		return path;
	}
	
	/**
	 * Returns the path on the Server to the XML Document
	 *
	 * @member 	Cache
	 *
	 * @return	string 	Path to the XML Document
	 * @type 	string
	 */
	Cache.prototype.getXMLPath = function () { return this._sXMLPath; }
	/**
	 * Returns the DOM Object for the XML Document from internal Memory
	 *
	 * @member 	Cache
	 *
	 * @return	XML Document with Data that was fetched from the Server
	 * @type 	DOM Document
	 */
	Cache.prototype.getXML = function () { return this._obj_XML; }
	/**
	 * Returns the DOM Object for the XSL Stylesheet from internal Memory
	 *
	 * @member 	Cache
	 *
	 * @return	XSL Stylesheet with transformation instructions that was fetched from the Server
	 * @type 	DOM Document
	 */
	Cache.prototype.getXSL = function () { return this._obj_XSL; }
}

/**
 * Constructer for creating a new Client object
 *
 * @constructor
 *
 * @class		The Client class handles general communication with the server using AJAX
 *				as well as transforms the XML document into XHTML using the XSL stylesheet
 *				provided in the XML header.
 *				All data is communicated with the server asynchroniously.
 *				When navigating between pages, the Client class instantiates objects of class: Cache
 *				to handle the HTTP Communication, these objects are then stored internally thereby caching pages
 *				and thus making the GUI more responsive.
 *
 * @requires	Cache 	Requires class: Cache for loading pages
 *
 * @param	{string} name	Name of Object that the class is instantiated as
 * @return	Instantiated object of class Client
 * @type	Client
 */
function Client(name)
{
	/**
	 * Name of object the Class was instantiated as.
	 * This allows the class methods to easily use Javascript functions such as setTimeout and setInterval
	 *
	 * @private
	 *
	 * @type 	string
	 */
	this._sName = name;
	
	/**
	 * Status message tag that should be kept between pages.
	 * The message will automatically be removed after 5 seconds
	 *
	 * @see 	Client#processReply
	 * @see 	Client#generatePage
	 * @see 	Client#clearMessages
	 *
 	 * @type 	string
	 */
	this.msg2keep = "";
	
	/**
	 * Array of Cache objects with chached pages.
	 * The content of this array is used for speeding up the GUI by pre-loading everything so
	 * the user doesn't have to wait for the network communication
	 *
	 * @see 	Client#changePage
	 * @see 	Client#recache
	 * @see 	Cache
	 *
	 * @private
	 *
 	 * @type 	array
	 */
	this._aCache = new Array();
	
	/**
	 * ID of the Server side session for the User
	 *
	 * @private
	 *
 	 * @type integer
	 */
	this._iSessionID = 0;
	
	/**
	 * Returns the internal array of cached pages from Memory
	 *
	 * @member 	Client
	 *
	 * @see 	Cache
	 *
	 * @return	Array of cache Objects currently held in memory
	 * @type 	array
	 */
	Client.prototype.getCache = function (url)
	{
		return url==null?this._aCache:this._aCache[url];
	}

	/**
	 * Encodes a string in accordance with the XML standard.
	 * That is: & becomes &amp; < becomes &lt; and > becomes &rt;
	 *
	 * @member 	Client
	 *
	 * @param	{string} str 	String to encode in accordance with the XML standard
	 * @return	Encoded string
	 * @type 	string
	 */
	Client.prototype.encode = function (str)
	{
		str = str.replace(/&/g, "&amp;");
		str = str.replace(/</g, "&lt;");
		str = str.replace(/>/g, "&gt;");
		
		return str;
	}
	
	/**
	 * Decodes a string in accordance with the XML standard.
	 * That is: &amp; becomes & &lt; becomes < and &rt; becomes >
	 *
	 * @member 	Client
	 *
	 * @param	{string} str 	String to decode in accordance with the XML standard
	 * @return	Decoded string
	 * @type 	string
	 */
	Client.prototype.decode = function (str)
	{
		str = str.replace(/&amp;/g, "&");
		str = str.replace(/&lt;/g, "<");
		str = str.replace(/&gt;/g, ">");
		
		return str;
	}
	
	/**
	 * Converts an input element to XML code that can be sent to the server
	 *
	 * @member 	Client
	 *
	 * @param	{object} oElem	Element object which should be converted to XML
	 * @return	XML code for the element
	 * @type 	string
	 */
	Client.prototype.elem2xml = function (oElem)
	{
		var xml = '<'+ oElem.name +'>';
		xml += this.encode(oElem.value);
		xml += '</'+ oElem.name +'>';
		
		return xml;
	}
	
	/**
	 * Converts an entire form and all its sub-element to XML code that can be sent to the server.
	 * The method uses the ID attribute from the form to identify the form to the server with the name attribute.
	 *
	 * @member 	Client
	 *
	 * @param	{object} oForm	Form object which should be converted to XML
	 * @return	XML code for the form
	 * @type 	string
	 */
	Client.prototype.form2xml = function (oForm)
	{
		var aProcessedTags = new Array();
		var xml = '<form name="'+ oForm.getAttribute("id") +'">';
		for (var i=0; i<oForm.length; i++)
		{
			// Tag is valid
			if (oForm.elements[i].name != null && oForm.elements[i].name != "" && oForm.elements[i].name != "undefined")
			{
				// Tag has not been processed yet
				if (aProcessedTags[oForm.elements[i].name] == "undefined" || aProcessedTags[oForm.elements[i].name] == null)
				{
					this.clear(oForm.elements[i].name);
					var oElems = oForm[oForm.elements[i].name];
					/**
					 * Tag consists of multiple elements, i.e. a checkbox
					 * Create a container using the tag name and add each value as an item
					 */
					if (oElems.length > 1)
					{
						xml += '<'+ oForm.elements[i].name +'>';
						for (var n=0; n<oElems.length; n++)
						{
							if (oElems[n].checked == true)
							{
								if ( oForm.elements[i].type == "radio")
								{
									xml += this.encode(oElems[n].value);
								}
								else
								{
									xml += '<item key="'+ n +'">';
									xml += this.encode(oElems[n].value);
									xml += '</item>';
								}
							}
							else if (oElems[n].selected == true)
							{
								xml += this.encode(oElems[n].value);
							}
						}
						xml += '</'+ oForm.elements[i].name +'>';
						aProcessedTags[oForm.elements[i].name] = oElems.length;
					}
					// Tag exists only once
					else if (oForm.elements[i].checked == true || oForm.elements[i].selected == true || oForm.elements[i].type != "checkbox")
					{
						xml += this.elem2xml(oForm.elements[i]);
						aProcessedTags[oForm.elements[i].name] = 1;
					}
				}
			}
		}
		xml += '</form>';
		
		return xml;
	}
	
	/**
	 * Sends data to the server for validation.
	 * If no callback method is provided, the send method will automatically call processReply.
	 * The Callback method will be passed the HTTP Object, thereby allowing it to retrieve any 
	 * response from the server.
	 *
	 * @member 	Client
	 *
	 * @see		Client#processReply
	 *
	 * @param	{string} path 	Path on server that XML document needs to be sent to
	 * @param	{string} xml 	XML document to send
	 * @param	string cb 		Callback to call when the XML document has been completely loaded
	 *							The callback can either be an array with an object in position 0 and method in position 1 or
	 *							a string with the function name
	 *							(optional)
	 */
	Client.prototype.send = function (path, xml, cb)
	{
		/*
		 * Synonym for the "this" pointer as the XML HTTP call back method cannot reference "this"
		 */
		var obj_This = this;
		
		var xml = '<?xml version="1.0" encoding="UTF-8"?>'+ xml;
		
		/**
		 * Private callback method for handling a change to the state when loading the response for a client action from the server
		 * If a reference to a callback method has been pased to method: send, this private method call the defined method
		 * passing the HTTP Object as a parameter when the HTTP Request has ended.
		 * If no callback method is provided, the send method will automatically call processReply if the request succeeded,
		 * i.e. the Server responded with HTTP Code 200 
		 *
		 * @member Client
		 *
		 * @private
		 */
		var _callback = function ()
		{
			report(8, "Retrieving XML, state = "+ obj_HTTP.readyState);
			// Response Loaded
			if (obj_HTTP.readyState == 4)
		    {
		    	// Response OK
		        if (obj_HTTP.status == 200)
		        {
		        	// Use default function for processing reply
		        	if (cb == "undefined" || cb == null)
		        	{
		        		report(8, "XML Doc:"+ obj_HTTP.responseText);
		        		
						var obj_Cache = new Cache(obj_This +".obj_Cache", path);
		        		obj_Cache.set(obj_HTTP);
			        	obj_This.processReply(obj_Cache, true, null);
		        	}
		        	// Use callback object and method for processing reply
		        	else if (cb.length == 2)
		        	{
		        		cb[1](obj_HTTP, cb[0]);
		        	}
		        	// Use callback function for processing reply
		        	else if (cb.length == 1)
		        	{
		        		cb(obj_HTTP);
		        	}
		        }
		        // Error: Problem retrieving data
		        else
		        {
		        	report(2, "Problem retrieving data: "+ obj_HTTP.status +" from: "+ path);
		        	// Use callback object and method for processing reply
		        	if (cb.length == 2)
		        	{
		        		cb[1](obj_HTTP, cb[0]);
		        	}
		        	// Use callback function for processing reply
		        	else if (cb.length == 1)
		        	{
		        		cb(obj_HTTP);
		        	}
		        }
		    }
		}
		var obj_HTTP = httpProducer();
		obj_HTTP.onreadystatechange = _callback;
		obj_HTTP.open("POST", path, true);
		obj_HTTP.setRequestHeader("content-type", "text/xml; charset=\"UTF-8\"");
		obj_HTTP.send(xml);
	}
	
	/**
	 * Sends form input to the server for validation
	 * Prior to sending the data, the method converts the input element into XML code
	 * The constructed XML Document which is sent to the server will have the following format:
	 * 	<root type="input">
	 * 		<{NAME OF DATA TO BE VALIDATED}>{DATA TO BE VALIDATED}</{NAME OF DATA TO BE VALIDATED}>
	 *	</root>
	 *
	 * @member 	Client
	 *
	 * @see 	Client#elem2xml
	 * @see 	Client#send
	 *
	 * @param	{object} oForm	Form object which the element to be sent is part of
	 * @param	{object} oElem	Element object which should be converted to XML and sent to the server
	 */
	Client.prototype.sendInputData = function (oForm, oElem)
	{	
		var xml = '<root type="input">';
		xml += this.elem2xml(oElem);
		xml += '</root>';
		
		// Send XML document to server
		this.send(oForm.getAttribute("action"), xml);
	}
	
	/**
	 * Sends an entire form to the server for validation
	 * Prior to sending the data, the method converts the each of the form's sub-elements into XML code
	 * The constructed XML Document which is sent to the server will have the following format:
	 * 	<root type="form">
	 * 		<{NAME OF FORM FIELD}>{VALUE OF FORM FIELD}</{NAME OF FORM FIELD}>
	 * 		<{NAME OF FORM FIELD}>{VALUE OF FORM FIELD}</{NAME OF FORM FIELD}>
	 *		...
	 *	</root>
	 *
	 * @member 	Client
	 *
	 * @see 	Client#form2xml
	 * @see 	Client#send
	 *
	 * @param	{object} oForm	Form object which should be converted to XML and sent to the server
	 */
	Client.prototype.sendFormData = function (oForm)
	{
		var xml = '<root type="form">';
		xml += this.form2xml(oForm);
		xml += '</root>';
		
		// Send XML document to server
		this.send(oForm.getAttribute("action"), xml);
	}
	
	/**
	 * Sends several related input fields to the server for validation, to allow the one dependant on the others to be validated.
	 * A classic example is a mobile number that needs to be validated for the selected country
	 * in order to do so, the server must receive both the mobile number to validate as well as the country
	 * to validate it in.
	 * The first entry in the aFields array will be assumed to be the one which should be validated and
	 * every entry there after data that is required in some fashion to perform the validation.
	 * The constructed XML Document which is sent to the server will have the following format:
	 * 	<root type="linked">
	 * 		<{NAME OF DATA TO BE VALIDATED} validate="true">{DATA TO BE VALIDATED}</{NAME OF DATA TO BE VALIDATED}>
	 *		<{NAME OF SUPPORT DATA} validate="false">{SUPPORT DATA TO BE USED IN THE VALIDATION}</{NAME OF SUPPORT DATA}>
	 *		<{NAME OF SUPPORT DATA} validate="false">{SUPPORT DATA TO BE USED IN THE VALIDATION}</{NAME OF SUPPORT DATA}>
	 *		...
	 *	</root>
	 *
	 * @member 	Client
	 *
	 * @see 	Client#elem2xml
	 * @see 	Client#send
	 *
	 * @param	{object} oForm		Form object which should be converted to XML and sent to the server
	 * @param	{array} aFields		List of fields which should be sent
	 */
	Client.prototype.sendLinkedData = function (oForm, aFields)
	{
		var xml = '<root type="linked">';
		for (var i=0; i<aFields.length; i++)
		{
			switch (typeof aFields[i])
			{
			case "string":
				var oElem = document.getElementById(aFields[i]);
				break;
			case "object":
				var oElem = aFields[i];
				break;
			default:
				break;
			}
			
			if (i == 0) { var val = 'true'; }
			else { var val = 'false'; }
			xml += '<'+ oElem.name +' validate="'+ val +'">';
			xml += this.encode(oElem.value);
			xml += '</'+ oElem.name +'>';
		}
		xml += '</root>';
		
		// Send XML document to server
		this.send(oForm.action, xml);
	}
	
	/**
	 * Changes the page the user is currently viewing.
	 * The method will call loadPage() in order to handle the communication
	 *
	 * @member 	Client
	 *
	 * @see Client#loadPage
	 *
	 * @param	{string} url	URL the page should be changed to
	 * @param	{Client} oCB	Callback object that the methods for changing a page should be called on
	 */
	Client.prototype.changePage = function (url, oCB)
	{
		// Callback object given
		if (oCB != null) { var obj = oCB; }
		else { var obj = this; }

		obj.loadPage(url, true);
	}

	/**
	 * Loads and caches the specified URL. If the URL is already found in the internal cache,
	 * the cached page is used, otherwise the page identified by the URL is fetched from the server
	 * and added to the cache.
	 * The method instantiates a new Cache object and uses its load method to handle the HTTP communication.
	 *
	 * @member 	Client
	 *
	 * @see Cache
	 * @see Cache#load
	 * @see Client#processReply
	 *
	 * @param	{string} url	URL the page should be changed to
	 * @param	{boolean} disp	Boolean flag indicating whether the new page actually should be displayed		
	 */
	Client.prototype.loadPage = function (url, disp)
	{
		if (disp != false) { disp = true; }
		url = this.decode(url);
		
		try
		{
			report(4, "Cache: "+ url +" - "+ this._aCache[url]);
			if (this._aCache[url] != null && this._aCache[url].getXML() != null)
			{
				this.processReply(this._aCache[url], disp);
			}
			else
			{
				this._aCache[url] = new Cache(this._sName +".obj_Cache", url);
				this._aCache[url].load(url);
				
				/*
				 * Synonym for the "this" pointer as the Timer call back method cannot reference "this"
				 */
				var obj_This = this;
					
				/**
				 * Private method used to let a timer check whether the XML Document has been loaded from the server
				 * The intention of this method is to effectively turn the asynchronious communication with the server
				 * into synchronious as it doesn't make sense to process the reply from a request where the
				 * returned XML Document has not been loaded yet.
				 *
				 * @member Client
				 *
				 * @private
				 */
				var _timerMethod = function()
				{
					// XML document with data and XSL document with design has been loaded for URL
					if (obj_This._aCache[url].getXML() == null)
					{
						setTimeout(_timerMethod, 500);
					}
					else
					{
						obj_This.loadPage(url, disp);
					}
				}
				setTimeout(_timerMethod, 500);
			}
		}
		catch (e)
		{
			report(1, "Error: "+ e.message +" at line: "+ e.lineNumber);
		}
	}
	
	/**
	 * Processes the reply received from the server and updates the GUI accordingly.
	 * If the method receives an error code for a specific element, only that element is highlighted
	 * If the method receives and error code for the entire form, all sub-elements to the form are highlighted
	 * If the method receives new URLs for updating part of the GUI, the function initialises this update.
	 * Additionally, if the root element of the page contains the attribute: cache and cache is set to false, the
	 * URL of the XML Document is removed from the client's cache.
	 *
	 * @param	{Cache} obj_Cache	Cache Object with XML Document and XSL Stylesheet to process
	 * @param	{boolean} disp		Boolean flag indicating whether the page should be displayed if it's of type "page" or "element"
	 * @param	{string} doctype	Document Type, if type="multipart" on the root node, the method will call itself recursively for each document
	 *								(optional)
	 */
	Client.prototype.processReply = function (obj_Cache, disp, doctype)
	{
		// Not a multipart document
		if (doctype == "undefined" || doctype == null)
		{
			doctype = "root";
			
			try
			{
				if (obj_Cache.getXML().getElementsByTagName("root")[0].getAttribute("cache") == 'false')
				{
					delete this._aCache[obj_Cache.getXMLPath()];
				}
			}
			// Cache control attribute missing, default to let the page be cached
			catch (e)
			{
				report(4, "cache attribute missing for tag root in file: "+ obj_Cache.getXMLPath() +" "+ e.lineNumber);
			}
		}
		
		try
		{
			// Get child nodes of current element
			var oRootElems = obj_Cache.getXML().getElementsByTagName(doctype);
			
			for (var i=0; i<oRootElems.length; i++)
			{
				switch (oRootElems[i].getAttribute("type") )
				{
				case "status":	// Status code returned by server
					this.dispStatus(oRootElems[i].childNodes);
					break;
				case "command":	// Command which doesn't requie user interaction returned by server
					this.msg2keep = oRootElems[i].getAttribute("msg");
					this.processCommand(oRootElems[i].childNodes, disp);
					break;
				case "page":	// Page to display
					// Generate HTML for page using the XML document and XSL stylesheet
					if (disp == true) { this.generatePage(obj_Cache, false, this.msg2keep); }
					break;
				case "element":	// Element to be updated with new Data
					// Generate report as HTML using the XML document and XSL stylesheet
					if (disp == true) { this.generatePage(obj_Cache, true); }
					break;
				case "multipart":
					this.processReply(obj_Cache, disp, "document");
					break;
				default:
					report(2, doctype +": unknown document type: "+ oRootElems[i].getAttribute("type") );
					break;
				}
			}
		}
		catch (e)
		{
			report(1, doctype +": "+ "Error: "+ e.message +" at line: "+ e.lineNumber +"\n"+ obj_Cache._sXMLPath +"\n"+ obj_Cache._xml);
		}
	}
	
	/**
	 * Deletes pages from the cache and re-caches them from the server
	 *
	 * @member 	Client
	 *
	 * @param	{NodeList} oElems	List of Nodes with URLs of the pages which whould be re-cached
	 */
	Client.prototype.recache = function (oElems)
	{
		// Delete URLs from Cache
		for (var i=0; i<oElems.length; i++)
		{
			delete this._aCache[oElems[i].firstChild.nodeValue];
		}
		// Load URLs into cache
		for (var i=0; i<oElems.length; i++)
		{
			report(4, "Recaching: "+ oElems[i].firstChild.nodeValue);
			this.loadPage(oElems[i].firstChild.nodeValue, false);
		}
	}
	
	/**
	 * Processes a server command, such as a redirect or when multiple pages should be loaded
	 *
	 * @member 	Client
	 *
	 * @param	{NodeList} oElems	List of Nodes with URLs of the pages which whould be re-cached
	 * @param	{boolean} disp		Boolean flag indicating whether any page elements which are part of the command should be displayed
	 */
	Client.prototype.processCommand = function (oElems, disp)
	{
		// Loop through child nodes
		for (var i=0; i<oElems.length; i++)
		{
			// Ignore nodes which are actually content in the root element
			if (oElems[i].tagName != "undefined" && oElems[i].tagName != null)
			{
				try
				{
					switch (oElems[i].tagName)
					{
					case ("recache"):	// User status has changed, some cached pages must be rechached
						this.recache(oElems[i].getElementsByTagName("url") );
						break;
					default:			// User should be redirected or several page sections updated
						this.loadPage(oElems[i].getElementsByTagName("url")[0].firstChild.nodeValue, disp);
						break;
					}	
				}
				catch (e)
				{
					report(1, "Error: "+ e.message +" at line: "+ e.lineNumber +", tag: "+ oElems[i].tagName);
				}
			}
		}
	}
	
	/**
	 * Generates the HTML code from the XML document and XSL stylesheets and updates the
	 * div tags in the default page accordingly
	 * If the append flag is set to true and the number of nodes in the element that the HTML should be appended to
	 * already has exceeded 1000, the method will automatically start removing the first child nodes.
	 * This prevents the browser from using an excessive amount of memory due to an ever increasing number of nodes.
	 *
	 * @member 	Client
	 *
	 * @param	{Cache} obj_Cache	Cache object from which the XML Document and XSL Stylesheet can be fetched in order to generate XHTML code for the page
	 * @param	{boolean} append	Set to true to append the new HTML to content of the tag or false to overwrite the existing content
	 * @param	{string} msg 		Name of message tag, which message should be kept between pages
	 */
	Client.prototype.generatePage = function (obj_Cache, append, msg)
	{
		if (msg != "undefined" && msg != null)
		{
			var obj_Msg = document.getElementById(msg +"_msg");
			if (obj_Msg != null) { document.getElementById("messages").appendChild(obj_Msg); }
		}
		else
		{
			// Clear previous messages
			document.getElementById("messages").innerHTML = "";
		}
		
		/*
		 * Synonym for the "this" pointer as the Timer call back method cannot reference "this"
		 */
		var obj_This = this;
		
		/**
		 * Private method used to let a timer check whether the XML Document and XSL Stylesheet
		 * have been loaded from the server prior to generating the page.
		 * The intention of this method is to effectively turn the asynchronious communication with the server
		 * into synchronious as it doesn't make sense to generate a page from an XML Document where the
		 * XSL Stylesheet has not yet been loaded
		 *
		 * @member Client
		 */
		var timerMethod = function()
		{
			// XML document with data and XSL document with design has been loaded
			if (obj_Cache.getXML() == null || obj_Cache.getXSL() == null)
			{
				setTimeout(timerMethod, 500);
			}
			else
			{
				// Transform into HTML
				var sHTML = obj_This.makeHTML(obj_Cache.getXML(), obj_Cache.getXSL() );
				
				// Get child nodes of root element
				var oElems = obj_Cache.getXML().getElementsByTagName("root")[0].childNodes;
				// Loop through child nodes
				for (var i=0; i<oElems.length; i++)
				{
					// Ignore nodes which are actually content in the root element
					if (oElems[i].tagName != "undefined" && oElems[i].tagName != null)
					{
						try
						{
							var o = document.getElementById(oElems[i].tagName);
							// Update content
							if (append == true)
							{
								// HTML to Append
								if (sHTML.length > 0)
								{
									o.innerHTML += sHTML;
									/**
									 * Remove first child nodes to prevent memory leak
									 */
									while(o.childNodes.length > 1000)
									{
										o.removeChild(o.firstChild);
									}
								}
							}
							else
							{
								o.innerHTML = sHTML;
								/*
								 * Internet Explorer strips embedded Javascript code.
								 * Additionally Firefox appears to truncate embedded Javascript code when doing DOM manipulation
								 * unless is has been added to the document's DOM Structure.
								 */
								if (sHTML.length > o.innerHTML.length) { obj_This._processJavaScript(sHTML); }
								else { obj_This._processJavaScript(o.innerHTML); }
							}
							// Break loop
							i = oElems.length;
						}
						catch (e)
						{
							report(2, oElems[i].tagName +" - "+ "Error: "+ e.message +" at line: "+ e.lineNumber);
						}
					}
				}
			}
			// Cache any pages from the links on the page
			obj_This.cacheLinks(sHTML);
		}
		setTimeout(timerMethod, 500);
	}
	
	/**
	 * Transforms the XML document into (X)HTML using the XSL stylesheet
	 * The method will clean the generated (X)HTML by removing the transformiix:result tag for Mozilla
	 * and the <?xml version="1.0" encoding="UTF-16"?> tag for Internet Explorer
	 *
	 * @member 	Client
	 *
	 * @param	{DOM Document} oXML 	XML DOM Object with Data to transform
	 * @param	{DOM Document} oXSL		XSL DOM Object with stylesheet to use for transformation
	 * @return	Created (X)HTML document
	 * @type 	string
	 */
	Client.prototype.makeHTML = function (oXML, oXSL)
	{
		try
		{
			// XML Serializer present
			if ( (typeof window.XMLSerializer) != "undefined")
			{
				// Initialise XSLT object for transforming the XML
				var obj_XSLT = new XSLTProcessor();
				
				// Import XSL Stylesheet
				obj_XSLT.importStylesheet(oXSL);
				// Transform and serialise XML document using the imported XSL stylesheet
				var sHTML = new XMLSerializer().serializeToString(obj_XSLT.transformToDocument(oXML) );
											
				// Clean up HTML
				sHTML = sHTML.replace(/<transformiix:result xmlns:transformiix="http:\/\/www.mozilla.org\/TransforMiix">/i, "");
				sHTML = sHTML.replace(/<\/transformiix:result>/i, "");
				sHTML = sHTML.replace(/<transformiix:result>/i, "");
			}
			// Microsoft Internet Explorer
		    else if (window.ActiveXObject != false)
			{
				obj_XSLT = domProducer(oXML.xml);
				var sHTML = obj_XSLT.transformNode(oXSL);
			}
			// Clean up HTML
			sHTML = sHTML.replace(/<\?xml version="1.0" encoding="[^\"]+"\?>/i, "");
			report(8, sHTML);
		}
		// Error: XSL Transformation failed
		catch (e)
		{
			report(1, "XSL Transformation failed with message: "+ e.message +" - XML Doc: "+ oXML.toString() +"\n"+ "XSL Doc: "+ oXSL.toString() );
		}
		
		return sHTML;
	}
	
	/**
	 * Processes all JavaScript code segments in the loaded page.
	 * All variables MUST be declared globally WITHOUT using the "var" modifier to be accessible outside of this method.
	 * Thus: var aMyArray = new Array(); would NOT behave as expected but aMyArray = new Array(); would.
	 *
	 * @member 	Client
	 *
	 * @private
	 *
	 * @param	{string} sHTML 	The HTML code that the page has just been updated with
	 */
	Client.prototype._processJavaScript = function (sHTML)
	{
		try
		{
			sHTML = '<root>'+ sHTML +'</root>';
			var obj_XML = domProducer(sHTML);
			
			// Get all JavaScript sections
			var aObj_Nodes = obj_XML.getElementsByTagName("script");
			
			// Evaluate code
			for (var i=0; i<aObj_Nodes.length; i++)
			{
				report(8, aObj_Nodes[i].firstChild.nodeValue);
				eval(aObj_Nodes[i].firstChild.nodeValue);
			}
		}
		// Error: Unable to evaluate JavaScript
		catch (e)
		{
			report(2, "Unable to evaluate Javascript: "+ aObj_Nodes[i].firstChild.nodeValue +" - "+ e.message +" in line: "+ e.line);
		}
	}
	
	/**
	 * Displays status messages returned by the server
	 *
	 * @member 	Client
	 *
	 * @param	{NodeList} oElems 	List of DOM Nodes with status messages to display
	 */
	Client.prototype.dispStatus = function (oElems)
	{
		// Loop through child nodes
		for (var i=0; i<oElems.length; i++)
		{
			// Ignore nodes which are actually content in the root element
			if (oElems[i].tagName != "undefined" && oElems[i].tagName != null)
			{
				// Status code returned by server
				if (oElems[i].getAttribute("id") != null)
				{
					// Status code indicates an error
					if (oElems[i].getAttribute("id") < 100)
					{
						// Highlight input field
						try
						{
							document.getElementById(oElems[i].tagName +"_img").className = "visible";
						}
						// Not an input field
						catch (e)
						{
							// Attempt to fetch all sub-elements
							try
							{
								var aElems = document.getElementById(oElems[i].getAttribute("name") ).elements;
								for (var j=0; j<aElems.length; j++)
								{
									// Identify current input field
									try
									{
										document.getElementById(aElems[j].name +"_img").className = "visible";
										document.getElementById(aElems[j].name +"_msg").innerHTML = "";
									}
									catch (e) 
									{
										// Ignore
									}
								}
							}
							// Internal error
							catch (e)
							{
								report(1, "Internal Error: No image element found for: "+ oElems[i].tagName +", all input elements must have a corresponding image element named <INPUT NAME>_img");
							}
						}
					}
					// Status code indicates an error or an entire form has been submitted
					if (oElems[i].getAttribute("id") < 100 || oElems[i].getAttribute("id") > 500 || oElems[i].tagName == "form")
					{
						// Display message
						try
						{
							document.getElementById(oElems[i].tagName +"_msg").innerHTML = oElems[i].firstChild.nodeValue;
						}
						// First time a message will be displayed for the input
						catch (e)
						{
							// Create container to hold message
							document.getElementById("messages").innerHTML += '<div id="'+ oElems[i].tagName +'_msg" />';
							// Display message
							document.getElementById(oElems[i].tagName +"_msg").innerHTML = oElems[i].firstChild.nodeValue;
						}
					}
					// Clear message that has been kept
					if (this.msg2keep == oElems[i].tagName)
					{
						setTimeout(this._sName +'.clearMessages("'+ this.msg2keep +'")', 5000);
					}
				}
			}
		}
	}
	
	/**
	 * Clears any messages for the provided element and hides any images that focuses on the input element
	 * The input parameter can either be a reference to the actual Element object or a string with the 
	 * name / id of the Object.
	 * In both cases the method assumes that an messages are stored in {elem}_msg and any images can be identified
	 * as {elem}_img
	 *
	 * @member 	Client
	 *
	 * @param	{object/string} elem	Element for which error data should be cleared
	 */
	Client.prototype.clear = function (elem)
	{
		// Attempt to clear message and hide field identifier
		try
		{
			switch (typeof elem)
			{
			case "string":
				document.getElementById(elem +"_img").className = "hidden";
				document.getElementById(elem +"_msg").innerHTML = "";
				break;
			case "object":
			default:
				document.getElementById(elem.name +"_img").className = "hidden";
				document.getElementById(elem.name +"_msg").innerHTML = "";
				break;
			}
		}
		catch (ignore)
		{
			// Ignore
		}
		// Attempt to clear submit message
		try
		{
			document.getElementById("form_msg").innerHTML = "";
		}
		catch (ignore)
		{
			// Ignore
		}
	}
	
	/**
	 * Removes a node from the messages part of the page
	 *
	 * @member 	Client
	 *
	 * @param	{string} id		ID of Message node to remove
	 */
	Client.prototype.clearMessages = function (id)
	{
		var obj = document.getElementById("messages");
		
		for (var i=0; i<obj.childNodes.length; i++)
		{
			try
			{
				if (obj.childNodes[i].getAttribute("id") == id +"_msg")
				{
					obj.removeChild(obj.childNodes[i]);
					i = obj.childNodes.length;
				}
			}
			catch (e)
			{
				report(1, obj.childNodes[i].tagName +": "+ "Error: "+ e.message +" at line: "+ e.lineNumber);
			}
		}
		this.msg2keep = null;
	}
	
	/**
	 * Sends a keep alive request to the Server in order to ensure that the user session doesn't time out
	 * due to all accessed pages being cached
	 * The constructed XML Document which is sent to the server will have the following format:
	 * 	<root type="keepalive" id="{ID OF USER'S SESSION ON THE SERVER}" />
	 *
	 * @member 	Client
	 *
	 * @param	{string} url	Path on the Server side component which handles the client's Keep Alive requests
	 * @param	{integer} to	Timeout interval in seconds specifying how often the server should be contacted
	 */
	Client.prototype.keepAlive = function(url, to)
	{
		to = to*1000;
		/*
		 * Synonym for the "this" pointer as the Timer call back method cannot reference "this"
		 */
		var obj_This = this;
		
		/**
		 * Private callback method for handling the response from the server from the Keep Alive Request.
		 *
		 * @member Client
		 *
		 * @private
		 */
		var _callback = function (oHTTP)
		{
			try
			{
				// First Keep Alive call
				if (oHTTP == null)
				{
					_timerMethod();
				}
				// Keep Alive request succeded
				else if (oHTTP.responseXML.getElementsByTagName("keepalive")[0].getAttribute("id") == 100)
				{
					report(4, "Keep Alive request to: "+ url +" succeeded, repeating in "+ (to/1000) +" seconds");
					if (obj_This._iSessionID == 0)
					{
						obj_This._iSessionID = oHTTP.responseXML.getElementsByTagName("keepalive")[0].firstChild.nodeValue;
						report(8, "Settings Session ID to: "+ obj_This._iSessionID);
					}
					setTimeout(_timerMethod, to);
				}
				// Keep Alive request failed, retry in 10 seconds
				else
				{
					report(2, "Keep Alive request to: "+ url +" failed, retrying in 10 seconds");
					setTimeout(_timerMethod, 10000);
				}
			}
			// Keep Alive request failed, retry in 10 seconds
			catch (e)
			{
				report(1, "Keep Alive request to: "+ url +" failed with error: "+ e.message +" at line: "+ e.lineNumber +", retrying in 10 seconds");
				setTimeout(_timerMethod, 10000);
			}
		}
		
		/**
		 * Private method used to let a timer initiate a Keep Alive request to the server
		 *
		 * @member Client
		 *
		 * @private
		 */
		var _timerMethod = function ()
		{
			var xml = '<root type="keepalive" id="'+ obj_This._iSessionID +'" />';
			obj_This.send(url, xml, _callback);
		}
		
		_callback(null);
	}
	
	/**
	 * Caches all links on a page to speed up the GUI
	 *
	 * @member 	Client
	 *
	 * @see 	Client#getLinks
	 * @see 	Client#changePage
	 *
	 * @param	{string} sHTML	HTML code of the page from which the links should be cached
	 */
	Client.prototype.cacheLinks = function(sHTML)
	{
		var aLinks = this.getLinks(sHTML);
		
		for (var i=0; i<aLinks.length; i++)
		{
			this.loadPage(aLinks[i], false);
		}
	}
	
	/**
	 * Analyses the HTML code and retrieves any links which are using the changePage method.
	 * Links with the rel attribute set to "nocache" ie. <a href="#" rel="nocache" onclick="obj_Client.changePage('{URL}');">
	 * are filtered out.
	 * This allows the programmer to specify that links such as logout and login should not be cached
	 *
	 * @member 	Client
	 *
	 * @param	{string} sHTML	HTML code of the page from which the links should be cached
	 * @return	array
	 * @type	array
	 */
	Client.prototype.getLinks = function (sHTML)
	{	
		var aLinks = new Array();
		
		try
		{
			// Retrieve all anchor tags from the HTML code
			var a1 = sHTML.match(/<a .*?>/gim);
			
			for (var i=0; i<a1.length; i++)
			{
				// Remove any anchor tags with rel="nocache"
				if (a1[i].match(/rel=["']nocache["']/i) == null)
				{
					// Isolate the URL part of the call to the changePage method
					var a2 = a1[i].split(/.changePage\(["']/i);
					for (var n=1; n<a2.length; n=n+2)
					{
						var a3 = a2[n].split(/["']/gim);
						aLinks[aLinks.length] = a3[0];
					}
				}
			}
		}
		catch (ignore)
		{
			// Ignore
		}
		
		return aLinks;
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