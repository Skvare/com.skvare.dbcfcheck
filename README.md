# com.skvare.dbcfcheck

![Screenshot](/images/screenshot_2.png)

This extention help to provide errors relate to custom field when we add
 field to custom table more than row size limit on the table.

Based on row size available for the table, we disable the add new custom
field button.

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

![Screenshot](/images/screenshot_1.png)

## Reference
* https://projects.skvare.com/issues/15557
* https://projects.skvare.com/issues/13848
