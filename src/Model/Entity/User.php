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

namespace MeCms\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Collection\Collection;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Symfony\Component\Finder\Finder;

/**
 * User entity
 * @property int $id
 * @property int $group_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property array|null $last_logins
 * @property bool $active
 * @property bool $banned
 * @property int $post_count
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \MeCms\Model\Entity\UsersGroup $group
 * @property \MeCms\Model\Entity\Post[] $posts
 * @property \Tokens\Model\Entity\Token[] $tokens
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned
     * @var array<string, bool>
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'post_count' => false,
        'modified' => false,
    ];

    /**
     * Virtual fields that should be exposed
     * @var array<string>
     */
    protected $_virtual = ['full_name', 'picture'];

    /**
     * Gets the full name (virtual field)
     * @return string
     */
    protected function _getFullName(): string
    {
        return $this->hasValue('first_name') && $this->hasValue('last_name') ? $this->get('first_name') . ' ' . $this->get('last_name') : '';
    }

    /**
     * Gets the picture (virtual field)
     * @return string
     * @throws \Tools\Exception\MethodNotExistsException
     */
    protected function _getPicture(): string
    {
        if ($this->hasValue('id')) {
            $finder = new Finder();
            $finder->files()->name('/^' . $this->get('id') . '\..+/')->in(USER_PICTURES);
            $files = objects_map(iterator_to_array($finder), 'getFilename');

            if (!empty($files)) {
                return 'users' . DS . array_value_first($files);
            }
        }

        $path = 'no-avatar.jpg';

        return is_readable(WWW_ROOT . 'img' . DS . $path) ? $path : 'MeCms.' . $path;
    }

    /**
     * Gets the last logins (accessor)
     * @param array|null $lastLogins Last logins
     * @return \Cake\Collection\Collection Last logins as `Collection`
     * @since 2.30.7-RC4
     */
    protected function _getLastLogins(?array $lastLogins): Collection
    {
        //Turns `time` values into ` FrozenTime` instances
        $lastLogins = array_map(fn(array $row): array => array_merge($row, ['time' => new FrozenTime($row['time'] ?? time())]), $lastLogins ?: []);

        return new Collection($lastLogins);
    }

    /**
     * Sets the password
     * @param string $password Password
     * @return string|false Password hash or `false` on failure
     */
    protected function _setPassword(string $password)
    {
        return (new DefaultPasswordHasher())->hash($password);
    }
}
