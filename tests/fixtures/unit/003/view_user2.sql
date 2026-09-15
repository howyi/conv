CREATE ALGORITHM=MERGE VIEW `view_user2` AS select
  `tu`.`user_id` AS `user_id`,
  `tu`.`name` AS `name`,
  `tua`.`address_line` AS `address_line`,
  `tua`.`zip_code` AS `zip_code`,
  `tc`.`country_name` AS `country_name`
from ((`tbl_user` `tu` join `tbl_user_address` `tua` on((`tu`.`user_id` = `tua`.`user_id`))) left join `tbl_country` `tc` on((`tua`.`country_id` = `tc`.`country_id`)))
