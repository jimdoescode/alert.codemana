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
     * @return Models\WatchedRepo
     */
    public function createNew(Models\Repo $repo);

    /**
     * @param Models\WatchedRepo &$model
     * @return boolean
     */
    public function save(Models\WatchedRepo &$model);
}
