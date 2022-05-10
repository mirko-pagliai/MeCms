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
 * TagsFixture
 */
class TagsFixture extends TestFixture
{
    /**
     * @var array<string, mixed>
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => null, 'autoIncrement' => true],
        'tag' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null],
        'post_count' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => '0', 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ];

    /**
     * @var array
     */
    public $records = [
        [
            'tag' => 'cat',
            'post_count' => 4,
            'created' => '2016-12-29 11:13:31',
            'modified' => '2016-12-29 11:13:31',
        ],
        [
            'tag' => 'dog',
            'post_count' => 2,
            'created' => '2016-12-29 11:14:31',
            'modified' => '2016-12-29 11:14:31',
        ],
        [
            'tag' => 'bird',
            'post_count' => 1,
            'created' => '2016-12-29 11:15:31',
            'modified' => '2016-12-29 11:15:31',
        ],
        [
            'tag' => 'lion',
            'post_count' => 1,
            'created' => '2016-12-29 11:16:31',
            'modified' => '2016-12-29 11:16:31',
        ],
    ];
}
