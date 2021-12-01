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

use MeCms\Model\Entity\Post;

$this->extend('/common/view');
$this->assign('title', $post->get('title'));

/**
 * Userbar
 */
$class = 'badge badge-warning';
if (!$post->get('active')) {
    $this->addToUserbar($this->Html->span(I18N_DRAFT, compact('class')));
}
if ($post->get('created')->isFuture()) {
    $this->addToUserbar($this->Html->span(I18N_SCHEDULED, compact('class')));
}
$this->addToUserbar($this->Html->link(
    __d('me_cms', 'Edit post'),
    ['action' => 'edit', $post->get('id'), 'prefix' => ADMIN_PREFIX],
    ['class' => 'nav-link', 'icon' => 'pencil-alt', 'target' => '_blank']
));
$this->addToUserbar($this->Form->postLink(
    __d('me_cms', 'Delete post'),
    ['action' => 'delete', $post->get('id'), 'prefix' => ADMIN_PREFIX],
    ['class' => 'nav-link text-danger', 'icon' => 'trash-alt', 'confirm' => I18N_SURE_TO_DELETE, 'target' => '_blank']
));

/**
 * Breadcrumb
 */
if (getConfig('post.category')) {
    $this->Breadcrumbs->add(
        $post->get('category')->get('title'),
        ['_name' => 'postsCategory', $post->get('category')->get('slug')]
    );
}
$this->Breadcrumbs->add($post->get('title'), ['_name' => 'post', $post->get('slug')]);

/**
 * Meta tags
 */
if ($this->getRequest()->is('action', 'view', 'Posts')) {
    $this->Html->meta(['content' => 'article', 'property' => 'og:type']);

    if ($post->hasValue('modified')) {
        $this->Html->meta(['content' => $post->get('modified')->toUnixString(), 'property' => 'og:updated_time']);
    }

    //Adds tags as keywords
    if (getConfig('post.keywords')) {
        $this->Html->meta('keywords', preg_replace('/,\s/', ',', $post->get('tags_as_string')));
    }

    if ($post->hasValue('preview')) {
        foreach ($post->get('preview') as $preview) {
            $this->Html->meta(['href' => $preview->get('url'), 'rel' => 'image_src']);
            $this->Html->meta(['content' => $preview->get('url'), 'property' => 'og:image']);
            $this->Html->meta(['content' => $preview->get('width'), 'property' => 'og:image:width']);
            $this->Html->meta(['content' => $preview->get('height'), 'property' => 'og:image:height']);
        }
    }

    $this->Html->meta([
        'content' => $this->Text->truncate($post->get('plain_text'), 100, ['html' => true]),
        'property' => 'og:description',
    ]);
}

echo $this->element('views/post', compact('post'));
?>

<?php if (!empty($related)) : ?>
    <?php
        $relatedAsArray = collection($related)->map(function (Post $post) {
            return $this->Html->link($post->get('title'), ['_name' => 'post', $post->get('slug')]);
        })->toArray();
    ?>
    <div class="related-contents mb-4">
        <?= $this->Html->h5(__d('me_cms', 'Related posts')) ?>
        <?php if (!getConfig('post.related.images')) : ?>
            <?= $this->Html->ul($relatedAsArray, ['icon' => 'caret-right']) ?>
        <?php else : ?>
            <div class="d-none d-lg-block">
                <div class="row">
                    <?php foreach ($related as $post) : ?>
                        <div class="col-3">
                            <?= $this->element('views/post-preview', compact('post')) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-lg-none">
                <?= $this->Html->ul($relatedAsArray, ['icon' => 'caret-right']) ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
