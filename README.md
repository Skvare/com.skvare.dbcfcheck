# CiviCRM Database Custom Field Check Extension

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL_v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)
[![CiviCRM Version](https://img.shields.io/badge/CiviCRM-5.45+-orange.svg)](https://civicrm.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.0+-777BB4.svg)](https://php.net/)

A CiviCRM extension that prevents database errors by monitoring custom field table row sizes and detecting orphaned custom fields.

![Extension Screenshot](/images/dbcheck_report_on_page.png)

## 🚀 Overview

This extension addresses critical database issues in CiviCRM by:
- **Preventing row size limit errors** when adding custom fields
- **Detecting orphaned custom fields** with missing database columns
- **Providing system status integration** for proactive monitoring

MySQL has a hard limit of 65,535 bytes per table row. When custom fields push tables beyond this limit, database operations fail. This extension calculates current row sizes and prevents new field creation when approaching this limit.

## ✨ Key Features

### 🛡️ Row Size Protection
- **Automatic monitoring** of custom group table sizes
- **Smart prevention** of field additions that would exceed MySQL limits
- **User-friendly warnings** with clear explanations
- **1,000 byte safety buffer** to ensure reliable operation

### 🔍 Orphaned Field Detection
- **Comprehensive scanning** for custom fields missing database columns
- **Tabular reports** showing problematic fields
- **Safe deletion tools** for cleanup operations
- **Database integrity verification**

### 📊 System Status Integration
- **Native CiviCRM status checker** integration
- **Proactive alerts** for database issues
- **Administrative dashboard** warnings

![Status Check Screenshot](/images/screenshot_1.png)

## 📋 Requirements

| Component | Version |
|-----------|---------|
| **PHP** | 7.0+ |
| **CiviCRM** | 5.45+ |
| **MySQL** | 5.6+ |

## 📦 Installation

### Option 1: CLI Installation (Recommended)

**Using CV (CiviCRM CLI tool):**

```bash
# Download and install from ZIP
cd <extension-directory>
cv dl com.skvare.dbcfcheck@https://github.com/skvare/com.skvare.dbcfcheck/archive/master.zip
```

**Using Git:**

```bash
# Clone repository
git clone https://github.com/skvare/com.skvare.dbcfcheck.git
cd com.skvare.dbcfcheck

# Enable extension
cv en dbcfcheck
```

### Option 2: Manual Installation

1. Download the latest release ZIP file
2. Extract to your CiviCRM extensions directory
3. Navigate to **Administer → System Settings → Extensions**
4. Find "Database Custom Field Check" and click **Install**

### Verification

After installation, verify the extension is working:
1. Go to **Administer → System Settings → System Status**
2. Look for custom field validation messages
3. Navigate to any Custom Fields page to see row size monitoring

## 🔧 Technical Details

### How Row Size Calculation Works

The extension uses sophisticated MySQL metadata queries to calculate exact byte usage:

```sql
SELECT
  COLUMN_NAME,
  DATA_TYPE,
  CHARACTER_MAXIMUM_LENGTH,
  NUMERIC_PRECISION,
  COLLATION_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'your_custom_table'
```

**Column Type Calculations:**
- **Numeric types**: `TINYINT(1)`, `INT(4)`, `BIGINT(8)`, `DECIMAL(variable)`
- **String types**: `VARCHAR(n × charset_bytes)`, `TEXT(65,535)`
- **Date types**: `DATE(3)`, `DATETIME(8)`, `TIMESTAMP(4)`
- **Binary types**: Exact byte storage requirements

### Character Set Impact

Row size varies significantly based on database collation:

| Collation | Bytes per Character | Impact on VARCHAR(255) |
|-----------|-------------------|----------------------|
| `latin1` | 1 byte | 255 bytes |
| `utf8_unicode_ci` | 3 bytes | 765 bytes |
| `utf8mb4_unicode_ci` | 4 bytes | 1,020 bytes |

## 🚨 Troubleshooting

### Common Error Messages

**"Row size too large" Error:**
```sql
ALTER TABLE civicrm_value_custom_table_12
  ADD COLUMN `new_field_123` VARCHAR(255)

[nativecode=1118 ** Row size too large. The maximum row size
for the used table type, not counting BLOBs, is 65535...]
```

**Solution:** Use the extension's monitoring to prevent this error before it occurs.

### Orphaned Custom Fields

**Symptoms:**
- Custom fields appear in CiviCRM admin but don't function
- Database errors when accessing certain records
- Missing columns in database tables

**Resolution:**
1. Navigate to **Administer → System Settings → System Status**
2. Review custom field validation warnings
3. Use the extension's cleanup tools to remove orphaned fields

### Performance Considerations

- Row size calculations are cached for 1 hour
- Only runs on custom field administration pages
- Minimal impact on normal CiviCRM operations

## 🔗 Useful Resources

### MySQL Documentation
- [Row Size Limits](https://dev.mysql.com/doc/mysql-reslimits-excerpt/8.0/en/column-count-limit.html#row-size-limits)
- [InnoDB Page Size](https://dev.mysql.com/doc/refman/5.6/en/innodb-parameters.html#sysvar_innodb_page_size)

### Project References
- [Original Issue #15557](https://projects.skvare.com/issues/15557)
- [Related Issue #13848](https://projects.skvare.com/issues/13848)

## 🤝 Contributing

We welcome contributions! Please:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Development Setup

```bash
# Clone for development
git clone https://github.com/skvare/com.skvare.dbcfcheck.git
cd com.skvare.dbcfcheck

# Install in development mode
cv en dbcfcheck --dev
```

## 📝 License

This project is licensed under the **GNU Affero General Public License v3.0** - see the [LICENSE.txt](LICENSE.txt) file for details.

## 🆘 Support

- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs via [GitHub Issues](https://github.com/skvare/com.skvare.dbcfcheck/issues)
- **Community**: Join CiviCRM community forums for general questions

---

**Made by [Skvare](https://github.com/Skvare)**

**Supporting Organizations**
[Skvare](https://skvare.com/contact)

## Protect Your Database Today

Don't wait for a row size error to disrupt your operations. Install proactive monitoring that prevents problems before they occur, helps clean up existing issues, and provides ongoing visibility into your custom field architecture.

Your future self will thank you for preventing a database crisis that could have been avoided with proper monitoring and limits.

---

**[Contact us](https://skvare.com/contact) for support or to learn more** about implementing database protection and custom field management in your CiviCRM environment.
