<?php
/**
 * The Image sub-package provides features for fetching images from different sources as well as optimizing the image
 * for the customer's Mobile Device using its User Agent Profile.
 *
 * @author Jonatan Evald Buus
 * @copyright Cellpoint Mobile
 * @link http://www.cellpointmobile.com
 * @package Payment
 * @subpackage Image
 * @version 1.0
 */

/**
 * The RetrieveImage class provides methods for easily retrieving and resizing an image from a source.
 * Currently the following image types can be retrieved:
 * 	- Client Logo
 * 	- Card Logo
 *
 */
class RetrieveImage extends General
{
	/**
	 * Default Constructor
	 *
	 * @param	RDB $oDB			Reference to the Database Object that holds the active connection to the mPoint Database
	 * @param	TranslateText $oDB 	Reference to the Text Translation Object for translating any text into a specific language
	 * @param	UAProfile $oUA 		Reference to the data object with the User Agent Profile for the customer's mobile device
	 */
	public function __construct(RDB &$oDB, TranslateText &$oTxt)
	{
		parent::__construct($oDB, $oTxt);
	}

	/**
	 * Retrieves and resizes a Client Logo in accordance with the screen resolution of the customer's Mobile Device.
	 * The image is resized so it will fit within the screen's width and will be no larger than 20% of the screen's height.
	 *
	 * @see 	iCLIENT_LOGO_SCALE
	 *
	 * @param 	string $url 	Absolute URL to the Client Logo
	 * @return 	Image
	 */
	public function getClientLogo($url)
	{
		// Re-Size Image to fit the screen resolution of the Customer's Mobile Device using its User Agent Profile
		$obj_Image = new Image($url);

		return $obj_Image;
	}

	/**
	 * Fetches the logo of a Credit Card from the Database and resizes it to take up approximately 1% of screen space.
	 * The logo is effectively resized to fit within a rectangle, which has a width and height that is equal to or less than 10%
	 * of the screen's width / height.
	 *
	 * @see 	iCARD_LOGO_SCALE
	 *
	 * @param 	integer $id 	Unique Card ID that should be fetched
	 * @return 	Image
	 */
	public function getCardLogo($id)
	{
		$sql = "SELECT logo
				FROM System.Card_Tbl
				WHERE id = ". intval($id);
//		echo $sql ."\n";
		$RS = $this->getDBConn()->getName($sql);

		$obj_Image =  new Image($this->getDBConn()->unescBin($RS["LOGO"]), true);

		return $obj_Image;
	}

	/**
	 * Retrieves and resizes mPoint's Logo in accordance with the screen resolution of the customer's Mobile Device.
	 * The logo is effectively resized to fit within a rectangle, which has a width and height that is equal to or less than 30%
	 * of the screen's width / height.
	 *
	 * @see 	iMPOINT_LOGO_SCALE
	 *
	 * @return 	Image
	 */
	public function getmPointLogo()
	{
		// Re-Size Image to fit the screen resolution of the Customer's Mobile Device using its User Agent Profile
		$obj_Image = new Image("mpoint_logo.png");

		return $obj_Image;
	}
}
?>