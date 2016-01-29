<?php namespace Alerts\Repositories\Dummy;

use Alerts\Models;
use Alerts\Repositories\Interfaces;

class Users implements Interfaces\Users
{
    public function getById($userId)
    {
        // TODO: Implement getById() method.
    }

    public function getByEmail($email)
    {
        // TODO: Implement getByEmail() method.
    }

    public function getAll(array $filters = [], $count = 10, $start = 0)
    {
        // TODO: Implement getAll() method.
    }

    public function save(Models\User &$user)
    {
        // TODO: Implement save() method.
    }
}
