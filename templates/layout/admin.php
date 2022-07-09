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
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        echo $this->Html->charset();
        echo $this->Html->viewport();
        echo $this->Html->title($this->fetch('title'));
        echo $this->fetch('meta');

        echo $this->Html->css([
            'https://fonts.googleapis.com/css?family=Roboto|Abel',
            '/vendor/font-awesome/css/all.min',
        ], ['block' => true]);
        echo $this->Asset->css([
            '/vendor/bootstrap/css/bootstrap.min',
            'MeTools.default',
            'MeTools.forms',
            'MeCms.userbar',
            'MeCms.admin/layout',
        ], ['block' => true]);
        echo $this->fetch('css');

        echo $this->Asset->script([
            '/vendor/jquery/jquery.min',
            '/vendor/bootstrap/js/bootstrap.min',
            'MeCms.js.cookie.min',
            'MeTools.default',
            'MeCms.admin/layout',
            'MeCms.display-password',
        ], ['block' => true]);
        echo $this->fetch('script');
        ?>
    </head>
    <body>
        <?= $this->element('MeCms.admin/userbar') ?>
        <div class="container-fluid">
            <div class="row">
                <nav id="sidebar" class="col d-none d-lg-block">
                    <?php
                    //Sidebar is cached only if debugging is disabled
                    $sidebarCache = getConfig('debug') ? null : [
                        'config' => 'admin',
                        'key' => sprintf('sidebar_user_%s', $this->Auth->user('id')),
                    ];

                    echo $this->element('MeCms.admin/sidebar', [], ['cache' => $sidebarCache]);
                    ?>
                </nav>
                <main id="content" class="col-lg-10">
                    <?= $this->Flash->render() ?>
                    <?= $this->fetch('content') ?>
                </main>
            </div>
        </div>
        <?= $this->fetch('css_bottom') ?>
        <?= $this->fetch('script_bottom') ?>
    </body>
</html>
