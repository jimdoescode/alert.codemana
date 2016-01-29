<?php namespace Alerts\Models;

class WatchedFile
{
    /**
     * @var int
     */
    public $id;

    /**
     * The id of the user watching this file
     *
     * @var string[]
     */
    public $emails;

    /**
     * The name of this file
     *
     * @var string
     */
    public $name;

    /**
     * What lines in this file are being watched.
     *
     * @var int[]
     */
    public $lines;
}
