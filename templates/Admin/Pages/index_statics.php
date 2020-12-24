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
$this->extend('MeCms./Admin/common/index');
$this->assign('title', __d('me_cms', 'Static pages'));
?>

<table class="table table-hover">
    <thead>
        <tr>
            <th><?= I18N_FILENAME ?></th>
            <th class="text-center"><?= I18N_TITLE ?></th>
            <th class="text-nowrap"><?= __d('me_cms', 'Path') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pages as $page) : ?>
            <tr>
                <td>
                    <strong>
                        <?= $this->Html->link($page->filename, ['_name' => 'page', $page->slug], ['target' => '_blank']) ?>
                    </strong>
                    <?php
                    $actions = [
                        $this->Html->link(I18N_OPEN, ['_name' => 'page', $page->slug], [
                            'icon' => 'external-link-alt',
                            'target' => '_blank',
                        ]),
                    ];

                    echo $this->Html->ul($actions, ['class' => 'actions']);
                    ?>
                </td>
                <td class="text-center">
                    <?= $page->title ?>
                </td>
                <td class="text-nowrap">
                    <samp><?= $page->path ?></samp>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
