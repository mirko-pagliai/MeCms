<?php
/**
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */

$this->extend('/Common/view');
$this->assign('title', $post->title);

/**
 * Userbar
 */
if (!$post->active) {
    $this->userbar($this->Html->span(
        __d('me_cms', 'Draft'),
        ['class' => 'label label-warning']
    ));
}

if ($post->created->isFuture()) {
    $this->userbar($this->Html->span(
        __d('me_cms', 'Scheduled'),
        ['class' => 'label label-warning']
    ));
}

$this->userbar([
    $this->Html->link(
        __d('me_cms', 'Edit post'),
        ['action' => 'edit', $post->id, 'prefix' => 'admin'],
        ['icon' => 'pencil', 'target' => '_blank']
    ),
    $this->Form->postLink(
        __d('me_cms', 'Delete post'),
        ['action' => 'delete', $post->id, 'prefix' => 'admin'],
        [
            'icon' => 'trash-o',
            'confirm' => __d('me_cms', 'Are you sure you want to delete this?'),
            'target' => '_blank',
        ]
    ),
]);

/**
 * Breadcrumb
 */
if (config('post.category')) {
    $this->Breadcrumbs->add(
        $post->category->title,
        ['_name' => 'postsCategory', $post->category->slug]
    );
}
$this->Breadcrumbs->add($post->title, ['_name' => 'post', $post->slug]);

/**
 * Meta tags
 */
if ($this->request->isAction('view', 'Posts')) {
    $this->Html->meta([
        'content' => 'article',
        'property' => 'og:type',
    ]);
    $this->Html->meta([
        'content' => $post->modified->toUnixString(),
        'property' => 'og:updated_time',
    ]);

    if (!empty($post->preview)) {
        $this->Html->meta([
            'href' => $post->preview,
            'rel' => 'image_src',
        ]);
        $this->Html->meta([
            'content' => $post->preview,
            'property' => 'og:image',
        ]);
    }

    if (!empty($post->text)) {
        $this->Html->meta([
            'content' => $this->Text->truncate(
                trim(strip_tags($this->BBCode->remove($post->text))),
                100,
                ['html' => true]
            ),
            'property' => 'og:description',
        ]);
    }
}

echo $this->element('views/post', compact('post'));
