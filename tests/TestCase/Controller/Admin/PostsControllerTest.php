<?php
/**
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeCms\Test\TestCase\Controller\Admin;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use MeCms\Controller\Admin\PostsController;
use MeCms\TestSuite\Traits\AuthMethodsTrait;

/**
 * PostsControllerTest class
 */
class PostsControllerTest extends IntegrationTestCase
{
    use AuthMethodsTrait;

    /**
     * @var \MeCms\Controller\Admin\PostsController
     */
    protected $Controller;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.posts',
    ];

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Controller = new PostsController;
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Controller);
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        $this->assertGroupsAreAuthorized([
            null => true,
            'admin' => true,
            'manager' => true,
            'user' => true,
        ]);

        //`edit` and `delete` actions
        foreach (['edit', 'delete'] as $action) {
            $this->Controller = new PostsController;
            $this->Controller->Posts = TableRegistry::get('MeCms.Posts');
            $this->Controller->request = $this->Controller->request->withParam('action', $action);

            $this->assertGroupsAreAuthorized([
                null => false,
                'admin' => true,
                'manager' => true,
                'user' => false,
            ]);
        }

        //`edit` action, with an user who owns the record
        $this->Controller = new PostsController;
        $this->Controller->Posts = TableRegistry::get('MeCms.Posts');
        $this->Controller->request = $this->Controller->request
            ->withParam('action', 'edit')
            ->withParam('pass.0', 1);

        $this->assertUsersAreAuthorized([
            1 => true,
            2 => false,
            3 => false,
            4 => false,
        ]);

        $this->Controller->request = $this->Controller->request
            ->withParam('pass.0', 2);

        $this->assertUsersAreAuthorized([
            1 => false,
            2 => false,
            3 => false,
            4 => true,
        ]);
    }
}