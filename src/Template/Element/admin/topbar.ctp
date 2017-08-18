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
use MeCms\Core\Plugin;
?>

<nav id="topbar" class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
    <?= $this->Html->button($this->Html->span(null, ['class' => 'navbar-toggler-icon']), null, [
        'class' => 'navbar-toggler',
        'data-toggle' => 'collapse',
        'data-target' => '#navbarNavDropdown',
        'aria-controls' => 'navbarNavDropdown',
        'aria-expanded' => 'false',
        'aria-label' => __d('me_cms', 'Toggle navigation'),
    ]) ?>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <?php
            $menus[] = $this->Html->link(__d('me_cms', 'Homepage'), ['_name' => 'homepage'], [
                'class' => 'nav-link',
                'icon' => 'home',
                'target' => '_blank',
            ]);

            //Renders menus for each plugin
            foreach (Plugin::all(['exclude' => [METOOLS, ASSETS, DATABASE_BACKUP, THUMBER]]) as $plugin) {
                $menus += $this->MenuBuilder->renderAsDropdown($plugin, ['class' => 'nav-link d-lg-none']);
            }

            echo $this->Html->ul($menus, ['class' => 'navbar-nav mr-auto'], ['class' => 'dropdown']);

            $userMenu[] = call_user_func(function () {
                $this->Dropdown->start($this->Auth->user('full_name'), ['class' => 'nav-link', 'icon' => 'user']);

                if (getConfig('users.login_log')) {
                    echo $this->Html->link(
                        I18N_LAST_LOGIN,
                        ['controller' => 'Users', 'action' => 'lastLogin', 'plugin' => ME_CMS],
                        ['class' => 'dropdown-item']
                    );
                }

                echo $this->Html->link(
                    __d('me_cms', 'Change password'),
                    ['controller' => 'Users', 'action' => 'changePassword', 'plugin' => ME_CMS],
                    ['class' => 'dropdown-item']
                );
                echo $this->Html->link(__d('me_cms', 'Logout'), ['_name' => 'logout'], ['class' => 'dropdown-item']);

                return $this->Dropdown->end(['class' => 'dropdown-menu-right']);
            });

            echo $this->Html->ul($userMenu, ['class' => 'navbar-nav'], ['class' => 'dropdown']);
        ?>
    </div>
</nav>