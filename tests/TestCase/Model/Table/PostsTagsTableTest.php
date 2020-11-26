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

namespace MeCms\Test\TestCase\Model\Table;

use MeCms\Model\Validation\PostsTagValidator;
use MeCms\TestSuite\TableTestCase;

/**
 * PostsTableTest class
 */
class PostsTagsTableTest extends TableTestCase
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
        'plugin.MeCms.Posts',
        'plugin.MeCms.PostsTags',
        'plugin.MeCms.Tags',
    ];

    /**
     * Test for `buildRules()` method
     * @test
     */
    public function testBuildRules()
    {
        $entity = $this->Table->newEntity(['tag_id' => 999, 'post_id' => 999]);
        $this->assertFalse($this->Table->save($entity));
        $this->assertEquals([
            'tag_id' => ['_existsIn' => I18N_SELECT_VALID_OPTION],
            'post_id' => ['_existsIn' => I18N_SELECT_VALID_OPTION],
        ], $entity->getErrors());
    }

    /**
     * Test for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        $this->assertEquals('posts_tags', $this->Table->getTable());
        $this->assertEquals('id', $this->Table->getDisplayField());
        $this->assertEquals('id', $this->Table->getPrimaryKey());

        $this->assertBelongsTo($this->Table->Posts);
        $this->assertEquals('post_id', $this->Table->Posts->getForeignKey());
        $this->assertEquals('INNER', $this->Table->Posts->getJoinType());
        $this->assertEquals('MeCms.Posts', $this->Table->Posts->getClassName());

        $this->assertBelongsTo($this->Table->Tags);
        $this->assertEquals('tag_id', $this->Table->Tags->getForeignKey());
        $this->assertEquals('INNER', $this->Table->Tags->getJoinType());
        $this->assertEquals('MeCms.Tags', $this->Table->Tags->getClassName());

        $this->assertHasBehavior('CounterCache');

        $this->assertInstanceOf(PostsTagValidator::class, $this->Table->getValidator());
    }
}
