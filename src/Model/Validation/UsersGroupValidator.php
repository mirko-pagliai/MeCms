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

namespace MeCms\Model\Validation;

use MeCms\Validation\AppValidator;

/**
 * UsersGroup validator class
 */
class UsersGroupValidator extends AppValidator
{
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->add('name', [
            'lengthBetween' => [
                'message' => __d('me_cms', 'Must be between {0} and {1} chars', 3, 100),
                'rule' => ['lengthBetween', 3, 100],
            ],
            'valid' => [
                'message' => sprintf('%s: %s', I18N_ALLOWED_CHARS, __d('me_cms', 'lowercase letters')),
                'rule' => ['custom', '/^[a-z]+$/'],
            ],
        ])->requirePresence('name', 'create');

        $this->add('label', [
            'lengthBetween' => [
                'message' => __d('me_cms', 'Must be between {0} and {1} chars', 3, 100),
                'rule' => ['lengthBetween', 3, 100],
            ],
        ])->requirePresence('label', 'create');
    }
}
