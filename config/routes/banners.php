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

/** @var \Cake\Routing\RouteBuilder $routes */

//Banner
if (!$routes->nameExists('banner')) {
    $routes->connect('/banner/:id', ['controller' => 'Banners', 'action' => 'open'], ['_name' => 'banner'])
        ->setPatterns(['id' => '\d+'])
        ->setPass(['id']);
}
