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

use Cake\Cache\Cache;
use Cake\Log\Log;
use Cake\ORM\Entity;
use MeCms\Form\BackupForm;
use MeCms\TestSuite\ControllerTestCase;

/**
 * BackupsControllerTest class
 */
class BackupsControllerTest extends ControllerTestCase
{
    /**
     * Internal method to create a backup file
     * @param string $extension Extension
     * @return string File path
     */
    protected function createSingleBackup($extension = 'sql')
    {
        $file = getConfigOrFail(DATABASE_BACKUP . '.target') . DS . sprintf('backup.%s', $extension);
        file_put_contents($file, null);

        return $file;
    }

    /**
     * Internal method to create some backup files
     * @return array Files paths
     */
    protected function createSomeBackups()
    {
        return array_map([$this, 'createSingleBackup'], ['sql', 'sql.gz', 'sql.bz2']);
    }

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        //Deletes all backups
        safe_unlink_recursive(getConfigOrFail(DATABASE_BACKUP . '.target'));

        parent::tearDown();
    }

    /**
     * Adds additional event spies to the controller/view event manager
     * @param \Cake\Event\Event $event A dispatcher event
     * @param \Cake\Controller\Controller|null $controller Controller instance
     * @return void
     */
    public function controllerSpy($event, $controller = null)
    {
        parent::controllerSpy($event, $controller);

        if ($this->getName() !== 'testSend') {
            return;
        }

        //Only for the `testSend` test, mocks the `send()` method of
        //  `BackupManager` class, so that it writes on the debug log
        //  instead of sending a real mail
        $this->_controller->BackupManager = $this->getMockBuilder(BackupManager::class)
            ->setMethods(['send'])
            ->getMock();

        $this->_controller->BackupManager->method('send')->will($this->returnCallback(function () {
            $args = implode(', ', array_map(function ($arg) {
                return '`' . $arg . '`';
            }, func_get_args()));

            return Log::write('debug', 'Called `send()` with args: ' . $args);
        }));
    }

    /**
     * Tests for `isAuthorized()` method
     * @test
     */
    public function testIsAuthorized()
    {
        $this->assertGroupsAreAuthorized([
            'admin' => true,
            'manager' => false,
            'user' => false,
        ]);
    }

    /**
     * Tests for `index()` method
     * @test
     */
    public function testIndex()
    {
        $this->createSomeBackups();
        $this->get($this->url + ['action' => 'index']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertTemplate('Admin/Backups/index.ctp');
        $this->assertContainsInstanceof(Entity::class, $this->viewVariable('backups'));
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
        $this->assertTemplate('Admin/Backups/add.ctp');
        $this->assertInstanceof(BackupForm::class, $this->viewVariable('backup'));

        //POST request. Data are invalid
        $this->post($url, ['filename' => 'backup.txt']);
        $this->assertResponseOkAndNotEmpty();
        $this->assertResponseContains(I18N_OPERATION_NOT_OK);
        $this->assertFileNotExists(getConfigOrFail(DATABASE_BACKUP . '.target') . DS . 'backup.txt');

        //POST request. Now data are valid
        $this->post($url, ['filename' => 'backup.sql']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertFileExists(getConfigOrFail(DATABASE_BACKUP . '.target') . DS . 'backup.sql');
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $file = $this->createSingleBackup();
        $this->post($this->url + ['action' => 'delete', urlencode(basename($file))]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertFileNotExists($file);
    }

    /**
     * Tests for `deleteAll()` method
     * @test
     */
    public function testDeleteAll()
    {
        $files = $this->createSomeBackups();
        $this->post($this->url + ['action' => 'deleteAll']);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertFileNotExists($files);
    }

    /**
     * Tests for `download()` method
     * @test
     */
    public function testDownload()
    {
        $file = $this->createSingleBackup();
        $this->get($this->url + ['action' => 'download', urlencode(basename($file))]);
        $this->assertResponseOkAndNotEmpty();
        $this->assertFileResponse($file);
    }

    /**
     * Tests for `restore()` method
     * @test
     */
    public function testRestore()
    {
        Cache::writeMany(['firstKey' => 'firstValue', 'secondKey' => 'secondValue']);
        $file = $this->createSingleBackup();
        $this->post($this->url + ['action' => 'restore', urlencode(basename($file))]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertFalse(Cache::read('firstKey'));
        $this->assertFalse(Cache::read('secondKey'));
    }

    /**
     * Tests for `send()` method
     * @test
     */
    public function testSend()
    {
        $email = getConfigOrFail(ME_CMS . '.email.webmaster');
        $file = $this->createSingleBackup();
        $this->post($this->url + ['action' => 'send', urlencode(basename($file))]);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage(I18N_OPERATION_OK);
        $this->assertLogContains(sprintf('Called `send()` with args: `%s`, `%s`', $file, $email), 'debug');
    }
}
