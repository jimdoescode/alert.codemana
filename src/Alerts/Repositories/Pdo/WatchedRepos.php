<?php namespace Alerts\Repositories\Pdo;

use Alerts\Models;
use Alerts\Services;
use Alerts\Repositories\Interfaces;

class WatchedRepos implements Interfaces\WatchedRepos
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Services\Converter
     */
    private $converter;

    /**
     * WatchedRepos constructor.
     * @param \PDO $pdo
     * @param Services\Converter $converter
     */
    public function __construct(\PDO $pdo, Services\Converter $converter)
    {
        $this->pdo = $pdo;
        $this->converter = $converter;
    }

    /**
     * @param int $id
     * @return Models\WatchedRepo
     */
    public function getById($id)
    {
        // TODO: Implement getById() method.
    }

    /**
     * @param array $filters
     * @param int $count
     * @param int $start
     * @return Models\WatchedRepo[]
     */
    public function getAll(array $filters = [], $count = 10, $start = 0)
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @param string $name
     * @return Models\WatchedRepo
     */
    public function createNew($name)
    {
        // TODO: Implement createNew() method.
    }

    /**
     * @param Models\WatchedRepo &$model
     * @return boolean
     */
    public function save(Models\WatchedRepo &$model)
    {
        // TODO: Implement save() method.
    }
}
