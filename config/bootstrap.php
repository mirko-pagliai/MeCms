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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */

/**
 * (here `Cake\Core\Plugin` is used, as the plugins are not yet all loaded)
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use MeTools\Network\Request;

//Loads plugins
Plugin::load('MeTools', ['bootstrap' => TRUE]);
Plugin::load('Assets', ['bootstrap' => TRUE]);
Plugin::load('Thumbs', ['bootstrap' => TRUE, 'routes' => TRUE]);
Plugin::load('DatabaseBackup', ['bootstrap' => TRUE]);

require_once 'constants.php';
require_once 'global_functions.php';

/**
 * Loads the MeCms configuration
 */
Configure::load('MeCms.me_cms');

//Merges with the configuration from application, if exists
if(is_readable(CONFIG.'me_cms.php'))
	Configure::load('me_cms');

/**
 * Forces debug and loads DebugKit on localhost, if required
 */
if(is_localhost() && config('main.debug_on_localhost') && !Configure::read('debug')) {
	Configure::write('debug', TRUE);
	
    if(!Plugin::loaded('DebugKit'))
        Plugin::load('DebugKit', ['bootstrap' => TRUE]);
}

/**
 * Loads the theme plugin
 */
$theme = config('frontend.theme');

if($theme && !Plugin::loaded($theme))
	Plugin::load($theme);

/**
 * Loads the cache configuration
 */
Configure::load('MeCms.cache');

//Merges with the configuration from application, if exists
if(is_readable(CONFIG.'cache.php'))
	Configure::load('cache');
    
//Adds all cache configurations
foreach(Configure::consume('Cache') as $key => $config) {
	//Drops the default cache
	if($key === 'default')
		Cache::drop('default');
	
	Cache::config($key, $config);
}

/**
 * Loads the widgets configuration
 */
Configure::load('MeCms.widgets');

//Overwrites with the configuration from application, if exists
if(is_readable(CONFIG.'widgets.php'))
	Configure::load('widgets', 'default', FALSE);

//Adds the widgets configuration to the MeCms configuration
Configure::write('MeCms.frontend.widgets', Configure::consume('Widgets'));

/**
 * Adds `isAdmin()` detector for the request
 */
Request::addDetector('admin', function ($request) {
    return $request->param('prefix') === 'admin';
});