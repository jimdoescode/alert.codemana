<?php namespace Alerts\Models;

class WatchedRepo
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var WatchedFile[]
     */
    public $watchedFiles;
}
