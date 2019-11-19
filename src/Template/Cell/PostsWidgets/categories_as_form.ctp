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

use MeCms\Model\Entity\PostsCategory;

if (empty($categories) || $categories->count() < 2) {
    return;
}

$this->extend('/Common/widget');
$this->assign('title', I18N_POSTS_CATEGORIES);

echo $this->Form->create(null, [
    'type' => 'get',
    'url' => ['_name' => 'postsCategory', 'category'],
]);
echo $this->Form->control('q', [
    'id' => false,
    'label' => false,
    'onchange' => 'sendForm(this)',
    'options' => $categories->map(function (PostsCategory $category) {
        return sprintf('%s (%d)', $category->get('title'), $category->get('post_count'));
    })->toArray(),
]);
echo $this->Form->end();
