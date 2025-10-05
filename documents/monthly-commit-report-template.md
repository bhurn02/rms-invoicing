# 📅 Monthly Git Commit Report Generator - AI Template

## 🎯 Purpose
Automated generation of comprehensive monthly commit reports for multiple projects with consistent formatting, analysis, and documentation.

## 📋 Template Parameters
Replace these placeholders with actual values when using this template:

- `{YEAR}` - Target year (e.g., 2025)
- `{MONTH}` - Target month (e.g., 09, 10, 11)

### Auto-Generated Values (based on YEAR and MONTH):
- `{MONTH_NAME}` - Full month name (auto-calculated from MM)
- `{START_DATE}` - Month start date (YYYY-MM-01)
- `{END_DATE}` - Month end date (YYYY-MM-31 or last day of month)
- `{OUTPUT_FILE}` - Generated report filename (YYYY-MM-all-commits-pretty.md)

## 🏢 Project Paths

- **RMS Project**: `C:\System\rms`
- **HR2v2 Project**: `C:\System\hr2v2`

## 🔧 Git Extraction Commands

### Step 1: Extract RMS Commits
```bash
cd C:\System\rms
echo "# {MONTH_NAME} {YEAR} Git Commits - RMS Project" > {OUTPUT_FILE}-rms.md
echo "" >> {OUTPUT_FILE}-rms.md
git log --all --decorate --since="{START_DATE}" --until="{END_DATE}" --date=format:'%Y-%m-%d %H:%M:%S' --pretty=format:'%C(green)%ad%C(reset) %C(blue)%an%C(reset) %C(cyan)%D%C(reset) %C(yellow)%h%C(reset) %s' >> {OUTPUT_FILE}-rms.md
```

### Step 2: Extract HR2v2 Commits
```bash
cd C:\System\hr2v2
echo "# {MONTH_NAME} {YEAR} Git Commits - HR2v2 Project" > {OUTPUT_FILE}-hr2v2.md
echo "" >> {OUTPUT_FILE}-hr2v2.md
git log --all --decorate --since="{START_DATE}" --until="{END_DATE}" --date=format:'%Y-%m-%d %H:%M:%S' --pretty=format:'%C(green)%ad%C(reset) %C(blue)%an%C(reset) %C(cyan)%D%C(reset) %C(yellow)%h%C(reset) %s' >> {OUTPUT_FILE}-hr2v2.md
```

## 📊 AI Processing Instructions

### 1. Data Extraction
- Read both project commit files
- Parse commit data: date, author, branch, hash, message
- Extract commit types (feat, docs, fix, merge, chore, backup, hotfix, refactor)

### 2. Data Processing
- **Categorize commits** by type with emoji indicators:
  - 🚀 feat/feat: Features
  - 📝 docs/docs: Documentation
  - 🔧 fix/fix: Bug fixes
  - 🔀 merge/merge: Merges
  - 🧹 chore/chore: Maintenance
  - 🏷️ backup/backup: Backups
  - 🔥 hotfix/hotfix: Hotfixes
  - 🔧 refactor/refactor: Refactoring

### 3. Statistical Analysis
- Count commits by type and calculate percentages
- Group commits by week within the month
- Identify key milestones and major features
- Analyze contributor activity and focus areas
- Calculate project distribution (RMS vs HR2v2)

### 4. Report Generation
- Create consolidated markdown file: `{OUTPUT_FILE}`
- Apply consistent formatting with CSS styling for date-time columns
- Include summary tables, weekly breakdowns, and milestone sections
- Add contributor analysis and activity trends
- Include current timestamp for generation date

## 📝 Output Format Structure

```markdown
# 📅 {MONTH_NAME} {YEAR} Git Commits - All Projects

> **Projects:** HR2v2 System & RMS (QR Meter Reading System)  
> **Period:** {MONTH_NAME} 1-{LAST_DAY}, {YEAR}  
> **Total Commits:** {TOTAL_COUNT} (Combined)

<style>
table {
  table-layout: fixed;
  width: 100%;
}
td:first-child {
  white-space: nowrap;
  width: 160px;
}
</style>

---

## 📊 Summary
[Commit type statistics table]

## 🗓️ Commits by Week
[Weekly commit breakdowns with full details]

## 🏷️ Key Milestones
[Major achievements and features by project]

## 👥 Contributors
[Author statistics and focus areas]

## 📈 Activity Trends
[Analysis of development patterns]

## 📋 Project Distribution
[Project-specific summaries]
```

## 🎯 Usage Instructions

1. **Set Parameters**: Only replace `{YEAR}` and `{MONTH}` with actual values
2. **Run Extraction**: Execute git commands for both projects (AI will handle date calculations)
3. **AI Processing**: Provide extracted files to AI with this template
4. **Review Output**: Generated report will be in `{OUTPUT_FILE}`

## 📅 Example Usage

For October 2025:
- `{YEAR}` = 2025
- `{MONTH}` = 10
- **Auto-calculated:**
  - `{MONTH_NAME}` = October
  - `{START_DATE}` = 2025-10-01
  - `{END_DATE}` = 2025-10-31
  - `{OUTPUT_FILE}` = 2025-10-all-commits-pretty.md

For September 2025:
- `{YEAR}` = 2025
- `{MONTH}` = 09
- **Auto-calculated:**
  - `{MONTH_NAME}` = September
  - `{START_DATE}` = 2025-09-01
  - `{END_DATE}` = 2025-09-30
  - `{OUTPUT_FILE}` = 2025-09-all-commits-pretty.md

---

*Template created: October 3, 2025*  
*Purpose: Automated monthly commit reporting for multi-project development tracking*
