<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Thomas Loeffler <loeffler@spooner-web.de>
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

class user_usersetup_hook {

		/**
		 * Hook-function: inject additional JS code and a flash message
		 * called in typo3/template.php:template->startPage
		 *
		 * @param  $params
		 * @param  $parentObj
		 */
	public function preStartPageHook($params, &$parentObj) {
		global $BE_USER;
		if ($parentObj->scriptID == 'ext/setup/mod/index.php') { // execute only in user setup module

				// get configuration of a secure password
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['be_secure_pw']);

				// add configuration for JS function in json format
			$parentObj->JScodeArray['be_secure_pw_inline'] = 'var beSecurePwConf = '.json_encode($extConf);

				// add JS code for password validation
			$parentObj->JScode .= '<script type="text/javascript" src="'.$parentObj->backPath.'../typo3conf/ext/be_secure_pw/res/js/passwordtester.js"></script>';

				// get the languages from ext
			$LANG = t3lib_div::makeInstance('language');
			$LANG->init($BE_USER->uc['lang']);
			$LANG->includeLLFile('EXT:be_secure_pw/res/lang/locallang.xml');

				// how many parameters have to be checked
			$toCheckParams = array('lowercaseChar', 'capitalChar', 'digit', 'specialChar');
			$checkParameter = array();
			foreach ($toCheckParams as $parameter) {
				if ($extConf[$parameter] == 1) {
					$checkParameter[] = $LANG->getLL($parameter);
				}
			}

				// flash message with instructions for the user
			$flashMessage = t3lib_div::makeInstance(
				't3lib_FlashMessage',
				sprintf($LANG->getLL('beSecurePw.description'), $extConf['passwordLength'], implode(', ', $checkParameter), $extConf['patterns']),
				$LANG->getLL('beSecurePw.header'),
				t3lib_FlashMessage::INFO,
				TRUE
			);
			t3lib_FlashMessageQueue::addMessage($flashMessage);
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/be_secure_pw/classes/class.user_usersetup_hook.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/be_secure_pw/classes/class.user_usersetup_hook.php']);
}

?>