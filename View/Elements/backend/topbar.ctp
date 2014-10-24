<?php
/**
 * Backend topbar.
 *
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeCms\View\Elements
 */
?>

<nav id="topbar" class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#backend-topbar-collapse">
				<span class="sr-only"><?php echo __d('me_cms', 'Toggle navigation'); ?></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<?php echo $this->Html->link($config['title'], '#', array('class' => 'navbar-brand')); ?>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="backend-topbar-collapse">
			<ul class="nav navbar-nav">
				<?php
					echo $this->Html->li($this->Html->link(NULL, '/admin', array('icon' => 'home', 'title' => __d('me_cms', 'Home'))));
					echo $this->Html->li($this->Menu->get('posts', 'dropdown'), array('class' => 'dropdown'));
					echo $this->Html->li($this->Menu->get('pages', 'dropdown'), array('class' => 'dropdown'));
					echo $this->Html->li($this->Menu->get('photos', 'dropdown'), array('class' => 'dropdown'));
					echo $this->Html->li($this->Menu->get('users', 'dropdown'), array('class' => 'dropdown'));
					echo $this->Html->li($this->Menu->get('banners', 'dropdown'), array('class' => 'dropdown'));
					echo $this->Html->li($this->Menu->get('systems', 'dropdown'), array('class' => 'dropdown'));
				?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<?php
						echo $this->Dropdown->link($auth['full_name'], array('icon' => 'user'));
						echo $this->Dropdown->dropdown(array(
							$this->Html->link(__d('me_cms', 'Change password'), array('controller' => 'users', 'action' => 'change_password')),
							$this->Html->link(__d('me_cms', 'Logout'), array('controller' => 'users', 'action' => 'logout', 'admin' => FALSE))
						));
					?>
				</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
