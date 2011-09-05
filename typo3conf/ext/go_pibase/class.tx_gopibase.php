<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Lucas Jenß <lucas@gosign.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_typo3 . 'sysext/cms/tslib/class.tslib_pibase.php');

require_once(PATH_typo3conf.'ext/go_pibase/class.tx_gopibase_local.php');

require_once(PATH_typo3conf.'ext/go_pibase/lib/class.imageResize.php');
#require_once(PATH_typo3conf.'ext/realurl/class.tx_realurl_advanced.php');
#require_once(PATH_typo3conf.'ext/dam/lib/class.tx_dam_indexing.php');

/**
 * Gosign Pluginbase.
 *
 * @author	Lucas Jenß <lucas@gosign.de>
 */
class tx_gopibase extends tslib_pibase {
	protected $local;	//Instance of class tx_gopibase_local which holds the project specific functions for the pibase

	protected $markerArray = array();					///< Default marker array.
	protected $subpartMarkerArray = array();			///< Default subpart marker array.
	protected $wrappedSubpartMarkerArray = array();	///< Default wrapped subpart marker array.
	protected $templateFile = '';						///< Template file loaded by loadTemplate(). If not set, the default template will be used (EXT:$extKey/pi#/template.html).
	protected $template = '';							///< Contents of the template file. Should not be modified manually.

	protected $img = null;							///< Instance of imageResize-class which handles image operations.

	protected $uploadRoot	= 'fileadmin/user_upload/unprocessed/';
	protected $thumbnailSize	= array(194, 126);
	protected $videoSize		= array(320, 240);
	protected $imageSize		= array(640, 480);

	protected $damMediaPid;		///< The pid for DAM images

	/* Variables used for paging. */
	protected $itemsPerPage = 9;
	protected $currentPage = 1;
	protected $totalPages = 0;

	protected $tempSubdir = '';	///< Subdirectory in typo3temp/ for temporary filenames (WITH trailing slash!). If directory does not exist, it is created.

	protected $jsPath = 'fileadmin/templates/javascript/';

	/**
	 * Class constructor.
	 */
	function __construct() {
		parent::__construct();

		$this->local = new tx_gopibase_local($this);

		$this->img = new imageResize();

		if(t3lib_extMgm::isLoaded('dam')) {
			foreach(explode(',', tx_dam_db::getPidList()) as $damPid) {
				$this->damMediaPid = $damPid;
				break;
			}
		}
	}

	/**
	 * Adds an item to the marker array.
	 *
	 * @param	string	Name of the marker.
	 * @param	string	Value which the marker should be replaced with.
	 */
	function addMarker($name, $value) {
		$this->markerArray['###'.strtoupper($name).'###'] = $value;
	}

	/**
	 * Gets an item of the marker array.
	 *
	 * @param	string	Name of the marker.
	 * @return  string	Value which the marker should be replaced with.
	 */
	function getMarker($name) {
		return ($this->markerArray['###'.strtoupper($name).'###']) ? $this->markerArray['###'.strtoupper($name).'###'] : false;
	}

	/**
	 * Adds an item to the subpart marker array.
	 *
	 * @param	string	Name of the marker.
	 * @param	string	Value which the marker should be replaced with.
	 */
	function addSMarker($name, $value) {
		$this->subpartMarkerArray['###'.strtoupper($name).'###'] = $value;
	}

	/**
	 * Gets an item from the subpart marker array.
	 *
	 * @param	string	Name of the marker.
	 * @return	string	Value which the marker should be replaced with.
	 */
	function getSMarker($name) {
		return ($this->subpartMarkerArray['###'.strtoupper($name).'###']) ? $this->subpartMarkerArray['###'.strtoupper($name).'###'] : false;
	}

	/**
	 * Adds an item to the wrapped subpart marker array.
	 *
	 * @param	string	Name of the marker.
	 * @param	string	Value for the left side of the wrap.
	 * @param	string	Value for the right side of the wrap.
	 */
	function addWSMarker($name, $left, $right) {
		$this->subpartMarkerArray['###'.strtoupper($name).'###'] = array($left, $right);
		return;
	}

	/**
	 * Gets an item to the wrapped subpart marker array.
	 *
	 * @param	string	Name of the marker.
	 * @return  array of two strings
	 * @return	string	Value for the left side of the wrap.
	 * @return	string	Value for the right side of the wrap.
	 */
	function getWSMarker($name) {
		return ($this->subpartMarkerArray['###'.strtoupper($name).'###']) ? $this->subpartMarkerArray['###'.strtoupper($name).'###'] : false;
	}

	/**
	 * Adds an item to the given marker array.
	 *
	 * @param	string	Name of the marker.
	 * @param	string	Value which the marker should be replaced with.
	 * @param	array	a reference to the marker-array
	 */
	function addMarkerUser($name, $value, $markerArray) {
		$markerArray['###'.strtoupper($name).'###'] = $value;
	}

	/**
	 * Loads the plugin template, if not already loaded. The template is taken from
	 * conf[templateFile]
	 * or, if set, from the file specified in $this->templateFile.
	 * or, else, the plugin directory
	 *
	 * @return	null
	 */
	function loadTemplate() {
		if($this->template) {
			return;
		}

		if (isset($this->conf['templateFile'])){
			$this->templateFile = $this->conf['templateFile'];
		}

		if(!$this->templateFile) {
			$this->template = $this->cObj->fileResource('EXT:'.$this->extKey.'/'.substr($this->prefixId, strpos($this->prefixId, '_pi')+1).'/template.html');
		}
		else {
			$this->template = $this->cObj->fileResource($this->templateFile);
		}
	}

	/**
	 * Returns the specified subpart from $this->template. If no template has been loaded, $this->loadTemplate is called first.
	 *
	 * @param	string	Name of the subpart.
	 * @return	string	Template subpart.
	 */
	function getSubpart($name) {
		if(!$this->template) {
			$this->loadTemplate();
		}
		return $this->cObj->getSubpart($this->template, '###'.strtoupper($name).'###');
	}

	/**
	 * Substitutes a marker like ###preview### with a bunch of lines ($content) and wraps it with
	 * subpart markers <!--- ###PREVIEW### ---> content <!--- ###PREVIEW ### --->
	 * @author Marius
	 *
	 * @param	string	Name of the subpart.
	 * @param	string  Content
	 * @return	void
	 */
	function setSubpartSubstituteMarkers($subpart, $content) {
		if(!$this->template) {
			$this->loadTemplate();
		}
		$content = '<!--- ###'.strtoupper($subpart).'### begin --->' .$content .'<!--- ###'.strtoupper($subpart).'### end --->';
		$this->template = $this->substituteMarkers($this->template, array('###'.strtoupper($subpart).'###' => $content));
	}

	/**
	 * Parses a template
	 *
	 * @param	subpart		Subpart of the template which should be parsed.
	 * @param	markerArray	Marker array which should be used to replace the template markers. If not set, $this->markerArray will be used.
	 * @return	string		The result of substituteMarkerArrayCached().
	 */
	function parseTemplate($subpart, $markerArray=false, $subpartMarkerArray=false, $wrappedSubpartMarkerArray=false) {
		$markerArray = $markerArray ? $markerArray : $this->markerArray;
		$subpartMarkerArray = $subpartMarkerArray ? $subpartMarkerArray : $this->subpartMarkerArray;
		$wrappedSubpartMarkerArray = $wrappedSubpartMarkerArray ? $wrappedSubpartMarkerArray : $this->wrappedSubpartMarkerArray;

		$this->loadTemplate();

		$tmp = $this->cObj->getSubpart($this->template, '###'.strtoupper($subpart).'###');
		return $this->cObj->substituteMarkerArrayCached($tmp, $markerArray, $subpartMarkerArray, $wrappedSubpartMarkerArray);
	}

	/**
	 * Wrapper for substituteMarkerArrayCached() to make function call shorter.
	 *
	 * @param	string	The content stream, typically HTML template content.
	 * @param	array	Regular marker-array where the 'keys' are substituted in $content with their values
	 * @param	array	Exactly like markContentArray only is whole subparts substituted and not only a single marker.
	 * @param	array	An array of arrays with 0/1 keys where the subparts pointed to by the main key is wrapped with the 0/1 value alternating.
	 * @return
	 */
	function substituteMarkers($template, $markerArray, $subpartContentArray=array(), $wrappedSubpartContentArray=array()) {
		return $this->cObj->substituteMarkerArrayCached($template, $markerArray, $subpartContentArray, $wrappedSubpartContentArray);
	}

	/** 10-01-2011
	 * This substitutes the language Markers automatically (!!and updates the template code!!)
	 * 
	 * ###LLL:xxx### will be substituted by the locallang.xml value of index xxx
	 * 
	 * @autor Elio Wahlen <vorname at gosign dot de>
	 */
	function substituteLanguageMarkers() {
		$this->loadTemplate;
		$code = $this->template;
		$markers = array();
		while ( ($code = stristr( $code, '###LLL:')) !== FALSE ) {
			$marker = substr( $code, 7, strpos(substr($code,7),'###'));
			$markers[] = $marker;
			// strip the ###LLL: thing, so that we can find the next one
			$code = substr($code,7);
			$this->markerArray['###LLL:'.$marker.'###'] = $this->pi_getLL($marker,'*value not defined*');
		}
		$this->template = $this->substituteMarkers( $this->template, $this->markerArray );
	}


	/** 10-01-2011
	 * This substitutes the value Markers automatically (!!and updates the template code!!)
	 * 
	 * ###value_xxx### will be substituted by the POST value xxx or a default value
	 * 
	 * @autor Elio Wahlen <vorname at gosign dot de>
	 */
	function substituteValueMarkers() {
		$this->loadTemplate;
		$code = $this->template;
		$markers = array();
		while ( ($code = stristr( $code, '###value_')) !== FALSE ) {
			$marker = substr( $code, 9, strpos(substr($code,9),'###'));
			$markers[] = $marker;
			// strip the ###value_ thing, so that we can find the next one
			$code = substr($code,9);
			// fill in the post variable, or, if not yet submitted, the default value
			$this->markerArray['###value_'.$marker.'###'] = t3lib_div::_GP('submitted') ? t3lib_div::_GP($marker) : $this->pi_getLL($marker.'.default');
		}
		$this->template = $this->substituteMarkers( $this->template, $this->markers );
	}	
	
	/* == function sendEmail ==
	 * Sends an email.
	 * @param $subject
	 * @param $content
	 * @param $attachment (filepath)
	 * @param $from
	 * @param $to
	 * @param $cc
	 * @param $bcc
	 *
	 * @author Marius
	 */
	function sendEmail($subject, $message, $attachment='', $email_from, $email_fromName='', $to, $cc='', $bcc='',  $replyTo='') {
		foreach(array($to, $cc, $bcc, $email_from, $replyTo) as $emails)
			if (strlen($emails) > 0)
				if (!$this->isEmail($emails))
					return "The Address $emails is no valid email address.";
		if ($attachment)
			return "Sending attachments is not implemented yet.";
		if ($bcc)
			return "Sending blind carbon copies is not implemented yet.";

		$message = $subject . "\n\n" . $message;
		$this->cObj->sendNotifyEmail($message, $to, $cc, $email_from, $email_fromName, $replyTo);

		return false;
	}

	/**
	 * Validates a given E-Mail address.
	 * RegEx taken from http://www.regular-expressions.info/email.html.
	 *
	 * @param email	The E-Mail address(es) to be validated (separated by ',')
	 * @return		true in case of valid E-Mail address, otherwise false.
	 */
	function isEmail($email) {
		$isEmail = true;
		foreach (explode(',', $email) as $single)
			$isEmail &= preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);
		return $isEmail;
	}

	/**
	 * Validates a http(s):// url.
	 *
	 * @return	bool	true in case of a valid url, otherwise false.
	 */
	function isUrl($url) {
		$regex = <<<EOF
{
  \\b
  # Match the leading part (proto://hostname, or just hostname)
  (
    # http://, or https:// leading part
    (https?)://[-\\w]+(\\.\\w[-\\w]*)+
  |
    # or, try to find a hostname with more specific sub-expression
    (?i: [a-z0-9] (?:[-a-z0-9]*[a-z0-9])? \\. )+ # sub domains
    # Now ending .com, etc. For these, require lowercase
    (?-i: com\\b
        | edu\\b
        | biz\\b
        | gov\\b
        | in(?:t|fo)\\b # .int or .info
        | mil\\b
        | net\\b
        | org\\b
        | [a-z][a-z]\\.[a-z][a-z]\\b # two-letter country code
    )
  )

  # Allow an optional port number
  ( : \\d+ )?

  # The rest of the URL is optional, and begins with /
  (
    /
    # The rest are heuristics for what seems to work well
    [^.!,?;"\\'<>()\[\]\{\}\s\x7F-\\xFF]*
    (
      [.!,?]+ [^.!,?;"\\'<>()\\[\\]\{\\}\s\\x7F-\\xFF]+
    )*
  )?
}ix
EOF;

		return preg_match($regex, $url);
	}

	/**
	 * Appends specified data to the <head>-section of the document.
	 *
	 * @params	string	Data to be added.
	 * @params	string	Key used for the headerData array.
	 */
	function addHeaderData($data, $key='') {
		$GLOBALS['TSFE']->additionalHeaderData[(empty($key) ? $this->prefixId : $key)] .= $data;
	}

	/**
	 * Includes an additional CSS file
	 * DEPRECATED!! include by typoscript instead!!
	 *
	 * @params	string	The CSS file to add
	 * @params	string	(optional) Key used for the headerData array. (default: $this->prefixId)
	 */
	function addCSSfile($path, $key='') {
		$this->addHeaderData('<link rel="stylesheet" type="text/css" href="'.$path.'" media="screen" />', (empty($key) ? t3lib_div::shortMd5($path) : $key));
	}

	/**
	 * Includes an additional JavaScript file
	 * DEPRECATED!! include by typoscript instead!!
	 *
	 * @params	string	The JavaScript file to add
	 * @params	string	(optional) Key used for the headerData array. (default: $this->prefixId)
	 */
	function addJSfile($path, $key='') {
		$this->addHeaderData('<script type="text/javascript" src="'.(basename($path) == $path ? $this->jsPath : '').$path.'"></script>', (empty($key) ? t3lib_div::shortMd5($path) : $key));
	}

	function addJSlib($path) {
		$this->addJSfile($path);
		#$this->addHeaderData('<script type="text/javascript" src="'.(basename($path) == $path ? $this->jsPath : '').$path.'"></script>', t3lib_div::shortMd5($path));
	}

	/**
	 * Includes inline JS code, wrapes it in <script> tags with CDATA
	 *
	 * @params	string	The content to add as inline JS
	 * @params	string	(optional) Key used for the headerData array. (default: t3lib_div::shortMd5($content))
	 */
	function addJSinline($content, $key='') {
		if(empty($key))
			$key = t3lib_div::shortMd5($content);
		$content = chr(10).'<script type="text/javascript">'.chr(10).'/*<![CDATA[*/'.chr(10) . $content . chr(10).'/*]]>*/'.chr(10).'</script>'.chr(10);
		$this->addHeaderData($content, $key);
	}

	/**
	 * The method returns for you after rendering a Image path
	 * @author Mansoor Ahmad
	 *
	 * @param	string	$damFieldName: The image fieldname in the database tt_content
	 * @param	string	$width: Target width of your Image / "c" stand for crop example '123c'
	 * @param	string	$height: Target height of your Image / "c" stand for crop example '123c'
	 *
	 * @return string	image source path
	 */
	function getImageSrc($damFieldName, $width='', $height='') {
		$getDAMImages	=	$this->getDamImagesFiles($table = 'tt_content', $this->cObj->data['uid'], $damFieldName);
		$imgArray['file'] = $getDAMImages[$damFieldName];
		if($width) {
			$imgArray['file.']['width'] = $width;
		}
		if($height) {
			$imgArray['file.']['height'] = $height;
		}
		return $this->cObj->IMG_RESOURCE($imgArray);
	}

	/**
	 * The method returns for you after rendering a Image
	 * @author Mansoor Ahmad
	 *
	 * @param	string	$damFieldName: The image fieldname in the database tt_content
	 * @param	string	$width: Target width of your Image / "c" stand for crop example '123c'
	 * @param	string	$height: Target height of your Image / "c" stand for crop example '123c'
	 * @param	string	$altTextFieldName: The alternative Text fieldname in the database tt_content
	 * @param	string	$linkFieldName: The link fieldname in the database tt_content
	 *
	 * @return string	Compleate html image source
	 */
	function getImage($damFieldName, $width='', $height='', $altTextFieldName='' , $linkFieldName='') {
		$getDAMImages	=	$this->getDamImagesFiles($table = 'tt_content', $this->cObj->data['uid'], $damFieldName);
		$imgArray['file'] = $getDAMImages[$damFieldName];
		if($width) {
			$imgArray['file.']['width'] = $width;
		}
		if($height) {
			$imgArray['file.']['height'] = $height;
		}
		if($altTextFieldName) {
			$imgArray['altText'] = $this->cObj->data[$altTextFieldName];
		}
		if($linkFieldName) {
			$imgArray['stdWrap.']['typolink.']['parameter'] = $this->cObj->data[$linkFieldName];
		}
		return ($imgArray['file'])?$this->cObj->IMAGE($imgArray):'';
	}

	/**
	 * Resizes an image to a certain size. If the target ratio is
	 * different from the source ratio, then the biggest possible area
	 * with the target ratio is extracted.
	 *
	 * @param	string	Source image-file.
	 * @param	string	Destination, including filename and type.
	 * @param	int		Target width.
	 * @param	int		Target height.
	 */
	function clip($source, $dest, $width, $height) {
		if (file_exists($source) && is_writable(dirname($dest)))
		{
			$this->img->loadImg($source);
			$this->img->clip($width, $height);
			$this->img->save($dest);
		}
	}

	/**
	 * Extracts the specified area from the source image and writes it to the dest image.
	 *
	 * @param	string	Source image-file.
	 * @param	string	Destination, including filename and type.
	 * @param	int		Upper left x-coordinate.
	 * @param	int		Upper left y-coordinate.
	 * @param	int		Lower right x-coordinate.
	 * @param	int		Lower right y-coordinate.
	 */
	function extract($source, $dest, $x1, $y1, $x2, $y2) {
		$this->img->loadImg($source);
		$this->img->extraxt($x1, $y1, $x2, $y2);
		$this->img->save($dest);
	}

	/**
	 * Resizes the source image to the specified size. If no height is specified,
	 * the height will be calculated from the source image ratio.
	 *
	 * @param	string	Source image-file.
	 * @param	string	Destination, including filename and type.
	 * @param	int		Target width.
	 * @param	int		Target height.
	 */
	function resize($source, $dest, $width, $height=0) {
		$this->img->loadImg($source);
		$this->img->resize($width, $height);
		$this->img->save($dest);
	}

	function rotate($source, $dest, $angle, $background=NULL) {
		$this->img->loadImg($source);
		$this->img->rotate($angle, $background);
		$this->img->save($dest);
	}

	/**
	 * The method get image-informations, included with extension DAM, from the Database
	 * @author Caspar Stuebs
	 *
	 *	Returns:
	 *	array (
	 *		'files' => array(
	 *			record-uid => 'fileadmin/example.jpg',
	 *		)
	 *		'rows' => array(
	 *			record-uid => array(meta data array),
	 *		)
	 *	);
	 *
	 * @param	string	$table: The table where the image is included
	 * @param	mixed	$uid: The uid from the table to look for the image. should be one uid, a comma-seperated list of uids, an array of uids or emtpy for all
	 * @param	string	$ident: The fieldname from the table where the images are included, empty for all
	 * @param	mixed	$where: Additional where clause as array or string (key => value)
	 * @param	array	$fields: Additional fields from $table
	 *
	 * @return array	An array with image-informations, ordered by tx_dam_mm_ref.ident, tx_dam_mm_ref.sorting_foreign; false, if DAM is not loaded
	 */
	function getDamImages($table = 'tt_content', $uid = '', $ident = '', $where = array(), $fields = array()) {
		$return = false;
		$addFields = '';

		if (t3lib_extMgm::isLoaded('dam')) {
			if(is_array($uid)) $uid = implode(',', $uid);
			//if(!is_array($where)) $where = array();
			if(!is_array($fields) && !empty($fields) && $fields != 'uid') $addFields = ','.$table.'.'.$fields;
			elseif(is_array($fields) && count($fields)) {
				foreach($fields as $field) {
					if($field != 'uid') $addFields = ','.$table.'.'.$field;
				}
			}

			$return = tx_dam_db::getReferencedFiles($table, $uid, $ident, 'tx_dam_mm_ref', 'tx_dam.*,tx_dam_mm_ref.*'.$addFields, $where, '', 'tx_dam_mm_ref.ident, tx_dam_mm_ref.sorting_foreign');
		}

		return $return;
	}

	/**
	 * The method get the image files, included with extension DAM, from the Database
	 * @author Caspar Stuebs
	 *
	 *	Returns:
	 *	array(
	 *		record-uid => 'fileadmin/example.jpg',
	 *		record-ident => 'fileadmin/example.jpg',
	 *	);
	 * ATTENTION: if there are more then one image in an row (record-ident) only the first one is given back with array_key record-ident
	 *
	 * @param	string	$table: @see getDamImages()
	 * @param	mixed	$uid: @see getDamImages()
	 * @param	string	$ident: @see getDamImages()
	 * @param	array	$where: @see getDamImages()
	 * @param	array	$fields: @see getDamImages()
	 *
	 * @return array	An array with image files, ordered by tx_dam_mm_ref.ident, tx_dam_mm_ref.sorting_foreign; false, if DAM is not loaded
	 */
	function getDamImagesFiles($table = 'tt_content', $uid = '', $ident = '', $where = array(), $fields = array()) {
		$myImages = $this->getDamImages($table, $uid, $ident, $where, $fields);

		foreach($myImages['rows'] as $key => $imageRow) {
			if(empty($myImages['files'][$imageRow['ident']])) $myImages['files'][$imageRow['ident']] = $myImages['files'][$key];
		}

		return ($myImages ? $myImages['files'] : false);
	}

	/**
	 * The method inserts a new dam-reference into the tx_dam_mm_ref table
	 * First it checks, if there are already images for the requested db-entry.
	 * If there are images, it checks the sorting_foreign values (are there entries missing?)
	 * After that it adds the image with the given $uid_dam as new last item and updates the field in the requested $table
	 *
	 * ATTENTION: This function is not tested yet!!!
	 *
	 * @author Caspar Stuebs
	 *
	 * @param	int		$uid_dam: The UID from the Image in the table tx_dam
	 * @param	int		$uid_table: The UID from the table where the image is inserted
	 * @param	string	$table: The table where the image is inserted
	 * @param	string	$ident: The fieldname from the table where the image is inserted
	 *
	 * @return boolean	true, if insert was successfull
	 */
	function putDamImageReference($uid_dam, $uid_table, $table, $field) {
		// creating $where_string and looking for existing images in requested $table entry, ordered by sorting_foreign
		$whereString = 'uid_foreign = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($uid_table, 'tx_dam_mm_ref').' AND tablenames = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($table,'tx_dam_mm_ref').' AND ident = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($field,'tx_dam_mm_ref');
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam_mm_ref', $whereString, '', 'sorting_foreign');
		// if there are already images
		if($count = $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			// check for correct sorting_foreign values, update them if not
			$checkSorting = 0;
			$updateCheck = true;
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// check each entry; if there was an update error, skip following updates
				if($row['sorting_foreign'] != ++$checkSorting && $updateCheck) {
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam_mm_ref', $whereString.' AND sorting_foreign = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($row['sorting_foreign'],'tx_dam_mm_ref'), array('sorting_foreign' => $check_sorting));
					$updateCheck = ((boolean)$GLOBALS['TYPO3_DB']->sql_affected_rows() && $updateCheck);
				}
				// if there was an update-error, use the highest sorting_foreign value +1 for new entry
				if(!$updateCheck) $sortingNew = $row['sorting_foreign']+1;
			}
		}

		// if there was no update-error, use $count+1 for sorting_foreign in new entry
		if($updateCheck) $sortingNew = $count+1;
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_mm_ref', array('uid_local' => $uid_dam, 'uid_foreign' => $uid_table, 'tablenames' => $table, 'ident' => $field, 'sorting_foreign' => $updateCheck));

		// if insert was successfull, update foreign table with new count of images
		if($insertSuccess = (boolean)$GLOBALS['TYPO3_DB']->sql_affected_rows()) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($uid_table, $table), array($field => ($count+1)));
		}

		return $insertSuccess;
	}

	/**
	 * An exec_SELECTquery wrapper which automatically generates the limit parameter according
	 * to $this->currentPage and $this->itemsPerPage.
	 *
	 * @param	string		List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @param	string		Table(s) from which to select. This is what comes right after "FROM ...". Required value.
	 * @param	string		Optional additional WHERE clauses put in the end of the query. NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself! DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
	 * @param	string		Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		Optional ORDER BY field(s), if none, supply blank string.
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function pagingSelect($select_fields, $from_table, $where_clause, $groupBy='', $orderBy='') {
		$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('1', $from_table, $where_clause);
		$rows = mysql_num_rows($query);
		$this->totalPages = ceil($rows/$this->itemsPerPage);

		$limit = ($this->currentPage-1)*$this->itemsPerPage.','.$this->itemsPerPage;
		return $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
	}

	/**
	 * Generates a filename for a temporary file in typo3temp using the source filename and the file modification
	 * time, therefore the temporary name returned is always the same as long as the source file does not change.
	 * A plugin-wide subdirectory may be specified using $this->tempSubdir.
	 *
	 * @param		file		Source filename from which the temporary filename is generated.
	 * @param		boolean	Specifies if the returned path should be absolute or relative to the Typo3-root.
	 * @param		string	A string added to the hash. Useful for example, if you want to generate multiple temporary images widht different dimensions from the same source.
	 * @param		boolean	if true (default), unlink file if exists...
	 *
	 * @return	string	Typo3temp filepath if there was no error and the source filename is valid, otherwise false.
	 */
	function tempname($file, $absolute=false, $additional='', $unlink=true) {
		if(!file_exists($file)) {
			return false;
		}

		$tempDir = 'typo3temp/'.$this->tempSubdir;
		if(!is_dir(PATH_site.$tempDir)) {
			mkdir(PATH_site.$tempDir);
		}

		$filename = basename($file);
		$ext = substr($filename, strrpos($filename, '.'));
		$tempName = t3lib_div::shortMd5($filename.filemtime(PATH_site.$file).$additional).$ext;

		if($unlink && file_exists($tempName)) unlink($tempName);

		return ($absolute ? PATH_site : '').$tempDir.$tempName;
	}

	/**
	 * Generates a filename for a temporary file in typo3temp, using the md5-hashed $string with the specified
	 * extension.
	 *
	 * @param		string
	 * @param		string	Extension for the tempfile (without leading dot).
	 * @param		boolean	Specifies if the returned path should be absolute or relative to the Typo3-root.
	 * @param		boolean	if true (default), unlink file if exists...
	 *
	 * @return	string	Typo3temp filepath
	 */
	function stringTempname($string, $ext, $absolute=false, $unlink=true) {
		$tempDir = 'typo3temp/'.$this->tempSubdir;
		if(!is_dir(PATH_site.$tempDir)) {
			mkdir(PATH_site.$tempDir);
		}

		$tempName = t3lib_div::shortMd5($string).'.'.$ext;

		if($unlink && file_exists($tempName)) unlink($tempName);

		return ($absolute ? PATH_site : '').$tempDir.$tempName;
	}

	/**
	 * Converts an array to Json
	 * @author Jannik Theiß
	 *
	 * @param	array	$arr: aray, that will be converted to json
	 * @param	boolean	$wrapInBrakets: if true, the whole output will be wrapped in simple brakets
	 *
	 * @return string	the converted array
	 */
	function arrayToJson($arr, $wrapInBrakets = false) {
		if(function_exists('json_encode')) {
			return json_encode($arr);
		}
		else {
			if (is_array($arr)) {
				if ($wrapInBrakets) {
					$json = '(';
					$json .= $this->arrayToJson($arr, false);
					$json .= ')';
				} else {
					$json = '{';
					foreach ($arr as $key => $value) {
						if (is_array($value)) {
							$json .= '"' . $key . '":' . $this->arrayToJson($value, false) . ',';
						} else {
							$json .= '"' . $key . '":"' . $value . '",';
						}
					}
					$json = rtrim($json, ',');
					$json .= '}';
				}
			} else {
				$json = false;
			}
			return $json;
		}
	}

	/**
	 * This method makes piVars from the given Array
	 * @author Caspar Stuebs
	 *
	 * @param array		$piVarArray: the array to convert to piVars
	 *
	 * @return array	the array with piVars
	 */
	function makePiVars(array $piVarArray) {
		if(!is_array($piVarArray)) {
			$return = FALSE;
		}
		else {
			$return = array();
			foreach($piVarArray as $key => $value) {
				$return[$this->prefixId.'['.$key.']'] = $value;
			}
		}
		return $return;
	}
	
	/*
	 * makes a piVar from a name
	 * @author Caspar Stuebs <caspar@gosign.de>
	 *
	 * @param	string	$name: the name of the piVar
	 *
	 * @return string 	the piVar
	 */
	function makePiVar($name) {
		return $this->prefixId . '[' . $name . ']';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_pibase/class.tx_gopibase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/go_pibase/class.tx_gopibase.php']);
}

?>
