[![Build Status](https://travis-ci.org/howyi/conv.svg?branch=master)](https://travis-ci.org/howyi/conv)
[![Coverage Status](https://coveralls.io/repos/github/howyi/conv/badge.svg?branch=master#konbu)](https://coveralls.io/github/howyi/conv?branch=master)
# conv
MySQL migration query auto generate from schema

### 概要
指定したPDOのテーブルとYAMLのスキーマ差分からMySQLのクエリを生成する

#### YAML定義例
tbl_user.yml
```yaml
comment: ユーザ管理テーブル
column:
  user_id:
    type: int(10)
    auto_increment: true
    unsigned: false
    nullable: false
    comment: ユーザID
  name:
    type: varchar(255)
    nullable: false
    comment: ユーザ名
  age:
    type: smallint(5)
    unsigned: false
    nullable: false
    comment: 年齢
  address:
    type: varchar(255)
    nullable: true
    comment: 住所
  registered_at:
    type: datetime
    nullable: false
    comment: 登録日時
primaryKey:
  - user_id
index:
  layer:
    isUnique: false
    column:
      - age
      - registered_at

```
