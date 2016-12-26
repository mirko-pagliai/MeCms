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
use Cake\Core\Configure;

if (!function_exists('config')) {
    /**
     * Gets config values stored in the configuration.
     * It will first look in the MeCms configuration, then in the application configuration
     * @param string|null $key Configuration key
     * @return mixed Configuration value
     */
    function config($key = null)
    {
        $value = Configure::read(sprintf('MeCms.%s', $key));

        if ($value) {
            return $value;
        }

        return Configure::read($key);
    }
}

if (!function_exists('firstImageFromText')) {
    /**
     * Performs a regex match on a text and returns the first image
     * @param string $text Text
     * @return string|bool Image or `false` if there's no image on the text
     */
    function firstImageFromText($text)
    {
        preg_match('#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im', $text, $matches);

        if (empty($matches[2])) {
            return false;
        }

        return $matches[2];
    }
}
