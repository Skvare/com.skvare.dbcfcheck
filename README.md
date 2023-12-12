# com.skvare.dbcfcheck

![Screenshot](/images/screenshot_2.png)

This extension helps to prevent errors related to custom fields when we add
 fields to a custom table that exceed the row size limit on the table.

Based on the row size available for the table, we disabled the add new
custom field button.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM 5.45+

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl com.skvare.dbcfcheck@https://github.com/skvare/com.skvare.dbcfcheck/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/skvare/com.skvare.dbcfcheck.git
cv en dbcfcheck
```

## Usage
This extension also provides tabular reports for custom fields where the column is
missing. You can delete these fields through the UI if possible or in the
database directly.

![Screenshot](/images/screenshot_1.png)

## Errors

If you check the civicrm logs, you may find similar errors.
```sql
ALTER TABLE civicrm_value_event_participant_consents_and_authorizati_12
  ADD COLUMN `subsidy_450` varchar(255),
  ADD INDEX INDEX_subsidy_450 ( subsidy_450 )

[nativecode=1118 ** Row size too large. The maximum row size for the used table type, not counting BLOBs, is 65535. This includes storage overhead, check the manual. You have to change some columns to TEXT or BLOBs]
```
We cannot increase the size  of innodb_page_size parameter, it is read only, it configured once during creation of database for more detail refer:
https://dev.mysql.com/doc/refman/5.6/en/innodb-parameters.html#sysvar_innodb_page_size

Row-size information is available at: https://dev.mysql.com/doc/mysql-reslimits-excerpt/8.0/en/column-count-limit.html#row-size-limits

Column size varies based on the `COLLATION` type used on each column.
* utf8_unicode_ci (bytes per char: 3)
* utf8mb4_unicode_ci (bytes per char: 4)
* latin1 (bytes per char: 1)

## Reference
* https://projects.skvare.com/issues/15557
* https://projects.skvare.com/issues/13848
