<?php

return [
    /**
     * DB Column name from model
     */
    'column_name' => 'role',

    /**
     * Roles with permission as path
     *
     * - `+` Wildcard one level
     * - `#` Wildcard everything following
     * - `!` Before the permission - prohibits permission
     *
     * 'admin' => [
     *     'users/#',
     *     'users/+/field',
     *     '!users/create'
     * ]
     */
    'roles' => [],

];
