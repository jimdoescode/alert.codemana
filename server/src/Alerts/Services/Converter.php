<?php namespace Alerts\Services;

class Converter
{
    /**
     * Convert a single entity array into the specified model
     *
     * @param array $entity
     * @param object $model
     * @return object
     */
    public function entityArrayToModel(array $entity, $model)
    {
        $reflectedModel = new \ReflectionClass(get_class($model));

        foreach ($entity as $key => $value) {
            $key = $this->underscoresToCamelCase($key);
            if (property_exists($model, $key)) {
                $property = $reflectedModel->getProperty($key);
                $type = $this->getPropertyTypeFromDocComment($property->getDocComment());
                if ($type === 'int' || $type === 'integer') {
                    $model->{$key} = filter_var($value, FILTER_VALIDATE_INT);
                } elseif ($type === 'bool' || $type === 'boolean') {
                    $model->{$key} = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                } elseif ($type === '\\datetime') {
                    $model->{$key} = new \DateTime($value);
                } else {
                    $model->{$key} = $value;
                }
            }
        }
        return $model;
    }

    /**
     * Convert multiple entity arrays into an array of the specified model
     *
     * @param array $entities
     * @param object $model
     * @return array
     */
    public function entityArraysToModels(array $entities, $model)
    {
        $models = [];
        foreach ($entities as $entity) {
            $models[] = $this->entityArrayToModel($entity, clone $model);
        }
        return $models;
    }

    /**
     * Converts a string of JSON into a model
     *
     * @param  string $entity The JSON entity to convert to a model
     * @param  object $model The model to convert the entity to
     * @return object or null if the entity or model are null
     */
    public function jsonEntityToModel($entity, $model)
    {
        if (is_string($entity)) {
            $entity = json_decode($entity, true);
        }

        return is_null($entity) ? null : $this->entityArrayToModel($entity, $model);
    }

    /**
     * Converts a model object into an entity array.
     *
     * @param $model
     * @param array $igoredFields
     * @return array
     */
    public function modelToEntityArray($model, $igoredFields = [])
    {
        $entity = [];

        $params = get_object_vars($model);
        foreach($params as $key => $value) {
            if ($value && !in_array($key, $igoredFields)) {
                $entity[$this->camelCaseToUnderscores($key)] = $value;
            }
        }

        return $entity;
    }

    /**
     * Converts an array representing model fields to an array of sql columns and values.
     * This is ideal for sanitizing filter data because any fields not present in the model
     * are not returned in the sql array.
     *
     * @param array $filter
     * @param object $model
     * @return array
     */
    public function filterArrayToSqlColumns(array $filter, $model)
    {
        $cols = [];
        foreach ($filter as $key => $val) {
            if (property_exists($model, $key)) {
                $cols[$this->camelCaseToUnderscores($key)] = $val;
            }
        }
        return $cols;
    }

    /**
     * Convert a string from camelCase to underscore_case
     *
     * @param  string $string
     * @return string
     */
    private function camelCaseToUnderscores($string)
    {
        $underscoreString = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);

        // So the above preg_replace would have replaced all instances
        // of a lower-case letter followed by an upper-case letter with
        // an underscore and the upper-case letter.  Like this:
        //
        // getURLForAPage -> get_URLFor_APage
        //
        // See the problem? We now have to go the other way too,
        // and find all instances of an upper-case letter followed
        // by a lower-case letter
        $underscoreString = preg_replace('/([^_])([A-Z])([a-z])/', '$1_$2$3', $underscoreString);

        // Now: get_URLFor_APage -> get_URL_For_A_Page
        // And we can make it all lower-case
        return strtolower($underscoreString);
    }

    /**
     * Convert a string from underscore_case to camelCase
     *
     * @param  string $string
     * @return mixed
     */
    private function underscoresToCamelCase($string)
    {
        return preg_replace('/_(.?)/e', "strtoupper('$1')", $string);
    }

    /**
     * Parses out the type from a doc comment string.
     * The format it's looking for is '@var type'. If
     * it's found the type will be returned in lower
     * case if it's not found null is returned.
     *
     * @param string $comment
     * @return null|string
     */
    private function getPropertyTypeFromDocComment($comment)
    {
        if (preg_match('/@var\s+([^\s]+)/', $comment, $matches) !== false) {
            return strtolower($matches[1]);
        }

        return null;
    }
}
