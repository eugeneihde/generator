<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Generator',
        'Activitymanager',
        [
            \Generator\Generator\Controller\ActivityController::class => 'list, show, new, create, edit, update, delete, generate'
        ],
        // non-cacheable actions
        [
            \Generator\Generator\Controller\ActivityController::class => 'list, show, new, create, edit, update, delete, generate'
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    activitymanager {
                        iconIdentifier = generator-plugin-activitymanager
                        title = LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_activitymanager.name
                        description = LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_activitymanager.description
                        tt_content_defValues {
                            CType = list
                            list_type = generator_activitymanager
                        }
                    }
                }
                show = *
            }
       }'
    );
})();
