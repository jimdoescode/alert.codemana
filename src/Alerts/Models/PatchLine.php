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
    public $number;

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
