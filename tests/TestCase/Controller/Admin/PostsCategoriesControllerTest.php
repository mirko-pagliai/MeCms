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
namespace MeCms\Test\TestCase\Controller\Admin;

use MeCms\Model\Entity\PostsCategory;
use MeCms\TestSuite\ControllerTestCase;

/**
 * PostsCategoriesControllerTest class
 */
class PostsCategoriesControllerTest extends ControllerTestCase
{
    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.me_cms.PostsCategories',
    ];

    /**
     * Tests for `beforeFilter()` method
     * @test
     */
    public function testBeforeFilter()
    {
        foreach (['add', 'edit'] as $action) {
            $this->get($this->url + compact('action') + [1]);
            $this->assertNotEmpty($this->viewVariable('categories'));
        }
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        parent::testIsAuthorized();

        //With `delete` action
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => false,
            'user' => false,
        ], 'delete');
    }

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin/PostsCategories/index.ctp');
        $this->assertContainsInstanceof(PostsCategory::class, $this->viewVariable('categories'));
    }

    /**
     * Tests for `add()` method
     * @test
     */
    public function testAdd()
    {
        $url = $this->url + ['action' => 'add'];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin/PostsCategories/add.ctp');
        $this->assertInstanceof(PostsCategory::class, $this->viewVariable('category'));

        //POST request. Data are valid
        $this->post($url, ['title' => 'new category', 'slug' => 'category-slug']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains(I18N_OPERATION_NOT_OK);
        $this->assertInstanceof(PostsCategory::class, $this->viewVariable('category'));
    }

    /**
     * Tests for `edit()` method
     * @test
     */
    public function testEdit()
    {
        $url = $this->url + ['action' => 'edit', 1];

        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin/PostsCategories/edit.ctp');
        $this->assertInstanceof(PostsCategory::class, $this->viewVariable('category'));

        //POST request. Data are valid
        $this->post($url, ['title' => 'another title']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);

        //POST request. Data are invalid
        $this->post($url, ['title' => 'aa']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains(I18N_OPERATION_NOT_OK);
        $this->assertInstanceof(PostsCategory::class, $this->viewVariable('category'));
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        //POST request. This category has no pages
        $id = $this->Table->findByPostCount(0)->extract('id')->first();
        $this->post($this->url + ['action' => 'delete', $id]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertTrue($this->Table->findById($id)->isEmpty());

        //POST request. This category has some pages, so it cannot be deleted
        $id = $this->Table->find()->where(['post_count >=' => 1])->extract('id')->first();
        $this->post($this->url + ['action' => 'delete', $id]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_BEFORE_DELETE);
        $this->assertFalse($this->Table->findById($id)->isEmpty());
    }
}
