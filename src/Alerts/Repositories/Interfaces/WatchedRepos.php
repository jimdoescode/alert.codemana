<?php namespace Alerts\Repositories\Interfaces;

use Alerts\Models;

interface WatchedRepos
{
    /**
     * @param int $id
     * @return Models\WatchedRepo
     */
    public function getById($id);

    /**
     * @param array $filters
     * @param int $count
     * @param int $start
     * @return Models\WatchedRepo[]
     */
    public function getAll(array $filters = [], $count = 10, $start = 0);

    /**
     * @param string $name
     * @return Models\WatchedRepo
     */
    public function createNew($name);

    /**
     * @param Models\WatchedRepo &$model
     * @return boolean
     */
    public function save(Models\WatchedRepo &$model);
}
