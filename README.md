[![Build Status](https://travis-ci.org/howyi/conv.svg?branch=master)](https://travis-ci.org/howyi/conv)
[![Coverage Status](https://coveralls.io/repos/github/howyi/conv/badge.svg?branch=master#konbu)](https://coveralls.io/github/howyi/conv?branch=master)
# conv
MySQL migration query auto generate from schema

```
composer require howyi/conv --dev
```

- wiki: https://github.com/howyi/conv/wiki

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
generated migration
UP
```sql
CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `age` tinyint(3) UNSIGNED COMMENT 'User age',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY ` id_age` (`user_id`, `age`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='User management table';
```
DOWN
```sql
DROP TABLE `tbl_user`;
```
