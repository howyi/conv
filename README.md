[![Build Status](https://travis-ci.org/howyi/conv.svg?branch=master)](https://travis-ci.org/howyi/conv)
[![Coverage Status](https://coveralls.io/repos/github/howyi/conv/badge.svg?branch=master#konbu)](https://coveralls.io/github/howyi/conv?branch=master)
# conv
MySQL migration query auto generate from schema

```
composer require howyi/conv --dev
```

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
  age:
    type: tinyint(3)
    comment: 'User age'
    attribute: [nullable, unsigned]
primary_key:
  - user_id
index:
  id_age:
    is_unique: true
    column: [user_id, age]
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

### スキーマの書き方
`tbl_user.yaml` というように名前を設定することで、ファイル名がテーブル名として扱われる
```yaml
comment: 'User management table'
column:
# columnはキーでカラム名を設定する
  user_id:
    type: int(11)
    comment: 'User ID'
  age:
    type: tinyint(3)
    comment: 'User age'
    attribute: [nullable, unsigned]
# attributeに指定できる属性は [auto_increment, unsigned, nullable] の三種類
primary_key:
  - user_id
# PKは配列になっており、複数指定することで複合PKとなる
index:
# indexはキーでindex名を設定する
  id_age:
    is_unique: true
    column: [user_id, age]
# columnを複数指定することで複合INDEXとなる
```
