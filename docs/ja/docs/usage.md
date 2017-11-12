### インストール {#install}

`composer require howyi/conv --dev`

### 注釈 {#annotation}
- 開発時のマイグレーションを生成するライブラリのため、symfony/consoleのコマンドを用意した上で、内部で各機能を呼び出す使用を想定している
- 差分からマイグレーションを生成を行うのみの為、実際のマイグレーション管理に使用する場合は生成されたクエリを各マイグレーションの形式に変換させる必要がある。

### 設定ファイルの作成 {#configuration_file}

カレントワーキングディレクトリ直下に `conv.yml` を作成することでデフォルトのDB設定を変更可能

```yaml
default:
    engine:  InnoDB
    charset: utf8mb4
    collate: utf8mb4_bin
```

### 既存DBのスキーマファイル化 {#database_to_schema}

ディレクトリをDB、ディレクトリ内の各定義ファイルを1テーブルとして扱う。

1. 実DBを `\Conv\Structure\DatabaseStructure` へ変換する

  ```php
  // DB名 conv のDBを `DatabaseStructure` に変換する場合 (PDOのパラメータは例)

  $dbname = 'conv';
  $pdo = new \PDO("mysql:host=localhost;dbname=$dbname;charset=utf8;", 'root', '');

  $dbs = \Conv\DatabaseStructureFactory::fromPDO($pdo, $dbname);
  ```

2. 変換した `\Conv\Structure\DatabaseStructure` をディレクトリに反映する

  ```php
  // 生成した `DatabaseStructure` のテーブル定義ファイルをディレクトリ `database/` 以下に配置する場合

  $dir = 'database'
  SchemaReflector::fromDatabaseStructure(
    $dir,
    $dbs,
    new \Conv\Operator($this->getHelper('question'), $input, $output)
  );
  ```

### 差分マイグレーションの生成 {#generate_migration}

変更前の `DatabaseStructure` と、変更後の `DatabaseStructure` を `\Conv\MigrationGenerator` に渡すことで、差分を解消するマイグレーションが生成される。

1. `DatabaseStructure` を作成
  - 実DBから生成する場合
    ```php
    $dbname = 'conv';
    $pdo = new \PDO("mysql:host=localhost;dbname=$dbname;charset=utf8;", 'root', '');

    $actualDbs = \Conv\DatabaseStructureFactory::fromPDO($pdo, $dbname);
    ```
  - スキーマのディレクトリから生成する場合
    ```php
    $dir = 'database'
    $schemaDbs = \Conv\DatabaseStructureFactory::fromDir($dir);
    ```

2. マイグレーションを生成
```php
// 引数は 変更前, 変更後, Operator の順とする。
$alter = MigrationGenerator::generate(
    $actualDbs,
    $schemaDbs,
    new \Conv\Operator($this->getHelper('question'), $input, $output)
);
```

生成された `$alter->getMigrationList()` はマイグレーションの配列となっている。  
各マイグレーションは `::getUp()` と `::getDown()` が実装されており、
- getUpで変更を行うクエリ
- getDownで変更をもとに戻すクエリ
を取得できる。

```
foreach ($alter->getMigrationList() as $migration) {
    var_dump($migration->getUp());
    var_dump($migration->getDown());
}
```

3. 各マイグレーションツールのSQL保管方法に合わせてクエリを変換する
- convはこの機能を提供しないため、個別に作成する必要がある。
