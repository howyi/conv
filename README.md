[![Build Status](https://travis-ci.org/howyi/conv.svg?branch=master)](https://travis-ci.org/howyi/conv)
[![Coverage Status](https://coveralls.io/repos/github/howyi/conv/badge.svg?branch=master)](https://coveralls.io/github/howyi/conv?branch=master)
# conv
Migration auto generate from schema

#### 概要
指定したPDOのテーブルとYAMLのスキーマ差分からクエリを生成する

### YAML定義例
```yaml
table: tbl_user
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
