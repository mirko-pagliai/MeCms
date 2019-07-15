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
 * @see         https://github.com/cakephp/cakephp/issues/12024
 * @since       2.25.0
 */
namespace MeCms\Form;

use Cake\Form\Form as CakeForm;
use Cake\Validation\Validator;

/**
 * Form abstraction used to create forms not tied to ORM backed models,
 * or to other permanent datastores. Ideal for implementing forms on top of
 * API services, or contact forms.
 *
 * This class only overwrites the `validate()` method with respect to the
 *  original CakePHP class, due to the issue #12024
 */
class Form extends CakeForm
{
    /**
     * Used to check if $data passes this form's validation.
     * @param array $data The data to check.
     * @return bool Whether or not the data is valid
     */
    public function validate(array $data): bool
    {
        $oldValidator = function(Validator $validator = null) {
            if ($validator === null && empty($this->_validator)) {
                $validator = $this->_buildValidator(new $this->_validatorClass);
            }
            if ($validator) {
                $this->_validator = $validator;
                $this->setValidator('default', $validator);
            }

            return $this->getValidator();
        };

        $validator = $this->getValidator();
        if (!$validator->count()) {
            $validator = $oldValidator();
        }
        $this->_errors = $validator->errors($data);

        return count($this->_errors) === 0;
    }
}
