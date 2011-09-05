<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Marcus Krause (marcus#expYYYY@t3sec.info)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Contains the class "t3lib_encryption" with encryption purpose methods.
 *
 * $Id$
 *
 * @author	Marcus Krause <marcus#expYYYY@t3sec.info>
 * @since   2009-03-08
 */


/**
 * TO BE FILLED.
 *
 * @author	    Marcus Krause <marcus#expYYYY@t3sec.info>
 * @package     TYPO3
 * @subpackage  t3lib
 */
class t3lib_symencryption implements t3lib_Singleton {

	/**
	 * Keeps array of blacklisted ciphers
	 * intented not to be used.
	 *
	 * @var array
	 */
	var $blacklistedCiphers = array();

	/**
	 * Keeps array of supported ciphers
	 * intented to be used.
	 *
	 * @var array
	 */
	var $supportedCiphers = array();

	/**
	 * Keeps directory to retrieve ciphers from.
	 *
	 * @var string
	 */
	var $cipherDir = '';

	/**
	 * Keeps default cipher to be used.
	 *
	 * @var string
	 */
	var $defaultCipher;

	/**
	 * Keeps array of blacklisted modes
	 * intented not to be used.
	 *
	 * @var array
	 */
	var $blacklistedModes = array();

	/**
	 * Keeps array of supported modes
	 * intented to be used.
	 *
	 * @var array
	 */
	var $supportedModes = array();

	/**
	 * Keeps directory to retrieve modes from.
	 *
	 * @var string
	 */
	var $modeDir = '';

	/**
	 * Keeps default mode to be used.
	 *
	 * @var string
	 */
	var $defaultMode;

	/**
	 * Keeps currently used cipher.
	 *
	 * @var string
	 */
	var $currentCipher = null;

	/**
	 * Keeps currently used mode.
	 *
	 * @var string
	 */
	var $currentMode = null;

	/**
	 * Keeps currently used initialisation vector size.
	 *
	 * @var integer
	 */
	var $currentIVSize = 0;

	/**
	 * Keeps currently used initialisation vector;
	 *
	 * @var string
	 */
	var $currentIV = null;


	/**
	 * Class constructor.
	 *
	 * If fallback mode is enabled, any (first) cipher which
	 * is supported will be used. Ommitting the parameter is
	 * encouraged and will enabling the default mode (standard).
	 *
	 * @access public
	 * @param  boolean (optional) fallback mode setting
	 * @param  string  (optional) directory to retrieve ciphers from
	 * @param  string  (optional) directory to retrieve modes from
	 * @return mixed   instance of object t3lib_encryption if mcrypt library is
	 *                 available, otherwise boolean false
	 */
	public function __construct ( $fallback = false, string $cipherDir = null, string $modeDir = null ) {
		$success = $this;

		if (!defined('MCRYPT_ENCRYPT')) $success = false;
		if (!is_bool($fallback)) $success = false;

			// enabling blacklists
		if ($success) {
			$this->blacklistedCiphers[] = MCRYPT_RIJNDAEL_128;
			$this->blacklistedCiphers[] = MCRYPT_RIJNDAEL_192;

			$this->blacklistedModes[] =   MCRYPT_MODE_ECB;
			$this->blacklistedModes[] =   MCRYPT_MODE_OFB;
			$this->blacklistedModes[] =   MCRYPT_MODE_STREAM;
		}

			// set default settings
		if ($success) {
			$this->defaultCipher = MCRYPT_BLOWFISH;
			$this->defaultMode =   MCRYPT_MODE_CBC;

			if ($cipherDir)  $this->cipherDir = $cipherDir;
			if ($modeDir)    $this->modeDir = $modeDir;
			$this->getSupportedCiphers();
		}

			// apply settings
		if ($success && !$fallback) {
			$success = $this->setDefault();
		} else if ($success && $fallback && empty($this->supportedCiphers)) {
			$cipher = array_shift($this->supportedCiphers);
			$success = $this->setCipher($cipher);
		}
		return $success;
	}


	/**
	 *
	 * @param string secret key
	 * @param string secret message
	 * @return unknown_type
	 */
	public function encrypt ($key, $msg) {
		$success = true;
		$encryptedMsg = null;

		if (!$this->hasPresets())  {
			$success = $this->setDefault();
		}
		if ($success && is_string($key) && is_string($msg)) {
			$td = mcrypt_module_open(	$this->currentCipher,
										$this->cipherDir,
										$this->currentMode,
										$this->modeDir);
			mcrypt_generic_init($td,
								$key,
								$this->currentIV);
			$encryptedMsg = mcrypt_generic(	$td,
											$msg);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
		}
		return $encryptedMsg;
	}

	public function decrypt($key, $encMsg) {
		$success = true;
		$decryptedMsg = null;

		if (!$this->hasPresets())  {
			$success = $this->setDefault();
		}
		if ($success && is_string($key) && is_string($encMsg)) {
			$td = mcrypt_module_open(	$this->currentCipher,
										$this->cipherDir,
										$this->currentMode,
										$this->modeDir);
			mcrypt_generic_init($td,
								$key,
								$this->currentIV);
			$decryptedMsg = trim(mdecrypt_generic($td,
											$encMsg));
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
		}
		return $decryptedMsg;
	}

	/**
	 * Returns currently used cipher.
	 *
	 * @return  string  name of current cipher if set, otherwise null
	 */
	public function getCurrentCipher () {
		return $this->currentCipher;
	}

	/**
	 * Returns currently used mode.
	 *
	 * @return  string  name of current mode if set, otherwise null
	 */
	public function getCurrentMode () {
		return $this->currentMode;
	}

	/**
	 * Returns currently used initialisation vector.
	 *
	 * Note: An initialisation vector (IV) is only needed for specific modes.
	 * This IV does not need to be secret at all, though it can be desirable.
	 * You even can send it along with your ciphertext without losing security.
	 *
	 * @return  string  IV as byte string (non-printable chars) if set, otherwise null
	 */
	public function getCurrentIV () {
		return $this->currentIV;
	}

	/**
	 * Returns currently used initialisation vector size in bytes.
	 *
	 * Note: An initialisation vector (IV) is only needed for specific modes.
	 *
	 * @return  integer  value > 0 if set, otherwise 0
	 */
	public function getCurrentIVSize () {
		return $this->currentIVSize;
	}

	/**
	 * Returns the default cipher.
	 *
	 * @return  string  default cipher
	 */
	public function getDefaultCipher () {
		return $this->defaultCipher;
	}

	/**
	 * Returns the default mode.
	 *
	 * @return  string  default mode
	 */
	public function getDefaultMode () {
		return $this->defaultMode;
	}

	/**
	 * Returns an array of supported ciphers.
	 *
	 * @param   string  (optional) directory to retrieve algorithms from
	 * @return  array   supported ciphers
	 */
	public function getSupportedCiphers ( $cipherDir = null ) {
		if (empty($this->supportedCiphers)) {
			if ($cipherDir) {
				$this->cipherDir = $cipherDir;
			} else if (!$this->cipherDir) {
				$this->cipherDir = ini_get('mcrypt.algorithms_dir');
			}
			$ciphers = mcrypt_list_algorithms( $this->cipherDir );

				// test ciphers;
				// although reported as supported, some ciphers are actually not
			$currentCipher = $this->currentCipher;
			foreach (array_diff($ciphers, $this->blacklistedCiphers) as $cipher) {
				if ($this->setCipher($cipher))  $this->supportedCiphers[] = $cipher;
			}
			$this->currentCipher = $currentCipher;
		}
		return $this->supportedCiphers;
	}

	/**
	 * Returns supported key lengths for given cipher and mode.
	 *
	 * @return  mixed  array of discrete supported key lengths or integer with maximum key key length
	 */
	public function getSupportedKeyLengths () {
		$supportedKeyLengths = array();
		$ed = @mcrypt_module_open(
				$this->currentCipher ? $this->currentCipher : $this->defaultCipher,
				$this->cipherDir ? $this->cipherDir : ini_get('mcrypt.algorithms_dir'),
				$this->currentMode ? $this->currentMode : $this->defaultMode,
				$this->modeDir ? $this->modeDir : ini_get('mcrypt.modes_dir'));
		if ($ed !== false) {
			$supportedKeyLengths = mcrypt_enc_get_supported_key_sizes($ed);
			if (empty($supportedKeyLengths)) {
				$supportedKeyLengths = mcrypt_enc_get_key_size($ed);
			}
		}
		return $supportedKeyLengths;
	}

	/**
	 * Returns an array of supported modes.
	 *
	 * @param   string  (optional) directory to retrieve modes from
	 * @return  array   supported modes
	 */
	public function getSupportedModes ( $modeDir = null ) {
		if (empty($this->supportedModes)) {
			if ($modeDir) {
				$this->modeDir = $modeDir;
			} else if (!$this->modeDir) {
				$this->modeDir = ini_get('mcrypt.modes_dir');
			}
			$modes = mcrypt_list_modes( $this->modeDir );
			$this->supportedModes = array_diff($modes, $this->blacklistedModes);
		}
		return $this->supportedModes;
	}

	/**
	 * Returns information if mandatory settings are done.
	 *
	 * @return  boolean  true, if mandatory settings are done, otherwise false
	 */
	protected function hasPresets () {
		$presets = false;

		if ($this->currentCipher && $this->currentMode) {
			$presets = true;
		}
		return $presets;
	}

	/**
	 * Returns information if a given value is a binary string.
	 *
	 * @param   string   value to check
	 * @return  boolean  true, if string is a binary one, otherwise false
	 */
	protected function isBinary ( $value ) {
		$isBinary = false;

		if (is_string($value) && !empty($value)) {
				// removing CR and LF
			$value = str_replace(array(chr(10), chr(13)), '', $value);
			$isBinary = !ctype_print($value);
		}
		return $isBinary;
	}

	/**
	 * Returns information if a given value is a hexadecimal string.
	 *
	 * @param   string   value to check
	 * @return  boolean  true if value is a hexadecimal one, otherwise false
	 */
	protected function isHex ( $value ) {
		$isHex = false;

		if (is_string($value)
			&& !empty($value)
			&& preg_match('/^[0-9a-f]+$/i', $value)) {
			$isHex = true;
		}
		return $isHex;
	}

	/**
	 * Resets IV and its size.
	 */
	protected function resetIV () {
		$this->currentIVSize = 0;
		$this->currentIV = null;
	}

	/**
	 * Sets default cipher and mode to be used.
	 *
	 * @return  boolean  true if all default values could be applied, otherwise false
	 */
	protected function setDefault () {
		$success = true;

		if ($success)  $success = $this->setCipher( $this->defaultCipher );
		if ($success)  $success = $this->setMode( $this->defaultMode );

		return $success;
	}

	/**
	 * Sets cipher to be used for encryption.
	 *
	 * @param   string   name or MCRYPT_ciphername constant of cipher to be used
	 * @return  boolean  true, if supplied cipher is usable, otherwise false
	 */
	public function setCipher ( $cipher ) {
		$isValidCipher = false;

		if(!in_array($cipher, $this->blacklistedCiphers)) {
			$ed = @mcrypt_module_open(
					$cipher,
					$this->cipherDir ? $this->cipherDir : ini_get('mcrypt.algorithms_dir'),
					$this->currentMode ? $this->currentMode : $this->defaultMode,
					$this->modeDir ? $this->modeDir : ini_get('mcrypt.modes_dir'));
			if ($ed !== false)  {
				@mcrypt_module_close( $ed );
				$this->currentCipher = $cipher;
				$this->resetIV();
				$this->setIV();
				$isValidCipher = true;
			}
		}
		return $isValidCipher;
	}

	/**
	 * Sets initialisation vector to be used for encryption.
	 *
	 * Note: An initialisation vector (IV) is only needed for specific modes.
	 * (necessary in CFB and OFB mode, possible in CBC mode)
	 * This IV does not need to be secret at all, though it can be desirable.
	 * You even can send it along with your ciphertext without losing security.
	 * So please don't use the TYPO3 encryption key as IV.
	 *
	 * @param   string   (optional) IV as byte string
	 * @return  boolean  false, if IV is not needed for current mode or if it's not usable, otherwise true
	 */
	public function setIV ( $iv = null ) {
		$success = false;

			// missing current cipher or mode
			// determining iv size impossible
		if (!$this->currentCipher || !$this->currentMode)  return $success;

		$ivSize = mcrypt_get_iv_size($this->currentCipher, $this->currentMode);
			// current mode needs IV
		if ($ivSize > 0) {
			$this->currentIVSize = $ivSize;

			if ($this->isBinary($iv) && strlen($iv) == $ivSize) {
				$this->currentIV = $iv;
				$success = true;
			} else if (!$iv) {
				$this->currentIV = t3lib_div::generateRandomBytes($ivSize);
				$success = true;
			}
		}
		return $success;
	}

	/**
	 * Sets mode to be used for encryption.
	 *
	 * @param   string   name or MCRYPT_mode constant of cipher to be used
	 * @return  boolean true, if supplied mode is usable, otherwise false
	 */
	public function setMode ( $mode ) {
		$isValidMode = false;

		if(in_array($mode, $this->getSupportedModes())) {
			$ed = @mcrypt_module_open(
					$this->currentCipher ? $this->currentCipher : $this->defaultCipher,
					$this->cipherDir ? $this->cipherDir : ini_get('mcrypt.algorithms_dir'),
					$mode,
					$this->modeDir ? $this->modeDir : ini_get('mcrypt.modes_dir'));
			if ($ed !== false)  {
				@mcrypt_module_close( $ed );
				$this->currentMode = $mode;
				$this->resetIV();
				$this->setIV();
				$isValidMode = true;
			}
		}
		return $isValidMode;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/security/class.t3lib_symencryption.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/security/class.t3lib_symencryption.php']);
}
?>