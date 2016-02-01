<?php namespace Alerts\Repositories\Pdo;

use Alerts\Models;
use Alerts\Services;
use Alerts\Repositories\Interfaces;

class Users implements Interfaces\Users
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
     * @param \PDO $pdo
     * @param Services\Converter $converter
     */
    public function __construct(\PDO $pdo, Services\Converter $converter)
    {
        $this->pdo = $pdo;
        $this->converter = $converter;
    }

    /**
     * Get a user based on Id.
     *
     * @param integer $userId
     * @return Models\User or null
     */
    public function getById($userId)
    {
        $users = $this->getAll(['id' => $userId], 1);
        return empty($users) ? null : $users[0];
    }

    /**
     * Get all users that match the specified filter.
     *
     * @param array $filters
     * @param int $count
     * @param int $start
     * @return Models\User[]
     */
    public function getAll(array $filters = [], $count = 10, $start = 0)
    {
        $temp = new Models\User();

        //This along with pdo prepared statements should prevent a sql injection attack
        $columns = $this->converter->filterArrayToSqlColumns($filters, $temp);
        $whereClause = empty($columns) ? '' : 'WHERE '.implode('=? AND ', array_keys($columns)).'=?';
        $query = $this->pdo->prepare("SELECT * FROM users {$whereClause} LIMIT {$start}, {$count}");
        $entities = $query->execute(array_values($columns)) ? $query->fetchAll(\PDO::FETCH_ASSOC) : [];

        return $this->converter->entityArraysToModels($entities, $temp);
    }

    /**
     * Creates or updates a user in a data source.
     *
     * @param Models\User &$user
     * @return bool
     */
    public function save(Models\User &$user)
    {
        //Convert the user model but don't convert the githubRepos field on the model.
        $modelArray = $this->converter->modelToEntityArray($user, ['githubRepos']);

        //Prevent someone from setting a different ID for a preexisting entry.
        if (isset($modelArray['id'])) {
            unset($modelArray['id']);
        }

        //Set the updated_at value in the database
        $modelArray['updated_at'] = date('Y-m-d G:i:s');

        $keys = array_keys($modelArray);
        $vals = array_values($modelArray);

        if (isset($user->id)) {
            $query = $this->pdo->prepare('UPDATE users SET '.implode('=?, ', $keys).'=? WHERE id=? LIMIT 1');
            $vals[] = $user->id;
            return $query->execute($vals);
        } else {
            $query = $this->pdo->prepare('INSERT INTO users ('.implode(',', $keys).') VALUES ('.implode(',', array_fill(0, count($vals), '?')).')');
            if ($query->execute($vals)) {
                //Refetch to populate everything properly.
                $user->id = $this->pdo->lastInsertId();
                return true;
            }
        }
        return false;
    }
}
