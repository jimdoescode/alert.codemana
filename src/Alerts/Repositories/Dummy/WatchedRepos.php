<?php namespace Alerts\Repositories\Dummy;

use Alerts\Models;
use Alerts\Repositories\Interfaces;

class WatchedRepos implements Interfaces\WatchedRepos
{
    public function getById($id)
    {
        $model = new Models\WatchedRepo();
        if ($id === 46534257) {
            $model->id = 46534257;
            $model->secret = 'super_secret_key_thingy';
        }
        return $model;
    }

    public function save(Models\WatchedRepo &$model)
    {
        return true;
    }
}
