/**
 * @fileoverview	The Menu class offers functionality to create simple one-level drop down menus.
 *					This is done using a div tag and a css class, thus a menu's appearance can be 100% customised
 *					in accordance with the general page design.
 * 
 * @author Jonatan Evald Buus
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Constructer for creating a new Menu object
 *
 * @constructor
 *
 * @class		The Menu class offers functionality to create simple one-level drop down menus.
 * 				This is done using a div tag and a css class, thus a menu's appearance can be 100% customised
 *				in accordance with the general page design.
 *				The class includes methods for handling the selection of a menu item as well as showing and hiding dropdown menus.
 *
 * @return	Instantiated object of class Menu
 * @type	Menu
 */
function Menu()
{
	this.css = null;
	
	/**
	 * Indicates the currently selected menu option by changing the CSS Class of
	 * the provided element and clearing the CSS class of all siblings
	 *
	 * @param	{object} obj 	Menu Option Element where user just clicked
	 * @param	{string} css 	New CSS class for the currently selected menu option
	 */
	Menu.prototype.select = function (obj, css)
	{
		if (this.css == null) { this.css = ''; }
		// Parent has no siblings
		if ( (obj.parentNode.nextSibling == null && obj.parentNode.previousSibling == null)
				|| (obj.parentNode.nextSibling != null && obj.parentNode.nextSibling.childNodes.length == 0 && obj.parentNode.previousSibling != null && obj.parentNode.previousSibling.childNodes.length == 0) )
		{
			var oSibling = obj.nextSibling;
			// Clear CSS class for all following siblings
			while (oSibling != null)
			{
				if (oSibling.className == css)
				{
					oSibling.className = oSibling.className.replace(' '+ css, '');
					oSibling.className = oSibling.className.replace(css, '');
					this.css = null;
					break;
				}
				oSibling = oSibling.nextSibling;
			}
			// Already found
			if (this.css != null)
			{
				oSibling = obj.previousSibling;
				// Clear CSS class for all previous siblings
				while (oSibling != null)
				{
					if (oSibling.className == css)
					{
						oSibling.className = oSibling.className.replace(' '+ css, '');
						oSibling.className = oSibling.className.replace(css, '');
						break;
					}
					oSibling = oSibling.previousSibling;
				}
			}
		}
		else
		{
			var oSibling = obj.parentNode.nextSibling;
			// Clear CSS class for all following siblings
			while (oSibling != null)
			{
				for (var i=0; i<oSibling.childNodes.length; i++)
				{
					if (oSibling.childNodes[i].className == css)
					{
						oSibling.childNodes[i].className = oSibling.childNodes[i].className.replace(' '+ css, '');
						oSibling.childNodes[i].className = oSibling.childNodes[i].className.replace(css, '');
						this.css = null;
						i = oSibling.childNodes.length;
					}
				}
				oSibling = oSibling.nextSibling;
			}
			
			// Already found
			if (this.css != null)
			{
				oSibling = obj.parentNode.previousSibling;
				// Clear CSS class for all previous siblings
				while (oSibling != null)
				{
					for (var i=0; i<oSibling.childNodes.length; i++)
					{
						if (oSibling.childNodes[i].className == css)
						{
							oSibling.childNodes[i].className = oSibling.childNodes[i].className.replace(' '+ css, '');
							oSibling.childNodes[i].className = oSibling.childNodes[i].className.replace(css, '');
							i = oSibling.childNodes.length;
						}
					}
					oSibling = oSibling.previousSibling;
				}
			}
		}
		this.css = obj.className; 
		// Change CSS class for current menu option
		if (obj.className == null || obj.className == '') { obj.className = css; }
		else { obj.className += ' '+ css; }
	}

	/**
	 * Shows the dropdown menu for a menu option
	 *
	 * @param	{object} obj 	Menu Option Element where user is holding the cursor over
	 */
	Menu.prototype.show = function (obj)
	{
		obj.style.visibility = 'visible';
	}

	/**
	 * Hides the dropdown menu for a menu option
	 *
	 * @param	{object} obj 	Menu Option Element where user moved the cursor from
	 */
	Menu.prototype.hide = function (obj)
	{
		obj.style.visibility = 'hidden';
	}
	
	Menu.prototype.deselect = function (obj, css)
	{
		obj.className = obj.className.replace(' '+ css, '');
		obj.className = obj.className.replace(css, '');
	}
}