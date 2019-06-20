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
namespace MeCms\Test\TestCase\View\View;

use MeCms\TestSuite\TestCase;
use MeCms\View\View\AdminView as View;

/**
 * AdminViewTest class
 */
class AdminViewTest extends TestCase
{
    /**
     * @var \MeCms\View\View\AdminView|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $View;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->View = $this->View ?: $this->getMockBuilder(View::class)
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Tests for `__construct()` method
     * @test
     */
    public function testConstruct()
    {
        $this->assertEquals('MeCms.admin', $this->View->getLayout());
    }

    /**
     * Tests for `render()` method
     * @test
     */
    public function testRender()
    {
        $this->View->render(false);
        $this->assertEquals([
            1 => '1 - Very low',
            2 => '2 - Low',
            3 => '3 - Normal',
            4 => '4 - High',
            5 => '5 - Very high',
        ], $this->View->get('priorities'));
    }
}
