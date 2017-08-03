[![Build Status](https://travis-ci.org/howyi/conv.svg?branch=master)](https://travis-ci.org/howyi/conv)
[![Coverage Status](https://coveralls.io/repos/github/howyi/conv/badge.svg?branch=master#konbu)](https://coveralls.io/github/howyi/conv?branch=master)
# conv
MySQL migration query auto generate from schema

### 概要
指定したPDOのテーブルとYAMLのスキーマ差分からMySQLのクエリを生成する

#### YAML定義例
tbl_user.yml
```yaml
comment: 'User management table'
column:
  user_id:
    type: int(11)
    comment: 'User ID'
  name:
    type: int(11)
    comment: 'User name'
    attribute: [nullable]
  age:
    type: int(11)
    comment: 'User age'
    attribute: [nullable]
primary_key:
  - user_id
index:
  name:
    is_unique: true
    column: [name]
```

### インストール
```
composer require howyi/conv --dev
```
### 使用方法
##### 基本部分
`Conv\Factory\DatabaseStructureFactory` によって生成される `Conv\Structure\DatabaseStructure` をベースとしてマイグレーションの生成などを行う
- テーブルをDatabaseStructureにする場合は `DatabaseStructureFactory::fromPDO()`
- スキーマディレクトリをDatabaseStructureにする場合は `DatabaseStructureFactory::fromDir()` を使用する
##### 現在のテーブルをスキーマファイルにする
`Conv\Util\SchemaReflector::fromDatabaseStructure` に生成した `DatabaseStructure` とディレクトリのパスを与えることで、`DatabaseStrucuture` を1テーブル1ファイルとしてディレクトリに保存する
##### 差分のマイグレーションを生成する
`Conv\Generator\MigrationGenerator` に `DatabaseStructure` を渡すことで `Conv\Migration\Database\Migration` が作成される
