<?php namespace Alerts\Models;

class PatchLine
{
    /**
     * @var string
     */
    public $raw;

    /**
     * @var int
     */
    public $oldNumber;

    /**
     * @var int
     */
    public $newNumber;

    /**
     * @var bool
     */
    public $isAdded = false;

    /**
     * @var bool
     */
    public $isRemoved = false;

    /**
     * @var string
     */
    public $parsed;
}
