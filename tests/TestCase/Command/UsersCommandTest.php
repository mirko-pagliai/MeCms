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

namespace MeCms\Test\TestCase\Command;

use MeCms\TestSuite\TestCase;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;

/**
 * UsersCommandTest class
 * @property \MeCms\Command\UsersCommand $Command
 */
class UsersCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var bool
     */
    public bool $autoInitializeClass = true;

    /**
     * @var array<string>
     */
    public $fixtures = [
        'plugin.MeCms.Users',
        'plugin.MeCms.UsersGroups',
    ];

    /**
     * Test for `getUsersRows()` method
     * @test
     */
    public function testGetUsersRows(): void
    {
        $result = $this->Command->getUsersRows();
        $this->assertSame('Active', $result[0]['status']);
        $this->assertSame('Pending', $result[1]['status']);
        $this->assertSame('Banned', $result[2]['status']);
    }

    /**
     * Test for `execute()` method
     * @test
     */
    public function testExecute(): void
    {
        /** @var \MeCms\Model\Table\UsersTable $Users */
        $Users = $this->getTable('MeCms.Users');
        $this->Command->Users = $Users;
        $expectedRows = [['<info>ID</info>', '<info>Username</info>', '<info>Group</info>', '<info>Name</info>', '<info>Email</info>', '<info>Posts</info>', '<info>Status</info>', '<info>Date</info>'], ...$this->Command->getUsersRows()];

        $this->exec('me_cms.users');
        $this->assertExitWithSuccess();
        array_walk($expectedRows, [$this, 'assertOutputContainsRow']);

        //Deletes all users
        $this->Command->Users->deleteAll(['id IS NOT' => null]);
        $this->exec('me_cms.users');
        $this->assertExitWithSuccess();
        $this->assertErrorContains('There are no users');
    }
}
