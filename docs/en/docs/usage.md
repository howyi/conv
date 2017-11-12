### Install {#install}

`composer require howyi/conv --dev`

### Annotation {#annotation}
-
It is assumed that you use the [symfony/console] (https://github.com/symfony/console) command and call each function internally.
- This library only "Generate Migration". If you migration query uses other migration tool.

### Configure {#configuration_file}

Put `conv.yml` to CurrentWorkingDirectory.

```yaml
default:
    engine:  InnoDB
    charset: utf8mb4
    collate: utf8mb4_bin
```

### Database to Schema {#database_to_schema}

Directory = Database, File = Table(or View)

1. Database to `\Conv\Structure\DatabaseStructure`

  ```php
  // Database `conv` to `DatabaseStructure` (sample \PDO)

  $dbname = 'conv';
  $pdo = new \PDO("mysql:host=localhost;dbname=$dbname;charset=utf8;", 'root', '');

  $dbs = \Conv\DatabaseStructureFactory::fromPDO($pdo, $dbname);
  ```

2. Generated `\Conv\Structure\DatabaseStructure` to Directory

  ```php
  // Generated `DatabaseStructure` to under the directory `database/`.

  $dir = 'database'
  SchemaReflector::fromDatabaseStructure(
    $dir,
    $dbs,
    new \Conv\Operator($this->getHelper('question'), $input, $output)
  );
  ```

### Generate migration {#generate_migration}

Generate migration is Before `DatabaseStructure` and After `DatabaseStructure` param to `\Conv\MigrationGenerator`.

1. Generate `DatabaseStructure`
  - from Database (\PDO)
    ```php
    $dbname = 'conv';
    $pdo = new \PDO("mysql:host=localhost;dbname=$dbname;charset=utf8;", 'root', '');

    $actualDbs = \Conv\DatabaseStructureFactory::fromPDO($pdo, $dbname);
    ```
  - from Directory (path)
    ```php
    $dir = 'database'
    $schemaDbs = \Conv\DatabaseStructureFactory::fromDir($dir);
    ```

2. Generate migration
```php
// Param: BeforeDBS, AfterDBS, Operator
$alter = MigrationGenerator::generate(
    $actualDbs,
    $schemaDbs,
    new \Conv\Operator($this->getHelper('question'), $input, $output)
);
```

Generated `$alter->getMigrationList()` is array of `Migration`.  
Migration have `::getUp()` と `::getDown()`.
- getUp to convert after database query.
- getDown to back before database query.


```
foreach ($alter->getMigrationList() as $migration) {
    var_dump($migration->getUp());
    var_dump($migration->getDown());
}
```

3. Query convert for migration tool.
- This library only "Generate Migration". If you migration query uses other migration tool.
