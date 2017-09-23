<?php

namespace Conv\Factory;

use Conv\Structure\ViewStructure;
use Conv\Structure\ViewRawStructure;
use Symfony\Component\Yaml\Yaml;
use Conv\Util\SchemaKey;
use Conv\Util\Evaluator;
use Conv\Util\Config;
use Conv\Util\SchemaValidator;

class ViewStructureFactory
{
    /**
     * @param string $viewName
     * @param array  $spec
     * @return ViewStructure
     */
    public static function fromSpec(string $viewName, array $spec): ViewStructure
    {
        $properties = array_diff_key($spec, array_flip(SchemaKey::VIEW_KEYS));

        $algorithm = isset($spec[SchemaKey::VIEW_ALGORITHM]) ? $spec[SchemaKey::VIEW_ALGORITHM] : null;

        return new ViewStructure(
            $viewName,
            $algorithm,
            $spec[SchemaKey::VIEW_ALIAS],
            $spec[SchemaKey::VIEW_COLUMN],
            $spec[SchemaKey::VIEW_FROM],
            $properties
        );
    }

    /**
     * @param \PDO   $pdo
     * @param string $dbName
     * @param string $viewName
     * @return ViewRawStructure
     */
    public static function fromView(\PDO $pdo, string $dbName, string $viewName): ViewRawStructure
    {
        $createQuery = $pdo->query('SHOW CREATE VIEW view_user')->fetch()['Create View'];
        return new ViewRawStructure(
            $viewName,
            $createQuery
        );
    }
}
