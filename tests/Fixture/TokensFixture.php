<?php
declare(strict_types=1);

/**
 * This file is part of me-cms.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

namespace MeCms\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TokensFixture
 */
class TokensFixture extends TestFixture
{
    /**
     * @var array<string, mixed>
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => null, 'autoIncrement' => true],
        'user_id' => ['type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'autoIncrement' => null],
        'token' => ['type' => 'string', 'length' => 25, 'null' => false, 'default' => null],
        'type' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null],
        'extra' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null],
        'expiry' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        '_indexes' => [
            'user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'type' => ['type' => 'index', 'columns' => ['type'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ];
}
