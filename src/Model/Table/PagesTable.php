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
namespace MeCms\Model\Table;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\Database\Schema\Table as Schema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\RulesChecker;
use MeCms\Model\Table\AppTable;
use MeCms\Model\Table\Traits\GetPreviewFromTextTrait;
use MeCms\Model\Table\Traits\NextToBePublishedTrait;

/**
 * Pages model
 * @property \Cake\ORM\Association\BelongsTo $Categories
 * @method \MeCms\Model\Entity\Page get($primaryKey, $options = [])
 * @method \MeCms\Model\Entity\Page newEntity($data = null, array $options = [])
 * @method \MeCms\Model\Entity\Page[] newEntities(array $data, array $options = [])
 * @method \MeCms\Model\Entity\Page|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \MeCms\Model\Entity\Page patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \MeCms\Model\Entity\Page[] patchEntities($entities, array $data, array $options = [])
 * @method \MeCms\Model\Entity\Page findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PagesTable extends AppTable
{
    use GetPreviewFromTextTrait;
    use LocatorAwareTrait;
    use NextToBePublishedTrait;

    /**
     * Name of the configuration to use for this table
     * @var string
     */
    public $cache = 'pages';

    /**
     * Alters the schema used by this table. This function is only called after
     *  fetching the schema out of the database
     * @param Cake\Database\Schema\TableSchema $schema TableSchema instance
     * @return Cake\Database\Schema\TableSchema TableSchema instance
     * @since 2.17.0
     */
    protected function _initializeSchema(Schema $schema)
    {
        $schema->columnType('preview', 'json');

        return $schema;
    }

    /**
     * Called after an entity has been deleted
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Entity $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     * @uses MeCms\Model\Table\AppTable::afterDelete()
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::setNextToBePublished()
     */
    public function afterDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        parent::afterDelete($event, $entity, $options);

        //Sets the next record to be published
        $this->setNextToBePublished();
    }

    /**
     * Called after an entity is saved
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Entity $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     * @uses MeCms\Model\Table\AppTable::afterSave()
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::setNextToBePublished()
     */
    public function afterSave(Event $event, Entity $entity, ArrayObject $options)
    {
        parent::afterSave($event, $entity, $options);

        //Sets the next record to be published
        $this->setNextToBePublished();
    }

    /**
     * Called before each entity is saved
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Entity $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     * @since 2.17.0
     * @uses MeCms\Model\Table\AppTable::beforeSave()
     * @uses MeCms\Model\Table\Traits\GetPreviewFromTextTrait::getPreview()
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        parent::beforeSave($event, $entity, $options);

        $entity->preview = $this->getPreview($entity->text);
    }

    /**
     * Returns a rules checker object that will be used for validating
     *  application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['category_id'], 'Categories', __d('me_cms', 'You have to select a valid option')));
        $rules->add($rules->isUnique(['slug'], __d('me_cms', 'This value is already used')));
        $rules->add($rules->isUnique(['title'], __d('me_cms', 'This value is already used')));

        return $rules;
    }

    /**
     * Creates a new Query for this repository and applies some defaults based
     *  on the type of search that was selected
     * @param string $type The type of query to perform
     * @param array|ArrayAccess $options An array that will be passed to
     *  Query::applyOptions()
     * @return \Cake\ORM\Query The query builder
     * @uses $cache
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::getNextToBePublished()
     * @uses MeCms\Model\Table\Traits\NextToBePublishedTrait::setNextToBePublished()
     */
    public function find($type = 'all', $options = [])
    {
        //Gets from cache the timestamp of the next record to be published
        $next = $this->getNextToBePublished();

        //If the cache is invalid, it clears the cache and sets the next record
        //  to be published
        if ($next && time() >= $next) {
            Cache::clear(false, $this->cache);

            //Sets the next record to be published
            $this->setNextToBePublished();
        }

        return parent::find($type, $options);
    }

    /**
     * Initialize method
     * @param array $config The configuration for the table
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('pages');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Categories', ['className' => 'MeCms.PagesCategories'])
            ->setForeignKey('category_id')
            ->setJoinType('INNER')
            ->setTarget($this->tableLocator()->get('MeCms.PagesCategories'))
            ->setAlias('Categories');

        $this->addBehavior('Timestamp');
        $this->addBehavior('CounterCache', ['Categories' => ['page_count']]);

        $this->_validatorClass = '\MeCms\Model\Validation\PageValidator';
    }
}
