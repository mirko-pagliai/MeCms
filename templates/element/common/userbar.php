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
 *
 * This element is extended by `templates/element/userbar.php` for the frontend
 *  userbar and by `templates/element/admin/userbar.php` for the backend userbar.
 */
?>

<nav id="userbar"
     class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm border-bottom border-white fs-6">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userbarNav"
                aria-controls="userbarNav" aria-expanded="false" aria-label="<?= __d('me_cms', 'Toggle navigation') ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="userbarNav">
            <?= $this->fetch('content') ?>

            <ul class="navbar-nav">
                <li class="nav-item dropwdown">
                    <?php
                    $params = ['controller' => 'Users', 'plugin' => 'MeCms'];
                    $text = $this->Auth->user('full_name');
                    if ($this->Auth->user('picture')) {
                        $text = $this->Thumb->fit($this->Auth->user('picture'), ['height' => 23], ['class' => 'me-2 rounded-circle']) . $text;
                    }

                    $this->Dropdown->start((string)$text, ['class' => 'nav-link']);

                    if (getConfig('users.login_log')) {
                        $this->Dropdown->link(I18N_LAST_LOGIN, $params + ['action' => 'lastLogin']);
                    }

                    $this->Dropdown->link(__d('me_cms', 'Change picture'), $params + ['action' => 'changePicture',]);
                    $this->Dropdown->link(__d('me_cms', 'Change password'), $params + ['action' => 'changePassword']);
                    $this->Dropdown->link(__d('me_cms', 'Logout'), ['_name' => 'logout']);

                    echo $this->Dropdown->end(['class' => 'dropdown-menu-end']);
                    ?>
                </li>
            </ul>
        </div>
    </div>
</nav>
