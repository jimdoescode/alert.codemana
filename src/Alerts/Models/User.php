<?php namespace Alerts\Models;

class User
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $githubAccessToken;

    /**
     * @var Repo[]
     */
    public $githubRepos;
}
