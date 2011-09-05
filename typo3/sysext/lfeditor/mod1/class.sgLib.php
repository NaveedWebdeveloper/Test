<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Stefan Galinski (stefan.galinski@frm2.tum.de)
*  All rights reserved
*
*  The script is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * personal library with lots of useful methods
 *
 * $Id: class.sgLib.php 95 2006-08-26 18:38:14Z fire $
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 */

/**
 * personal library with lots of useful methods
 *
 * @author Stefan Galinski <stefan.galinski@frm2.tum.de>
 * @package sgLib
 */
class sgLib {
	###############################
	######## http functions #######
	###############################

	/**
	 * forces download of a file
	 *
	 * @param string download file or data string
	 * @param string download filename
	 * @param string type of file
	 * @return void
	 */
	public static function download($file, $filename, $type='x-type/octtype')
	{
		if(is_file($file))
			$content = readfile($file);
		else
			$content = $file;

		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Pragma: public"); // needed for IE
		header('Content-Type: ' . $type);
		header('Content-Length: ' . strlen($content));
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		echo $content;
	}

	/**
	 * sends email with php multibyte functions
	 *
	 * @throws Exception raised if something failed
	 * @param string subject
	 * @param string text
	 * @param string from header
	 * @param string email address
	 * @param string file attachement name or data string (optional)
	 * @param string send filename (optional)
	 * @param string language (default == unicode)
	 * @return void
	 */
	public static function sendMail($subject, $text, $fromAddress, $toAddress,
		$attachement='', $sendFileName='', $mbLanguage='uni')
	{
		// checks
		if(!preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $toAddress) &&
			!preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/',
			$toAddress))
			throw new Exception('email address isnt valid: ' . $toAddress);

		if(!preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $fromAddress) &&
			!preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/',
			$fromAddress))
			throw new Exception('email address isnt valid: ' . $fromAddress);

		// prepare data
		$text = htmlspecialchars($text);
		$subject = htmlspecialchars($subject);
		if(is_file($attachement))
			$fileContent = readfile($attachement);
		else
			$fileContent = $attachement;

		// prepare header
		$boundary = md5(uniqid(time()));
		$header = 'From: ' . $fromAddress . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n";
		if(!empty($fileContent)) {
			$header .= 'MIME-Version: 1.0' . "\r\n";
			$header .= 'Content-Type: multipart/mixed; boundary=' . $boundary . "\r\n\r\n";
			$header .= '--' . $boundary . "\r\n";
			$header .= 'Content-Type: text/plain' . "\r\n";
			$header .= 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n";
			$header .= $text . "\r\n";
			$header .= '--' . $boundary . "\r\n";
			$header .= 'Content-Type: Application/Octet-Stream; name=' . $sendFileName . "\r\n";
			$header .= 'Content-Transfer-Encoding: base64' . "\r\n";
			$header .= 'Content-Disposition: attachment; filename=' . $sendFileName . "\r\n\r\n";
			$header .= chunk_split(base64_encode($fileContent));
			$header .= "\r\n";
			$header .= '--' . $boundary . '--';

			$text = '';
		}

		// send mail
		if(!mb_language($mbLanguage))
			throw new Exception('mb_language reported an error: "' . $mbLanguage . '"');
		if(!mb_send_mail($toAddress, $subject, $text, $header))
			throw new Exception('mail couldnt be sended to: ' . $toAddress);
	}

	#################################
	######## string functions #######
	#################################

	/**
	 * trims some string from an given path
	 *
	 * @param string string part to delete
	 * @param string some path
	 * @param string some prefix for the new path
	 * @return string new path
	 */
	public static function trimPath($replace, $path, $prefix='')
	{
		return trim(str_replace($replace, '', $path), '/') . $prefix;
	}

	#####################################
	######## filesystem functions #######
	#####################################

	/**
	 * reads the extension of a given filename
	 *
	 * @param string filename
	 * @return string extension of a given filename
	 */
	public static function getFileExtension($file)
	{
		return substr($file, strrpos($file, '.') + 1);
	}

	/**
	 * replaces the file extension in a given filename
	 *
	 * @param string new file extension
	 * @param string filename
	 * @return string new filename
	 */
	public static function setFileExtension($type, $file)
	{
		return substr($file, 0, strrpos($file, '.')+1) . $type;
	}

	/**
	 * checks write permission of a given file (checks directory permission if file doesnt exists)
	 *
	 * @param string file path
	 * @return boolean true or false
	 */
	public static function checkWritePerms($file)
	{
		if(!is_file($file))
			$file = dirname($file);

		if(!is_writable($file))
			return false;

		return true;
	}

	/**
	 * deletes given files
	 *
	 * @throws Exception raised, if some files cant be deleted (throwed after deletion of all)
	 * @param array files
	 * @return void
	 */
	public static function deleteFiles($files)
	{
		// delete all old files
		$error = array();
		foreach($files as $file)
			if(is_file($file))
				if(!unlink($file))
					$error[] = $file;

		if(count($error))
			throw new Exception('following files cant be deleted: "' . implode(', ', $error) . '"');
	}

	/**
	 * creates a full path (all nonexistent directories will be created)
	 *
	 * @throws Exception raised if some path token cant be created
	 * @param string full path
	 * @param string protected path (i.e. /var/www -- needed for basedir restrictions)
	 * @return void
	 */
	public static function createDir($path, $protectArea)
	{
		unset($tmp);
		if(!is_dir($path))
		{
			$path = explode('/', sgLib::trimPath($protectArea, $path));
			foreach($path as $dir)
			{
				$tmp .= $dir . '/';
				if(is_dir($protectArea . $tmp))
					continue;

				if(!mkdir($protectArea . $tmp))
					throw new Exception('path "' . $protectArea . $tmp . '" cant be deleted');
			}
		}
	}

	/**
	 * deletes a directory (all subdirectories and files will be deleted)
	 *
	 * @throws Exception raised if a file or directory cant be deleted
	 * @param string full path
	 * @return void
	 */
	public static function deleteDir($path)
	{
		if(!$dh = @opendir($path))
			throw new Exception('directory "' . $path . '" cant be readed');

		while($file = readdir($dh))
		{
			$myFile = $path . '/' . $file;

			// ignore links and point directories
			if(preg_match('/\.{1,2}/', $file) || is_link($myFile))
				continue;

			if(is_file($myFile))
			{
				if(!unlink($myFile))
					throw new Exception('file "' . $myFile . '" cant be deleted');
			}
			elseif(is_dir($myFile))
				deldir($myFile);
		}
		closedir($dh);

		if(!@rmdir($path))
			throw new Exception('directory "' . $path . '" cant be deleted');
	}

	/**
	 * searches defined files in a given path recursivly
	 *
	 * @throws Exception raised if the search directory cant be read
	 * @param string search in this path
	 * @param string optional: regular expression for files
	 * @param integer optional: maximum search in this depth (0 is infinite)
	 * @return void
	 */
	public static function searchFiles($path, $searchRegex='', $pathDepth=0)
	{
		if(!$fhd = @opendir($path))
			throw new Exception('directory "' . $path . '" cant be read');

		$fileArray = array();
		while($file = readdir($fhd))
		{
			$filePath = $path . '/' . $file;

			// ignore links and point directories
			if(preg_match('/^\.{1,2}$/', $file) || is_link($filePath))
				continue;

			// save path to file or continue
			if(is_file($filePath))
			{
				if(empty($searchRegex))
					$fileArray[] = $filePath;
				elseif(preg_match($searchRegex, $file))
					$fileArray[] = $filePath;

				continue;
			}

			// breakpoint, if pathDepth is reached
			if($pathDepth <= 1 && $pathDepth != 0)
				continue;

			// next dir
			if(is_dir($filePath))
				$fileArray = array_merge($fileArray,
					sgLib::searchFiles($filePath, $searchRegex, $pathDepth-1));
		}
		closedir($fhd);

		return $fileArray;
	}
}
?>
