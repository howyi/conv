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

#### YAML sample
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
    is_unique: false
    column: [user_id, age]
```
#### Generated migration  
UP
```sql
CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `age` tinyint(3) UNSIGNED COMMENT 'User age',
  PRIMARY KEY (`user_id`),
  KEY `id_age` (`user_id`, `age`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='User management table';
```
DOWN
```sql
DROP TABLE `tbl_user`;
```

#### Migrated and Modified
```yaml
comment: 'All user management table'
column:
  user_id:
    type: bigint(20)
    comment: 'User ID'
    attribute: [auto_increment, unsigned]
  name:
    type: varchar(255)
    comment: 'User name'
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

#### And regenerated migration  
UP
```sql
ALTER TABLE `tbl_user`
  COMMENT 'All user management table',
  DROP INDEX `id_age`,
  CHANGE `user_id` `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  ADD COLUMN `name` varchar(255) NOT NULL COMMENT 'User name' AFTER `user_id`,
  ADD UNIQUE `id_age` (`user_id`, `age`);
```
DOWN
```sql
ALTER TABLE `tbl_user`
  DROP INDEX `id_age`,
  DROP COLUMN `name`,
  CHANGE `user_id` `user_id` int(11) NOT NULL COMMENT 'User ID',
  ADD INDEX `id_age` (`user_id`, `age`),
  COMMENT 'User management table';
```
