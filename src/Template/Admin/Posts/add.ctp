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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
?>

<?php
	$this->assign('title', __d('me_cms', 'Add post'));
	$this->Library->ckeditor();
	$this->Library->datetimepicker();
	$this->Library->slugify();
	$this->Html->js('MeCms.backend/tags.min', ['block' => 'script_bottom']);
?>

<div class="posts form">
	<?= $this->Html->h2(__d('me_cms', 'Add post')) ?>
    <?= $this->Form->create($post); ?>
	<div class='float-form'>
		<?php
			//Only admins and managers can add posts on behalf of other users
			if($this->Auth->isGroup(['admin', 'manager']))
				echo $this->Form->input('user_id', [
					'default'	=> $auth['id'],
					'label'		=> __d('me_cms', 'Author')
				]);
			
            echo $this->Form->input('category_id', [
				'default'	=> count($categories) < 2 ? fv($categories) : NULL,
				'label'		=> __d('me_cms', 'Category')
			]);
            echo $this->Form->datetimepicker('created', [
				'label'	=> __d('me_cms', 'Date'),
				'tip'	=> [
					__d('me_cms', 'If blank, the current date and time will be used'),
					__d('me_cms', 'You can delay the publication by entering a future date')
				]
			]);
            echo $this->Form->input('priority', [
				'default'	=> '3',
				'label'		=> __d('me_cms', 'Priority')
			]);
            echo $this->Form->input('active', [
				'checked'	=> TRUE,
				'label'		=> sprintf('%s?', __d('me_cms', 'Published')),
				'tip'		=> __d('me_cms', 'Disable this option to save as a draft')
			]);
		?>
	</div>
    <fieldset>
        <?php
			echo $this->Form->input('title', [
				'id'	=> 'title',
				'label'	=> __d('me_cms', 'Title')
			]);
			echo $this->Form->input('subtitle', [
				'label' => __d('me_cms', 'Subtitle')
			]);
			echo $this->Form->input('slug', [
				'id'	=> 'slug',
				'label'	=> __d('me_cms', 'Slug'),
				'tip'	=> __d('me_cms', 'The slug is a string identifying a resource. If you do not have special needs, let it be generated automatically')
			]);
		?>	
			<div class="form-group to-be-hidden">
			<?php
				echo $this->Form->input('tags', [
					'id'	=> 'tags-output-text',
					'label'	=> __d('me_cms', 'Tags'),
					'rows'	=> 2,
					'tip'	=> __d('me_cms', 'Tags must be at least 3 chars and separated by a space. Only lowercase letters and numbers')
				]);
			?>
		</div>
		<div class="form-group hidden to-be-shown">
			<?php
				echo $this->Html->div(NULL, sprintf('%s:', __d('me_cms', 'Tags')), ['id' => 'tags-preview']);
				echo $this->Form->input('add_tags', [
					'button'	=> $this->Form->button(NULL, ['class' => 'btn-success', 'icon' => 'plus', 'id' => 'tags-input-button']),
					'id'		=> 'tags-input-text',
					'label'		=> FALSE,
					'tip'		=> __d('me_cms', 'Tags must be at least 3 chars and separated by a space. Only lowercase letters and numbers')
				]);
				
				//Tags error
				if($this->Form->isFieldError('tags'))
					echo $this->Form->error('tags');
			?>
		</div>
		<?php
			echo $this->Form->ckeditor('text', [
				'label' => __d('me_cms', 'Text'),
				'rows'	=> 10
			]);
        ?>
    </fieldset>
    <?= $this->Form->submit(__d('me_cms', 'Add post')) ?>
    <?= $this->Form->end() ?>
</div>