<?php namespace Alerts\Models;

class PatchChunk
{
    /**
     * @var string
     */
    public $header;

    /**
     * @var PatchLine[]
     */
    public $lines;
}
