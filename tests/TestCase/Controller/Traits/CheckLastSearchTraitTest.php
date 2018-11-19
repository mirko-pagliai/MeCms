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
namespace MeCms\Test\TestCase\Controller\Traits;

use Cake\Core\Configure;
use MeCms\Controller\PostsController;
use MeTools\TestSuite\TestCase;
use MeTools\TestSuite\Traits\MockTrait;

/**
 * CheckLastSearchTraitTest class
 */
class CheckLastSearchTraitTest extends TestCase
{
    use MockTrait;

    /**
     * Tests for `checkLastSearch()` method
     * @test
     */
    public function testCheckLastSearch()
    {
        $controller = $this->getMockForController(PostsController::class, null);

        $checkLastSearchMethod = function ($queryId = false) use ($controller) {
            return $this->invokeMethod($controller, 'checkLastSearch', [$queryId]);
        };

        $this->assertTrue($checkLastSearchMethod('my-query'));
        $firstSession = $controller->request->getSession()->read('last_search');
        $this->assertEquals('6bd2aab45de1d380f1e47e147494dbbd', $firstSession['id']);

        //Tries with the same query
        $this->assertTrue($checkLastSearchMethod('my-query'));
        $secondSession = $controller->request->getSession()->read('last_search');
        $this->assertEquals('6bd2aab45de1d380f1e47e147494dbbd', $secondSession['id']);

        $this->assertEquals($firstSession, $secondSession);

        //Tries with another query
        $this->assertFalse($checkLastSearchMethod('another-query'));
        $thirdSession = $controller->request->getSession()->read('last_search');
        $this->assertEquals($firstSession, $thirdSession);

        //Deletes the session and tries again with another query
        $controller->request->getSession()->delete('last_search');
        $this->assertTrue($checkLastSearchMethod('another-query'));
        $fourthSession = $controller->request->getSession()->read('last_search');
        $this->assertNotEquals($firstSession, $fourthSession);

        foreach ([0, false] as $value) {
            $controller->request->getSession()->delete('last_search');
            Configure::write(ME_CMS . '.security.search_interval', $value);
            $this->assertTrue($checkLastSearchMethod());
            $this->assertNull($controller->request->getSession()->read('last_search'));
        }
    }
}
