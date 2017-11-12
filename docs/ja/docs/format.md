# 書き方

スキーマのパーサには [howyi/evi](https://github.com/howyi/evi) を使用しており、eviで提供される eval、参照($ref)、継承($ext)の機能を使用することが出来る。

# テーブル {#table}  
ファイル名がテーブル名として解釈される
_ex) tbl_user.yml = tbl_user_

必須キー
* comment
* column

追加出来るキー
* primary_key
* index
* partition

### カラム
必須キー
* type  
  MySQLの型と同じ `int(10)` 等
* comment

追加出来るキー
* default
* attribute  
  usable: `auto_increment, unsigned, nullable`

### インデックス
require keys
* is_unique (bool)
* column  
  column string array  

### パーティション

### テーブル定義例
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

# ビュー {#view}  

### ビュー定義例
```yaml
type: view
algorithm: merge
alias:
  tbl_user:         tu
  tbl_user_address: tua
  tbl_country:      tc
column:
  user_id:      tu.user_id
  user_name:    tu.name
  address_line: tua.address_line
  zip_code:     tua.zip_code
  country_name: tc.country_name
from:
  reference: tu
  joins:
    - join:
        factor: tua
        on: tu.user_id = tua.user_id
    - left_join:
        factor: tc
        on: tua.country_id = tc.country_id
```
