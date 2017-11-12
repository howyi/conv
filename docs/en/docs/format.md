# Schema format

Schema perse using [howyi/evi](https://github.com/howyi/evi), You can use the provided functions eval, reference($ref), inheritance($ext).

# Table {#table}  
File name is interpreted as table name.
_ex) tbl_user.yml = tbl_user_

require keys
* comment
* column

optional keys
* primary_key
* index
* partition

### Columns
require keys
* type  
  MySQL Type, ex) `int(10)`
* comment

optional keys
* default
* attribute  
  usable: `auto_increment, unsigned, nullable`

### Index
require keys
* is_unique (bool)
* column  
  column string array  

### Partition

### Table schema example
```yaml
comment: 'User management table'
column:
# Sets the column name with the key
  user_id:
    type: int(11)
    comment: 'User ID'
  age:
    type: tinyint(3)
    comment: 'User age'
    attribute: [nullable, unsigned]
# Attribute [auto_increment, unsigned, nullable]
primary_key:
  - user_id
# PK
index:
# Index is the key to set the index name
  id_age:
    is_unique: true
    column: [user_id, age]
# By specifying multiple columns, it becomes compound INDEX
```

# View {#view}  

### View schema example
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
