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
 * @since       2.31.0
 */

namespace MeCms\TestSuite;

use Cake\Http\ServerRequest;
use MeCms\Model\Entity\User;
use MeCms\Model\Entity\UsersGroup;

/**
 * Abstract class for test admin controllers
 * @property \MeCms\Controller\Admin\AppController $Controller
 */
abstract class AdminControllerTestCase extends ControllerTestCase
{
    /**
     * Called before every test method
     * @return void
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        if (!str_contains(get_class($this), 'Controller\\Admin\\')) {
            $this->fail('You cannot use the `AdminControllerTestCase` class with a non-admin controller');
        }

        parent::setUp();

        $this->url = ['prefix' => ADMIN_PREFIX, 'plugin' => 'MeCms'] + ($this->url ?? []) + ['controller' => 'Posts'];

        if (!empty($this->Controller)) {
            $Request = new ServerRequest(['params' => $this->url]);
            $this->Controller->setRequest($Request);
        }

        $this->setAuthData('admin');
    }

    /**
     * Internal method to get the result of a `isAuthorized()` call for a specific action and user group
     * @param string $action Action name
     * @param string $group Group name
     * @return bool
     */
    protected function _getIsAuthorizedResult(string $action, string $group): bool
    {
        $Controller = &$this->Controller;
        $Controller->setRequest($Controller->getRequest()->withParam('action', $action));

        return $Controller->isAuthorized(new User(['group' => new UsersGroup(['name' => $group])]));
    }

    /**
     * Assert that all groups are authorized to perform `$action`, calling `isAuthorized()` method for the current controller
     * @param string $action Action name
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    protected function assertAllGroupsAreAuthorized(string $action, string $message = ''): void
    {
        foreach (['admin', 'manager', 'user'] as $group) {
            self::assertGroupIsAuthorized($action, $group, $message);
        }
    }

    /**
     * Assert that `$group` is authorized to perform `$action`, calling `isAuthorized()` method for the current controller
     * @param string $action Action name
     * @param string $group Group name
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    protected function assertGroupIsAuthorized(string $action, string $group, string $message = ''): void
    {
        $message = $message ?: sprintf('False that the `%s` group is authorized to perform the `%s` action for controller `%s`', $group, $action, get_class($this->Controller));

        parent::assertTrue($this->_getIsAuthorizedResult($action, $group), $message);
    }

    /**
     * Assert that `$group` is not authorized to perform `$action`, calling `isAuthorized()` method for the current controller
     * @param string $action Action name
     * @param string $group Group name
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    protected function assertGroupIsNotAuthorized(string $action, string $group, string $message = ''): void
    {
        $message = $message ?: sprintf('False that the `%s` group is not authorized to perform the `%s` action for controller `%s`', $group, $action, get_class($this->Controller));

        parent::assertFalse($this->_getIsAuthorizedResult($action, $group), $message);
    }

    /**
     * Assert that only `admin` group is authorized to perform `$action`, calling `isAuthorized()` method for the current controller
     * @param string $action Action name
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    protected function assertOnlyAdminIsAuthorized(string $action, string $message = ''): void
    {
        self::assertGroupIsAuthorized($action, 'admin', $message);
        self::assertGroupIsNotAuthorized($action, 'manager', $message);
        self::assertGroupIsNotAuthorized($action, 'user', $message);
    }

    /**
     * Assert that only `user` group is not authorized to perform `$action`, calling `isAuthorized()` method for the current controller
     * @param string $action Action name
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    protected function assertOnlyUserIsNotAuthorized(string $action, string $message = ''): void
    {
        self::assertGroupIsAuthorized($action, 'admin', $message);
        self::assertGroupIsAuthorized($action, 'manager', $message);
        self::assertGroupIsNotAuthorized($action, 'user', $message);
    }

    /**
     * Tests for `isAuthorized()` method.
     *
     * This is a test for the default `isAuthorized()` method. It can then be extended for controllers that use the same
     *  method as the parent class. Otherwise, if the controller to be tested uses its own rules and implements its own
     *  `isAuthorized()` method, this test will have to be overridden.
     * @return void
     * @test
     */
    public function testIsAuthorized(): void
    {
        $this->assertOnlyUserIsNotAuthorized('add');
        $this->assertOnlyAdminIsAuthorized('delete');
    }
}
