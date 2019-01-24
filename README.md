[![Build Status](https://travis-ci.org/howyi/conv.svg?branch=master)](https://travis-ci.org/howyi/conv)
[![Coverage Status](https://coveralls.io/repos/github/howyi/conv/badge.svg?branch=master#konbu)](https://coveralls.io/github/howyi/conv?branch=master)
# conv
Core package for [howyi/conv-laravel](https://github.com/howyi/conv-laravel)  

Generate MySQL migration queries from actual DB and DDL  

```
composer require howyi/conv --dev
```

#### Query sample
tbl_user.sql
```sql
CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `age` tinyint(3) UNSIGNED COMMENT 'User age',
  PRIMARY KEY (`user_id`),
  KEY `id_age` (`user_id`, `age`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='User management table';
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

## CONTRIBUTING
### install
```bash
$ composer install
```
### check (before pull-request)
```bash
$ composer check-fix
```