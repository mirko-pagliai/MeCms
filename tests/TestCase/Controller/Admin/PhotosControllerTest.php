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

use MeCms\Model\Entity\Photo;
use MeCms\TestSuite\ControllerTestCase;

/**
 * PhotosControllerTest class
 */
class PhotosControllerTest extends ControllerTestCase
{
    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.MeCms.Photos',
        'plugin.MeCms.PhotosAlbums',
    ];

    /**
     * Adds additional event spies to the controller/view event manager
     * @param \Cake\Event\Event $event A dispatcher event
     * @param \Cake\Controller\Controller|null $controller Controller instance
     * @return void
     */
    public function controllerSpy($event, $controller = null)
    {
        parent::controllerSpy($event, $controller);

        //Only for the `testUploadErrorOnSave()` method, it mocks the `Photos`
        //  table, so the `save()` method returns `false`
        if ($this->getName() === 'testUploadErrorOnSave') {
            $this->_controller->Photos = $this->getMockForModel('MeCms.' . $this->Table->getRegistryAlias(), ['save']);
            $this->_controller->Photos->method('save')->will($this->returnValue(false));
        }
    }

    /**
     * Tests for `beforeFilter()` method
     * @test
     */
    public function testBeforeFilter()
    {
        $this->Table->Albums->deleteAll(['id IS NOT' => null]);
        $this->get($this->url + ['action' => 'add']);
        $this->assertRedirect(['controller' => 'PhotosAlbums', 'action' => 'index']);
        $this->assertFlashMessage('You must first create an album');
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
            'user' => true,
        ]);

        //With `delete` action
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => true,
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
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'index.ctp');
        $this->assertContainsOnlyInstancesOf(Photo::class, $this->viewVariable('photos'));
        $this->assertCookieIsEmpty('render-photos');
    }

    /**
     * Tests for `index()` method, render as `grid`
     * @test
     */
    public function testIndexAsGrid()
    {
        $this->get($this->url + ['action' => 'index', '?' => ['render' => 'grid']]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'index_as_grid.ctp');
        $this->assertContainsOnlyInstancesOf(Photo::class, $this->viewVariable('photos'));
        $this->assertCookie('grid', 'render-photos');
    }

    /**
     * Tests for `index()` method, render as `grid` with cookie
     * @test
     */
    public function testIndexWithCookie()
    {
        $this->cookie('render-photos', 'grid');
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'index_as_grid.ctp');
        $this->assertCookie('grid', 'render-photos');
    }

    /**
     * Tests for `upload()` method
     * @test
     */
    public function testUpload()
    {
        $url = $this->url + ['action' => 'upload'];

        //GET request
        $this->get($url);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'upload.ctp');

        //POST request. This works
        $file = $this->createImageToUpload();
        $this->post($url + ['_ext' => 'json', '?' => ['album' => 1]], compact('file'));
        $this->assertResponseOkAndNotEmpty();

        //Checks the photo has been saved
        $photo = $this->Table->find()->last()->extract(['album_id', 'filename']);
        $this->assertEquals(['album_id' => 1, 'filename' => $file['name']], $photo);
        $this->assertFileExists(PHOTOS . 1 . DS . $file['name']);
    }

    /**
     * Tests for `upload()` method, error during the upload
     * @test
     */
    public function testUploadErrorDuringUpload()
    {
        $file = ['error' => UPLOAD_ERR_NO_FILE] + $this->createImageToUpload();
        $this->post($this->url + ['action' => 'upload', '_ext' => 'json', '?' => ['album' => 1]], compact('file'));
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"No file was uploaded"}');
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'json' . DS . 'upload.ctp');
    }

    /**
     * Tests for `upload()` method, error, missing ID on the query string
     * @test
     */
    public function testUploadErrorMissingAlbumIdOnQueryString()
    {
        $this->post($this->url + ['action' => 'upload', '_ext' => 'json'], ['file' => true]);
        $this->assertResponseFailure();
        $this->assertResponseContains('Missing ID');
    }

    /**
     * Tests for `upload()` method, error on save
     * @test
     */
    public function testUploadErrorOnSave()
    {
        //The table `save()` method returns `false` for this test. See the
        //  `controllerSpy()` method.
        $file = $this->createImageToUpload();
        $this->post($this->url + ['action' => 'upload', '_ext' => 'json', '?' => ['album' => 1]], compact('file'));
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"' . I18N_OPERATION_NOT_OK . '"}');
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'json' . DS . 'upload.ctp');
    }

    /**
     * Tests for `upload()` method, error on entity
     * @test
     */
    public function testUploadErrorOnEntity()
    {
        $file = ['name' => 'a.pdf'] + $this->createImageToUpload();
        $this->post($this->url + ['action' => 'upload', '_ext' => 'json', '?' => ['album' => 1]], compact('file'));
        $this->assertResponseFailure();
        $this->assertResponseEquals('{"error":"Valid extensions: gif, jpg, jpeg, png"}');
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'json' . DS . 'upload.ctp');
    }

    /**
     * Tests for `upload()` method, with only one album
     * @test
     */
    public function testUploadOnlyOneAlbum()
    {
        //Deletes all albums, except for the first one
        $this->Table->Albums->deleteAll(['id >' => 1]);

        //POST request. This should also work without the album ID on the query
        //  string, as there is only one album
        $file = $this->createImageToUpload();
        $this->post($this->url + ['action' => 'upload', '_ext' => 'json'], compact('file'));
        $this->assertResponseOkAndNotEmpty();

        //Checks the photo has been saved
        $photo = $this->Table->find()->last()->extract(['album_id', 'filename']);
        $this->assertEquals(['album_id' => 1, 'filename' => $file['name']], $photo);
        $this->assertFileExists(PHOTOS . 1 . DS . $file['name']);
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
        $this->assertTemplate('Admin' . DS . 'Photos' . DS . 'edit.ctp');
        $this->assertInstanceof(Photo::class, $this->viewVariable('photo'));

        //POST request. Data are valid
        $this->post($url, ['description' => 'New description for first banner']);
        $this->assertRedirect(['action' => 'index', 1]);
        $this->assertFlashMessage(I18N_OPERATION_OK);

        //POST request. Data are invalid
        $this->post($url, ['album_id' => 'aa']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains(I18N_OPERATION_NOT_OK);
        $this->assertInstanceof(Photo::class, $this->viewVariable('photo'));
    }

    /**
     * Tests for `download()` method
     * @test
     */
    public function testDownload()
    {
        $this->get($this->url + ['action' => 'download', 1]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertFileResponse(PHOTOS . 1 . DS . 'photo1.jpg');
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $this->post($this->url + ['action' => 'delete', 1]);
        $this->assertRedirect(['action' => 'index', 1]);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertTrue($this->Table->findById(1)->isEmpty());
        $this->skipIf(IS_WIN);
        $this->assertFileNotExists(PHOTOS . 1 . DS . 1);
    }
}
