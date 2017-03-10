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
namespace MeCms\Test\TestCase\Form;

use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\TestSuite\TestCase;
use MeCms\Form\ContactForm;
use Reflection\ReflectionTrait;

/**
 * ContactFormTest class
 */
class ContactFormTest extends TestCase
{
    use MailerAwareTrait;
    use ReflectionTrait;

    /**
     * @var \MeCms\Form\ContactForm
     */
    public $ContactForm;

    /**
     * @var array
     */
    protected $example;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Email::configTransport(['debug' => ['className' => 'Debug']]);

        $this->ContactForm = new ContactForm;

        $this->example = [
            'email' => 'test@test.com',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'message' => 'Example of message',
        ];
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        Email::dropTransport('debug');

        unset($this->ContactForm);
    }

    /**
     * Test validation.
     * It tests the proper functioning of the example data.
     * @test
     */
    public function testValidationExampleData()
    {
        $this->assertTrue($this->ContactForm->validate($this->example));
        $this->assertEmpty($this->ContactForm->errors());

        foreach (array_keys($this->example) as $key) {
            //Create a copy of the example data and removes the current value
            $copy = $this->example;
            unset($copy[$key]);

            $this->assertFalse($this->ContactForm->validate($copy));
            $this->assertEquals([
                $key => ['_required' => 'This field is required'],
            ], $this->ContactForm->errors());
        }
    }

    /**
     * Test validation for `message` property
     * @test
     */
    public function testValidationForMessage()
    {
        foreach ([str_repeat('a', 9), str_repeat('a', 1001)] as $value) {
            $this->example['message'] = $value;

            $this->assertFalse($this->ContactForm->validate($this->example));
            $this->assertEquals([
                'message' => ['lengthBetween' => 'Must be between 10 and 1000 chars'],
            ], $this->ContactForm->errors());
        }

        foreach ([str_repeat('a', 10), str_repeat('a', 1000)] as $value) {
            $this->example['message'] = $value;

            $this->assertTrue($this->ContactForm->validate($this->example));
            $this->assertEmpty($this->ContactForm->errors());
        }
    }

    /**
     * Tests for `_execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->ContactForm = $this->getMockBuilder(get_class($this->ContactForm))
            ->setMethods(['getMailer'])
            ->getMock();

        $this->ContactForm->expects($this->once())
            ->method('getMailer')
            ->with('MeCms.ContactForm')
            ->will($this->returnCallback(function ($data) {
                return $this->getMailer($data)->transport('debug');
            }));

        $this->assertEquals(['headers', 'message'], array_keys($this->ContactForm->execute($this->example)));
    }
}
