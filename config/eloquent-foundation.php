<?php

declare(strict_types=1);

return [
    'user_model' => \App\Models\User::class,

    'columns' => [
        'creator' => 'created_by',
        'updater' => 'updated_by',
        'deleter' => 'deleted_by',
        'owner' => 'owner_id',
    ],

    'sync_updater_on_create' => false,
];
