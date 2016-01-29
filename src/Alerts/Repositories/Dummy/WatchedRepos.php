<?php namespace Alerts\Repositories\Dummy;

use Alerts\Models;
use Alerts\Repositories\Interfaces;

class WatchedRepos implements Interfaces\WatchedRepos
{
    public function getById($id)
    {
        $model = new Models\WatchedRepo();
        $watchedFile = new Models\WatchedFile();
        if ($id === 46534257) {

            $watchedFile->emails = ['jimdoescode+github@gmail.com'];

            $model->id = 46534257;
            $model->secret = 'super_secret_key_thingy';
            $model->watchedFiles = [
                $watchedFile
            ];
        }
        return $model;
    }

    public function createNew(Models\Repo $repo)
    {
        $model = new Models\WatchedRepo();
        $model->id = 46534257;
        $model->name = $repo->name;
        $model->secret = 'super_secret_key_thingy';
        return $model;
    }

    public function save(Models\WatchedRepo &$model)
    {
        return true;
    }
}
