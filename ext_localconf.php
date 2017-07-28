<?php
defined('TYPO3_MODE') || die('Access denied.');

$boot = function($extensionKey) {

    // here we register "PasswordEvaluator"
    // for editing by tca form
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\SpoonerWeb\BeSecurePw\Evaluation\PasswordEvaluator::class] =
        'EXT:be_secure_pw/Classes/Evaluation/PasswordEvaluator.php';

    // Information in user setup module
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/setup/mod/index.php']['modifyUserDataBeforeSave']['be_secure_pw'] =
        \SpoonerWeb\BeSecurePw\Hook\UserSetupHook::class . '->modifyUserDataBeforeSave';

    // password reminder
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/backend.php']['constructPostProcess']['be_secure_pw'] =
        \SpoonerWeb\BeSecurePw\Hook\BackendHook::class . '->constructPostProcess';

    // Set timestamp for last password change
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['be_secure_pw'] =
        \SpoonerWeb\BeSecurePw\Hook\BackendHook::class;

    $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]);

    // execution of is hook only needed in backend, but it is in the abstract class and could also be executed
    // from frontend otherwise if the backend is set to adminOnly, we can not enforce the change,
    // because the hook removes the admin flag
    if (!empty($extConf['forcePasswordChange']) && TYPO3_MODE === 'BE'
        && (int)$GLOBALS['TYPO3_CONF_VARS']['BE']['adminOnly'] === 0) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][] =
            \SpoonerWeb\BeSecurePw\Hook\RestrictModulesHook::class . '->addRefreshJavaScript';

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] =
            \SpoonerWeb\BeSecurePw\Hook\RestrictModulesHook::class . '->postUserLookUp';
    }

};

$boot($_EXTKEY);
unset($boot);
