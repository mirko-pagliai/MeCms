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
namespace MeCms\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use MeCms\Controller\AppController;
use MeCms\TestSuite\IntegrationTestCase;

/**
 * AppControllerTest class
 */
class AppControllerTest extends IntegrationTestCase
{
    /**
     * @var \MeCms\Controller\AppController
     */
    protected $Controller;

    /**
     * @var \Cake\Event\Event
     */
    protected $Event;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        //Sets some configuration values
        Configure::write(ME_CMS . '.admin.records', 7);
        Configure::write(ME_CMS . '.default.records', 5);
        Configure::write(ME_CMS . '.security.recaptcha', true);
        Configure::write(ME_CMS . '.security.search_interval', 15);

        $this->Controller = $this->getMockBuilder(AppController::class)
            ->setMethods(['isBanned', 'isOffline', 'redirect'])
            ->getMock();

        $this->Controller->method('redirect')->will($this->returnArgument(0));

        $this->Event = new Event('myEvent');
    }

    /**
     * Tests for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $componentsInstance = $this->Controller->components();

        $components = collection($componentsInstance->loaded())
            ->map(function ($value) use ($componentsInstance) {
                return get_class($componentsInstance->{$value});
            })->toList();

        $this->assertEquals([
            'Cake\Controller\Component\CookieComponent',
            ME_CMS . '\Controller\Component\AuthComponent',
            METOOLS . '\Controller\Component\FlashComponent',
            'Cake\Controller\Component\RequestHandlerComponent',
            METOOLS . '\Controller\Component\UploaderComponent',
            'Recaptcha\Controller\Component\RecaptchaComponent',
        ], $components);

        $this->assertFalse($this->Controller->Cookie->config('encryption'));
    }

    /**
     * Tests for `beforeFilter()` method
     * @test
     */
    public function testBeforeFilter()
    {
        $this->assertEmpty($this->Controller->Auth->allowedActions);

        $this->Controller->request = $this->Controller->request
            ->withParam('action', 'my-action')
            ->withQueryParams(['sort' => 'my-field']);

        $this->Controller->beforeFilter($this->Event);

        $this->assertNotEmpty($this->Controller->Auth->allowedActions);
        $this->assertFalse(array_search('sortWhitelist', array_keys($this->Controller->paginate)));
        $this->assertEquals(5, $this->Controller->paginate['limit']);
        $this->assertEquals(5, $this->Controller->paginate['maxLimit']);
        $this->assertNull($this->Controller->viewBuilder()->getLayout());
        $this->assertEquals(ME_CMS . '.View/App', $this->Controller->viewBuilder()->getClassName());

        //Admin request
        $this->Controller = new AppController;
        $this->Controller->request = $this->Controller->request
            ->withParam('action', 'my-action')
            ->withQueryParams(['sort' => 'my-field'])
            ->withParam('prefix', ADMIN_PREFIX);

        $this->Controller->beforeFilter($this->Event);
        $this->assertEmpty($this->Controller->Auth->allowedActions);
        $this->assertEquals(['my-field'], $this->Controller->paginate['sortWhitelist']);
        $this->assertEquals(7, $this->Controller->paginate['limit']);
        $this->assertEquals(7, $this->Controller->paginate['maxLimit']);
        $this->assertEquals(ME_CMS . '.View/Admin', $this->Controller->viewBuilder()->getClassName());

        //Ajax request
        $this->Controller->request = $this->Controller->request->withEnv('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->Controller->beforeFilter($this->Event);
        $this->assertEquals(ME_CMS . '.ajax', $this->Controller->viewBuilder()->getLayout());
    }

    /**
     * Tests for `beforeFilter()` method, with a banned user
     * @test
     */
    public function testBeforeFilterWithBannedUser()
    {
        $this->Controller->method('isBanned')->willReturn(true);

        $beforeFilter = $this->Controller->beforeFilter($this->Event);
        $this->assertEquals(['_name' => 'ipNotAllowed'], $beforeFilter);
    }

    /**
     * Tests for `beforeFilter()` method, on offline site
     * @test
     */
    public function testBeforeFilterWithOfflineSite()
    {
        $this->Controller->method('isOffline')->willReturn(true);

        $beforeFilter = $this->Controller->beforeFilter($this->Event);
        $this->assertEquals(['_name' => 'offline'], $beforeFilter);
    }

    /**
     * Tests for `beforeRender()` method
     * @test
     */
    public function testBeforeRender()
    {
        $this->Controller->beforeRender($this->Event);
        $this->assertArrayKeysEqual([
            'Recaptcha.Recaptcha',
            'MeCms.Auth',
        ], $this->Controller->viewBuilder()->getHelpers());
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        //No prefix
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
            'user' => true,
        ]);

        //Admin prefix
        $this->Controller = new AppController;
        $this->Controller->request = $this->Controller->request->withParam('prefix', ADMIN_PREFIX);
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
            'user' => false,
        ]);

        //Other prefix
        $this->Controller = new AppController;
        $this->Controller->request = $this->Controller->request->withParam('prefix', 'otherPrefix');
        $this->assertGroupsAreAuthorized([
            'admin' => false,
            'manager' => false,
            'user' => false,
        ]);
    }
}
