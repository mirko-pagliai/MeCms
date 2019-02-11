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
namespace MeCms\Test\TestCase\Command;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\Stub\ConsoleOutput;
use MeCms\Command\VersionUpdatesCommand;
use MeCms\TestSuite\TestCase;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;

/**
 * VersionUpdatesCommandTest class
 */
class VersionUpdatesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * @var bool
     */
    protected $autoInitializeClass = true;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.MeCms.Pages',
        'plugin.MeCms.Posts',
        'plugin.MeCms.Tags',
    ];

    /**
     * Test for `addEnableCommentsField()` method
     * @test
     */
    public function testAddEnableCommentsField()
    {
        $getTable = function ($name) {
            return $this->getMockForModel(sprintf('MeCms.%s', $name), null);
        };

        $this->loadFixtures('Pages', 'Posts');

        foreach (['Pages', 'Posts'] as $table) {
            $getTable($table)->getConnection()->execute(sprintf('ALTER TABLE `%s` DROP `enable_comments`', $getTable($table)->getTable()));
        }

        $this->Command->addEnableCommentsField();

        foreach (['Pages', 'Posts'] as $table) {
            $this->assertTrue($getTable($table)->getSchema()->hasColumn('enable_comments'));
        }
    }

    /**
     * Test for `alterTagColumnSize()` method
     * @test
     */
    public function testAlterTagColumnSize()
    {
        $getTable = function () {
            return $this->getMockForModel('MeCms.Tags', null);
        };

        $this->loadFixtures('Tags');

        $getTable()->getConnection()->execute(sprintf('ALTER TABLE %s MODIFY tag varchar(254) NOT NULL', $getTable()->getTable()));
        $this->assertEquals(254, $getTable()->getSchema()->getColumn('tag')['length']);

        $this->Command->alterTagColumnSize();
        $this->assertEquals(255, $getTable()->getSchema()->getColumn('tag')['length']);
    }

    /**
     * Test for `deleteOldDirectories()` method
     * @test
     */
    public function testdeleteOldDirectories()
    {
        $dir = WWW_ROOT . 'fonts';
        mkdir($dir);
        $this->assertFileExists($dir);
        $this->Command->deleteOldDirectories();
        $this->assertFileNotExists($dir);
    }

    /**
     * Test for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $methods = get_child_methods(VersionUpdatesCommand::class);
        $Command = $this->getMockBuilder(VersionUpdatesCommand::class)
            ->setMethods($methods)
            ->getMock();

        foreach ($methods as $method) {
            $Command->expects($this->once())->method($method);
        }

        $this->assertNull($Command->run([], new ConsoleIo(new ConsoleOutput, new ConsoleOutput)));
    }
}
