<?php namespace Alerts\Repositories\Interfaces;

use \Alerts\Models;

interface Users
{
    /**
     * @param $email
     * @return Models\User | null
     */
    public function getByEmail($email);

    /**
     * @param Models\User &$user
     * @return bool
     */
    public function save(Models\User &$user);
}
