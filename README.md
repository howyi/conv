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
