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
 * @since       2.25.4
 */

namespace MeCms\TestSuite;

use MeCms\TestSuite\TestCase;

/**
 * Abstract class for test entities
 */
abstract class EntityTestCase extends TestCase
{
    /**
     * Entity instance
     * @var \Cake\ORM\Entity|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $Entity;

    /**
     * If `true`, a mock instance of the shell will be created
     * @var bool
     */
    protected $autoInitializeClass = true;

    /**
     * Asserts that the entity has a "no accessible" property
     * @param string|array $property Property name
     * @return void
     * @uses $Entity
     */
    public function assertHasNoAccessibleProperty($property)
    {
        $this->Entity ?: $this->fail('The property `$this->Entity` has not been set');

        foreach ((array)$property as $name) {
            $this->assertFalse($this->Entity->isAccessible($name));
        }
    }

    /**
     * Called before every test method
     * @return void
     * @uses $Entity
     * @uses $autoInitializeClass
     */
    public function setUp()
    {
        parent::setUp();

        if (!$this->Entity && $this->autoInitializeClass) {
            $parts = explode('\\', get_class($this));
            array_splice($parts, 1, 2, []);
            $parts[count($parts) - 1] = substr($parts[count($parts) - 1], 0, -4);
            $className = implode('\\', $parts);

            $this->Entity = $this->getMockBuilder($className)
                ->setMethods(null)
                ->getMock();
        }
    }
}
