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
namespace MeCms\Controller;

use Cake\Cache\Cache;
use MeCms\Controller\AppController;

/**
 * PhotosAlbums controller
 * @property \MeCms\Model\Table\PhotosAlbumsTable $PhotosAlbums
 */
class PhotosAlbumsController extends AppController
{
    /**
     * Lists albums
     * @return \Cake\Network\Response|null|void
     */
    public function index()
    {
        $albums = $this->PhotosAlbums->find('active')
            ->select(['id', 'title', 'slug', 'photo_count'])
            ->contain([
                'Photos' => function ($q) {
                    return $q->select(['album_id', 'filename'])
                        ->where([
                            sprintf('%s.active', $this->PhotosAlbums->Photos->alias()) => true,
                        ])
                        ->order('rand()');
                }
            ])
            ->order(['title' => 'ASC'])
            ->cache('albums_index', $this->PhotosAlbums->cache)
            ->all();

        //If there is only one album, redirects to that album
        if ($albums->count() === 1) {
            return $this->redirect(['action' => 'view', $albums->toArray()[0]->slug]);
        }

        $this->set(compact('albums'));
    }

    /**
     * Views album
     * @param string $slug Album slug
     * @return \Cake\Network\Response|null|void
     */
    public function view($slug = null)
    {
        //Data can be passed as query string, from a widget
        if ($this->request->getQuery('q')) {
            return $this->redirect([$this->request->getQuery('q')]);
        }

        //Gets album ID and title
        $album = $this->PhotosAlbums->find('active')
            ->select(['id', 'title'])
            ->where(compact('slug'))
            ->cache(sprintf('album_%s', md5($slug)), $this->PhotosAlbums->cache)
            ->firstOrFail();

        $page = $this->request->getQuery('page') ?: 1;
        $this->paginate['limit'] = $this->paginate['maxLimit'] = config('default.photos');

        //Sets the cache name
        $cache = sprintf('album_%s_limit_%s_page_%s', md5($slug), $this->paginate['limit'], $page);

        //Tries to get data from the cache
        list($photos, $paging) = array_values(
            Cache::readMany(
                [$cache, sprintf('%s_paging', $cache)],
                $this->PhotosAlbums->cache
            )
        );

        //If the data are not available from the cache
        if (empty($photos) || empty($paging)) {
            $query = $this->PhotosAlbums->Photos->find('active')
                ->select(['id', 'album_id', 'filename', 'description'])
                ->where(['album_id' => $album->id])
                ->order([
                    sprintf('%s.created', $this->PhotosAlbums->Photos->alias()) => 'DESC',
                    sprintf('%s.id', $this->PhotosAlbums->Photos->alias()) => 'DESC',
                ]);

            $photos = $this->paginate($query)->toArray();

            //Writes on cache
            Cache::writeMany([
                $cache => $photos,
                sprintf('%s_paging', $cache) => $this->request->getParam('paging'),
            ], $this->PhotosAlbums->cache);
        //Else, sets the paging parameter
        } else {
            $this->request = $this->request->withParam('paging', $paging);
        }

        $this->set(compact('album', 'photos'));
    }

    /**
     * Preview for albums.
     * It uses the `view` template.
     * @param string $slug Album slug
     * @return \Cake\Network\Response
     */
    public function preview($slug = null)
    {
        $album = $this->PhotosAlbums->find()
            ->select(['id', 'slug', 'title', 'active'])
            ->contain([
                'Photos' => function ($q) {
                    return $q->select(['id', 'album_id', 'filename', 'description'])
                        ->order([
                            sprintf('%s.created', $this->PhotosAlbums->Photos->alias()) => 'DESC',
                            sprintf('%s.id', $this->PhotosAlbums->Photos->alias()) => 'DESC',
                        ]);
                }
             ])
            ->where(compact('slug'))
            ->firstOrFail();

        $this->set(compact('album'));

        $this->render('view');
    }
}
