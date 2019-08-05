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

use Cake\Datasource\ConnectionInterface;
use Cake\TestSuite\Fixture\TestFixture;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * BannersFixture
 */
class BannersFixture extends TestFixture
{
    /**
     * Fields
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'position_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'filename' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'target' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'description' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'active' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'click_count' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'position_id' => ['type' => 'index', 'columns' => ['position_id'], 'length' => []],
        ],
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
            'id' => 1,
            'position_id' => 1,
            'filename' => 'banner1.jpg',
            'target' => 'http://www.example.com',
            'description' => 'First banner',
            'active' => 1,
            'click_count' => 2,
            'created' => '2016-12-26 16:26:04',
            'modified' => '2016-12-26 16:26:04',
        ],
        [
            'id' => 2,
            'position_id' => 1,
            'filename' => 'banner2.jpg',
            'target' => '',
            'description' => 'Second banner',
            'active' => 0,
            'click_count' => 0,
            'created' => '2016-12-26 16:27:04',
            'modified' => '2016-12-26 16:27:04',
        ],
        [
            'id' => 3,
            'position_id' => 2,
            'filename' => 'banner3.jpg',
            'target' => '',
            'description' => 'Third banner',
            'active' => 1,
            'click_count' => 3,
            'created' => '2016-12-26 16:28:04',
            'modified' => '2016-12-26 16:28:04',
        ],
    ];

    /**
     * Run after all tests executed, should remove the table/collection from
     *  the connection
     * @param ConnectionInterface $db An instance of the connection the fixture
     *  should be removed from
     * @return void
     */
    public function drop(ConnectionInterface $db)
    {
        parent::drop($db);

        try {
            unlink_recursive(BANNERS, 'empty');
        } catch (IOException $e) {
        }
    }

    /**
     * Run before each test is executed
     * @param ConnectionInterface $db An instance of the connection into which
     *  the records will be inserted
     * @return void
     */
    public function insert(ConnectionInterface $db)
    {
        parent::insert($db);

        foreach ($this->records as $record) {
            @create_file(BANNERS . $record['filename']);
        }
    }
}
