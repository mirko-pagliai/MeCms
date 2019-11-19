<?php

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
 * PostsCategoriesFixture
 */
class PostsCategoriesFixture extends TestFixture
{
    /**
     * Fields
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => null, 'autoIncrement' => true],
        'parent_id' => ['type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'autoIncrement' => null],
        'lft' => ['type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'autoIncrement' => null],
        'rght' => ['type' => 'integer', 'length' => 11, 'null' => true, 'default' => null, 'autoIncrement' => null],
        'title' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null],
        'slug' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null],
        'description' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null],
        'post_count' => ['type' => 'integer', 'length' => 11, 'null' => false, 'default' => '0', 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ];

    /**
     * Records
     * @var array
     */
    public $records = [
        [
            'parent_id' => null,
            'lft' => 1,
            'rght' => 6,
            'title' => 'First post category',
            'slug' => 'first-post-category',
            'description' => 'Description of first category',
            'post_count' => 1,
            'created' => '2016-12-26 21:24:32',
            'modified' => '2016-12-26 21:24:32',
        ],
        [
            'parent_id' => null,
            'lft' => 7,
            'rght' => 8,
            'title' => 'Another post category',
            'slug' => 'another-post-category',
            'description' => 'Description of another category',
            'post_count' => 0,
            'created' => '2016-12-26 21:25:32',
            'modified' => '2016-12-26 21:25:32',
        ],
        [
            'parent_id' => 1,
            'lft' => 2,
            'rght' => 5,
            'title' => 'Sub post category',
            'slug' => 'sub-post-category',
            'description' => 'Description of sub category',
            'post_count' => 0,
            'created' => '2016-12-26 21:26:32',
            'modified' => '2016-12-26 21:26:32',
        ],
        [
            'parent_id' => 3,
            'lft' => 3,
            'rght' => 4,
            'title' => 'Sub sub post category',
            'slug' => 'sub-sub-post-category',
            'description' => 'Description of sub sub category',
            'post_count' => 2,
            'created' => '2016-12-26 21:27:32',
            'modified' => '2016-12-26 21:27:32',
        ],
    ];
}
