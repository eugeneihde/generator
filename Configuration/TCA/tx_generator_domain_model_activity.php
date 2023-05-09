<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity',
        'label' => 'date',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
        ],
        'searchFields' => 'designation,description,category',
        'iconfile' => 'EXT:generator/Resources/Public/Icons/tx_generator_domain_model_activity.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'date, designation, description, category, trainee, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_generator_domain_model_activity',
                'foreign_table_where' => 'AND {#tx_generator_domain_model_activity}.{#pid}=###CURRENT_PID### AND {#tx_generator_domain_model_activity}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.date',
            'description' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.date.description',
            'config' => [
                'dbType' => 'date',
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date,required',
                'default' => null,
            ],
        ],
        'designation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.designation',
            'description' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.designation.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.description',
            'description' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.description.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ],
        ],
        'category' => [
            'exclude' => true,
            'label' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.category',
            'description' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.category.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'trainee' => [
            'exclude' => true,
            'label' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.trainee',
            'description' => 'LLL:EXT:generator/Resources/Private/Language/locallang_db.xlf:tx_generator_domain_model_activity.trainee.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            
        ],
    
    ],
];
