<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_generator_domain_model_activity', 'EXT:generator/Resources/Private/Language/locallang_csh_tx_generator_domain_model_activity.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_generator_domain_model_activity');
})();
