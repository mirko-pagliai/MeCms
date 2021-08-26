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
$this->extend('MeCms./common/form');
$this->assign('title', $title = __d('me_cms', 'Sign up'));
?>

<?= $this->Form->create($user); ?>
<fieldset>
    <?php
    echo $this->Form->control('username', [
        'autocomplete' => 'off',
        'label' => I18N_USERNAME,
    ]);
    echo $this->Form->control('email', [
        'autocomplete' => 'off',
        'help' => I18N_ENTER_YOUR_EMAIL,
        'label' => I18N_EMAIL,
    ]);
    echo $this->Form->control('email_repeat', [
        'autocomplete' => 'off',
        'help' => __d('me_cms', 'Repeat your email'),
        'label' => I18N_REPEAT_EMAIL,
    ]);
    echo $this->Form->control('password', [
        'autocomplete' => 'off',
        'button' => $this->Html->button('', '#', [
            'class' => 'display-password',
            'icon' => 'eye',
            'title' => I18N_SHOW_HIDE_PASSWORD,
         ]),
        'help' => __d('me_cms', 'Enter your password'),
        'label' => I18N_PASSWORD,
    ]);
    echo $this->Form->control('password_repeat', [
        'autocomplete' => 'off',
        'button' => $this->Html->button('', '#', [
            'class' => 'display-password',
            'icon' => 'eye',
            'title' => I18N_SHOW_HIDE_PASSWORD,
         ]),
        'help' => __d('me_cms', 'Repeat your password'),
        'label' => I18N_REPEAT_PASSWORD,
    ]);
    echo $this->Form->control('first_name', [
        'autocomplete' => 'off',
        'label' => I18N_FIRST_NAME,
    ]);
    echo $this->Form->control('last_name', [
        'autocomplete' => 'off',
        'label' => I18N_LAST_NAME,
    ]);

    if (getConfig('security.recaptcha')) {
        echo $this->Recaptcha->display();
    }
    ?>
</fieldset>
<?= $this->Form->submit($title, ['class' => 'btn-block btn-lg btn-primary']) ?>
<?= $this->Form->end() ?>

<?= $this->element('login/menu'); ?>
