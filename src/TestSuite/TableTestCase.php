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
 * @since       2.25.4
 */

namespace MeCms\TestSuite;

use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;

/**
 * Abstract class for test tables
 */
abstract class TableTestCase extends TestCase
{
    /**
     * If `true`, a mock instance of the table will be created
     * @var bool
     */
    protected bool $autoInitializeClass = true;

    /**
     * Asserts that the table has a "belongs to" association
     * @param \Cake\ORM\Association $association Association
     * @return void
     */
    public function assertBelongsTo(Association $association): void
    {
        $this->assertInstanceOf(BelongsTo::class, $association);
    }

    /**
     * Asserts that the table has a "belongs to many" association
     * @param \Cake\ORM\Association $association Association
     * @return void
     */
    public function assertBelongsToMany(Association $association): void
    {
        $this->assertInstanceOf(BelongsToMany::class, $association);
    }

    /**
     * Asserts that the table has a behavior
     * @param string|array $behavior Behavior name as string or array
     * @return void
     */
    public function assertHasBehavior($behavior): void
    {
        $this->Table ?: $this->fail('The property `$this->Table` has not been set');

        foreach ((array)$behavior as $name) {
            $this->assertTrue($this->Table->hasBehavior($name));
        }
    }

    /**
     * Asserts that the table has a "many" association
     * @param \Cake\ORM\Association $association Association
     * @return void
     */
    public function assertHasMany(Association $association): void
    {
        $this->assertInstanceOf(HasMany::class, $association);
    }

    /**
     * Called before every test method
     * @return void
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (empty($this->Table) && $this->autoInitializeClass) {
            $this->Table = $this->getTable($this->getAlias($this), ['className' => $this->getOriginClassNameOrFail($this)]);
        }
    }
}
