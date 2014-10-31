<?php
/**
 * ConfigComponent
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
 * @package		MeCms\Controller\Component
 */

App::uses('Component', 'Controller');

/**
 * It automatically handles the configuration
 */
class ConfigComponent extends Component {
	/**
	 * Configuration
	 * @var array
	 */
	protected $config = array();

	/**
	 * Turns a string of words separated by commas (and optional spaces) into an array.
	 * 
	 * For example:
	 * <pre>'alfa, beta, gamma'</pre>
	 * 
	 * Becomes:
	 * <code>array(
	 *	(int) 0 => 'alfa',
	 *	(int) 1 => 'beta',
	 *	(int) 2 => 'gamma'
	 * )
	 * </code>
	 * @param string $string String of words separated by commas (and optional spaces)
	 * @return array Array of values
	 */
	protected function _turnsAsArray($string) {
		return explode(',', preg_replace('/\s/', NULL, $string));
	}

	/**
	 * Is called after the controller executes the requested action's logic, 
	 * but before the controller's renders views and layout.
	 * @param Controller $controller
	 */
	public function beforeRender(Controller $controller) {
		//Sets the configuration for the view
		$controller->set('config', $this->config);
	}
	
    /**
     * Called before the controller's beforeFilter method.
     * @param Controller $controller
     * @see http://api.cakephp.org/2.5/class-Component.html#_initialize CakePHP Api
	 * @uses config
	 * @uses MeCmsAppController::isAdminRequest()
	 * @uses _turnsAsArray()
     */	
	public function initialize(Controller $controller) {		
		//Loads the configuration from the plugin (`APP/Plugin/MeCms/Config/mecms.php`)
		Configure::load('MeCms.mecms');
		
		//Loads the configuration from the app, if exists (`APP/Config/mecms.php`).
		//This configuration will overwrite the one obtained by the plugin
		if(is_readable(APP.'Config'.DS.'mecms.php'))
			Configure::load('mecms');
		
		//Turns some values as array
		Configure::write($key = 'MeCms.backend.topbar', $this->_turnsAsArray(Configure::read($key)));
		Configure::write($key = 'MeCms.frontend.widgets', $this->_turnsAsArray(Configure::read($key)));
				
		//If it's an admin request
		if($controller->isAdminRequest())
			$this->config = am(Configure::read('MeCms.backend'), Configure::read('MeCms.general'));
		//Else, if it is not ad admin request
		else
			$this->config = am(Configure::read('MeCms.frontend'), Configure::read('MeCms.general'));
		
		//Sets debug
		Configure::write('debug', $this->config['debug'] ? 2 : 0);
		//Sets cache
		Configure::write('Cache.disable', !$this->config['cache']);
		//Sets the session timeout
		Configure::write('Session.timeout', $this->config['timeout']);
		
		//Sets the configuration so that the controller can read it
		$controller->config = $this->config;
	}
}