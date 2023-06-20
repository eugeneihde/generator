<?php

declare(strict_types=1);

return [
    \Generator\Generator\Domain\Model\Trainee::class => [
        'tableName' => 'fe_users',
        
    ],
    \Generator\Generator\Domain\Model\Activity::class => [
        'properties' => [
            'date' => [
                'fieldName' => 'timestamp',
            ],
        ],  
    ],
];
