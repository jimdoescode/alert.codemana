<?php namespace Alerts\Repositories\Interfaces;

use Alerts\Models;

interface WatchedRepos
{
    /**
     * @param int $id
     * @return Models\WatchedRepo;
     */
    public function getById($id);
}
