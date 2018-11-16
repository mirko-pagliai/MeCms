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
namespace MeCms\Test\TestCase\Controller\Component;

use MeTools\TestSuite\ComponentTestCase;

/**
 * AuthComponentTest class
 */
class AuthComponentTest extends ComponentTestCase
{
    /**
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.Users',
        'plugin.me_cms.UsersGroups',
    ];

    /**
     * Tests for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $expected = [
            'authenticate' => [
                'Form' => [
                    'finder' => 'auth',
                    'userModel' => ME_CMS . '.Users',
                ],
            ],
            'authorize' => 'Controller',
            'ajaxLogin' => null,
            'flash' => [
                'element' => ME_TOOLS . '.flash',
                'params' => ['class' => 'alert-danger'],
            ],
            'loginAction' => ['_name' => 'login'],
            'loginRedirect' => ['_name' => 'dashboard'],
            'logoutRedirect' => ['_name' => 'homepage'],
            'authError' => false,
            'unauthorizedRedirect' => ['_name' => 'dashboard'],
            'storage' => 'Session',
            'checkAuthIn' => 'Controller.startup',
        ];
        $this->assertEquals($expected, $this->Component->getConfig());

        $this->Component->setUser(['id' => 1]);
        $this->Component->initialize([]);
        $expected['authError'] = 'You are not authorized for this action';
        $this->assertEquals($expected, $this->Component->getConfig());
    }

    /**
     * Tests for `hasId()` method
     * @test
     */
    public function testHasId()
    {
        $this->assertFalse($this->Component->hasId(1));

        $this->Component->setUser(['id' => 1]);
        $this->assertTrue($this->Component->hasId(1));
        $this->assertTrue($this->Component->hasId([1, 2]));
        $this->assertFalse($this->Component->hasId(2));
        $this->assertFalse($this->Component->hasId([2, 3]));
    }

    /**
     * Tests for `identify()` method
     * @test
     */
    public function testIdentify()
    {
        $this->loadFixtures();
        $expected = [
            'id' => 6,
            'username' => 'zeta',
            'email' => 'zeta@example.com',
            'active' => true,
            'banned' => false,
            'first_name' => 'Zeta',
            'last_name' => 'Zeta',
            'group' => ['name' => 'admin'],
            'full_name' => 'Zeta Zeta',
            'picture' => 'MeCms.no-avatar.jpg',
        ];

        $this->Component->constructAuthenticate();
        $this->Component->request = $this->Component->request->withData('username', 'zeta');
        $this->Component->request = $this->Component->request->withData('password', 'zeta');
        $this->assertEquals($expected, $this->Component->identify());
    }

    /**
     * Tests for `isFounder()` method
     * @test
     */
    public function testIsFounder()
    {
        $this->assertFalse($this->Component->isFounder());

        $this->Component->setUser(['id' => 1]);
        $this->assertTrue($this->Component->isFounder());

        $this->Component->setUser(['id' => 2]);
        $this->assertFalse($this->Component->isFounder());
    }

    /**
     * Tests for `isLogged()` method
     * @test
     */
    public function testIsLogged()
    {
        $this->assertFalse($this->Component->isLogged());

        $this->Component->setUser(['id' => 1]);
        $this->assertTrue($this->Component->isLogged());
    }

    /**
     * Tests for `isGroup()` method
     * @test
     */
    public function testIsGroup()
    {
        $this->assertFalse($this->Component->isGroup('admin'));

        $this->Component->setUser(['group' => ['name' => 'admin']]);
        $this->assertTrue($this->Component->isGroup('admin'));
        $this->assertTrue($this->Component->isGroup(['admin', 'manager']));
        $this->assertFalse($this->Component->isGroup(['manager', 'noExistingGroup']));
    }
}
