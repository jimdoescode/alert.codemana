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
     * @var PatchChunk[]
     */
    public $chunks;

    /**
     * @var array
     */
    public $editors;
}
