/**
 * @fileoverview	The Window package re-creates the functionality of the built-in window.open function in Javascript
 *					This is done using a div tag and a css class to ensure maximum flexibility and speed
 * 
 * @author Jonatan Evald Buus
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Constructer for creating a new Window object
 *
 * @constructor
 *
 * @class		The Window class re-creates the functionality of the built-in window.open function in Javascript
 *				This is done using a div tag and a css class, thus a window's appearance can be 100% customised
 *				in accordance with the general page design and it is far quicker to open a div than an entire new
 *				browser window, thus the user experience is improved as the system feels faster
 *				The class includes methods for handling the opening of a new window, closing of a window and moving
 *				of a window
 *
 * @param	{string} name	Name of Object that the class is instantiated as
 * @return	Instantiated object of class Window
 * @type	Window
 */
function Window(name)
{
	/**
	 * Mouse Cursor's current X coordinate
	 *
	 * @type integer
	 */
	this.iMouseX = 0;
	/**
	 * Mouse Cursor's current Y coordinate
	 *
	 * @type integer
	 */
	this.iMouseY = 0;
	
	/**
	 * Mouse Cursor's X coordinate when the window was clicked
	 *
	 * @type integer
	 */
	this.iGrabX = 0;
	/**
	 * Mouse Cursor's Y coordinate when the window was clicked
	 *
	 * @type integer
	 */
	this.iGrabY = 0;
	/**
	 * Orignal X coordinate of the Window's lefthand side
	 *
	 * @type integer
	 */
	this.iOrgX = 0;
	/**
	 * Orignal Y coordinate of the Window's top
	 *
	 * @type integer
	 */
	this.iOrgY = 0;
	/**
	 * Current X coordinate of the Window's lefthand side
	 *
	 * @type integer
	 */
	this.iElemX = 0;
	/**
	 * Current Y coordinate of the Window's top
	 *
	 * @type integer
	 */
	this.iElemY = 0;
	
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
	 * Pointer to the object itself as callback methods cannot use the "this" pointer
	 *
	 * @type Window
	 */
	var obj_This = this;
	
	/**
	 * Opens a new Window using a div tag and a css class
	 * This method should be registered as the onclick event for the element that opens the new window
	 * I.e. onclick="javascript:obj_Console.openWindow(id, pid, url, css, cb);"
	 *
	 * @member Window
	 *
	 * @param	{string} id	 	ID of new Window, should match the tag returned XML document fetched via url
	 * @param	{string} pid	ID of Parent element that the new window should be appended to
	 * @param	{string} url 	URL that should be loaded into the new window
	 * @param	{string} css 	CSS class used for controlling the position / size / behaviour of the Window
	 * @param	{mixed} cb 		Callback for loading the URL, if no callback function is provided, the url will simply be displayed in the new window
	 *							The callback can either be an array with an object in position 0 and method in position 1 or
	 *							a string with the function name
	 * @param	{string} style 	CSS style used for overridding the CSS class behaviour
	 *							(optional)
	 */
	Window.prototype.openWindow = function (id, pid, url, css, cb, style)
	{
		// Window doesn't exist
		if (document.getElementById(id) == "undefined" || document.getElementById(id) == null)
		{
			try
			{
				var obj_Window = document.createElement('div');
				obj_Window.setAttribute("id", id);
				
				obj_Window.className = css;
				if (style != "undefined" && style != null)
				{
					obj_Window.setAttribute("style", style);
				}
				obj_Window.style.zIndex = document.getElementById(pid).style.zIndex + 10;
				document.getElementById(pid).appendChild(obj_Window);
			}
			catch (e)
			{
				document.getElementById(pid).innerHTML += '<div id="'+ id +'" class="'+ css +'" />';
				if (style != "undefined" && style != null)
				{
					document.getElementById(id).setAttribute("style", style);
				}
				document.getElementById(id).style.zIndex = document.getElementById(pid).style.zIndex + 10;
			}
		}
		
		if (cb == "undefined" || cb == null)
		{
			document.getElementById(id).appendChild(document.createTextNode(url) );
		}
		else if (cb.length == 2)
		{
			cb[1](url, cb[0]);
		}
		else
		{
			cb(url);
		}
	}
	
	/**
	 * Closes an existing Window that has been created using divs
	 * This method should be registered as the onclick event for the element that closes the window
	 * I.e. onclick="javascript:obj_Console.closeWindow(id);"
	 *
	 * @member Window
	 *
	 * @param	{string} id		ID of Window that should be closed
	 */
	Window.prototype.closeWindow = function (id)
	{
		// Attempt to close window by removing it from the DOM tree
		try
		{
			document.getElementById(id).parentNode.removeChild(document.getElementById(id) );
		}
		catch (e)
		{
			// No window found with the id - Ignore
		}
	}
	
	/**
	 * Moves a Window to allow the user to drag it across the screen
	 * This method should be registered as the onmousedown event for the element that opens the new window
	 * I.e. onmousedown="javascript:obj_Console.moveWindow(this);"
	 *
	 * @member Window
	 *
	 * @param	{object} oElem 	Element to move, is likely a div element
	 */
	Window.prototype.moveWindow = function (oElem)
	{
		try
		{
			// IE
			if (oElem.srcElement)
			{
				var o = event.srcElement;
				while(o.attributes != null && o.getAttribute("id") == "")
				{
					o = o.parentNode;
				}
				if (o.attributes != null)
				{
					oElem = document.getElementById(o.getAttribute("id") );
				}
			}
			// Store element to move
			obj_This.obj2move = oElem;
			
			// Store Mouse cursor position
			obj_This.iGrabX = obj_This.iMouseX;
			obj_This.iGrabY = obj_This.iMouseY;
			// Store elements current top/left position
			obj_This.iElemX = obj_This.obj2move.offsetLeft;
			obj_This.iElemY = obj_This.obj2move.offsetTop;
			// Store elements original top/left position (same as current position at this point in time)
			obj_This.iOrgX = obj_This.obj2move.offsetLeft;
			obj_This.iOrgY = obj_This.obj2move.offsetTop;
			
			// in NS obj_This prevents cascading of events, thus disabling text selection
			if (document.onmousedown) { document.onmousedown = obj_This.falseFunc; }
			document.onmousemove = obj_This.drag;
			document.onmouseup = obj_This.drop;
		}
		// Unable to move the element
		catch (e)
		{
			// Ignore
		}
	}
	
	/**
	 * Get's the mouse cursor's current X/Y coordinate
	 * Works in IE5.5, IE6, Firefox, Mozilla, Opera7
	 *
	 * @member Window
	 *
	 * @param	{event} e 	"on mouse move" event passed by the Gecko engine, will be null for IE
	 */
	Window.prototype.getMouseXY = function (e)
	{
		// Works on IE, but not NS (we rely on NS passing us the event)	
		if (!e) { e = window.event; }
	
		if (e)
		{
			// This doesn't work on IE5.5 & IE6, only works on Firefox, Mozilla, Opera7
			if (e.pageX || e.pageY)
			{
				obj_This.iMouseX = e.pageX;
				obj_This.iMouseY = e.pageY;
			}
			// Works on IE5.5, IE6,Firefox, Mozilla, Opera7
			else if (e.clientX || e.clientY)
			{
				obj_This.iMouseX = e.clientX + document.body.scrollLeft;
				obj_This.iMouseY = e.clientY + document.body.scrollTop;
			}
		}
	}
	
	/**
	 * Drags the window across the screen, i.e. updates it's top/left coordinates as the user moves the mouse
	 * Works in IE5.5, IE6, Firefox, Mozilla, Opera7
	 * Passing of the event parameter is important for NS family 
	 *
	 * @member Window
	 *
	 * @param	{event} e 	"on mouse move" event passed by the Gecko engine, will be null for IE
	 * @return	Always returns false, to prevent cascading in IE, thus text selection is disabled
	 * @type	boolean
	 */
	Window.prototype.drag = function drag(e)
	{
		// Works on IE, but not NS (we rely on NS passing us the event)	
		if (!e) { e = window.event; }
			
		if (obj_This.obj2move)
		{	
			// Calculate Element's current position
			obj_This.iElemX = obj_This.iOrgX + (obj_This.iMouseX - obj_This.iGrabX);
			obj_This.iElemY = obj_This.iOrgY + (obj_This.iMouseY - obj_This.iGrabY);

			// Move element
			obj_This.obj2move.style.position = "absolute";
			obj_This.obj2move.style.left = (obj_This.iElemX).toString(10) +"px";
			obj_This.obj2move.style.top = (obj_This.iElemY).toString(10) +"px";
		}
		obj_This.getMouseXY(e);
		
		// In IE this prevents cascading of events, thus text selection is disabled
		return false;
	}
	
	/**
	 * Drops the element when the "on mouse up" element is triggered and resets all internal
	 * control variables so another element can be moved nex
	 * Works in IE5.5, IE6, Firefox, Mozilla, Opera7
	 *
	 * @member Window
	 */
	Window.prototype.drop = function drop()
	{
		if (obj_This.obj2move) { obj_This.obj2move = null; }
		document.onmouseup = null;
		document.onmousedown = null;   // re-enables text selection on NS
	}
	
	/**
	 * Dummy function, used to block cascading events
	 *
	 * @member Window
	 */
	Window.prototype.falsefunc = function falseFunc() { return false; }
	/**
	 * Registers method for receiving "on mouse move" events 
	 * This allows the class to always know the X/Y coordinate of the mouse cursor
	 */
	document.onmousemove = this.getMouseXY;
}