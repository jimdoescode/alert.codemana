<?php namespace Alerts\Models;

class PatchFile
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $raw;

    /**
     * @var PatchLine[]
     */
    public $lines;

    /**
     * @var string
     */
    public $editors;
}
