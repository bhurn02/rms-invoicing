# Git Command Template

## 1. Branch Listing Commands

### List Local Branches (Sorted by Recent Activity)

```bash
# Just date and time without seconds
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M) %(refname:short)'

# With commit hash and author
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(objectname:short) %(authorname)'

# With commit message
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(contents:subject)'

# With author and commit message
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(authorname) %(contents:subject)'

# Show all remote branches with commit info
git for-each-ref --sort=-committerdate refs/remotes/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(objectname:short) %(authorname)'

# Show both local and remote branches
git for-each-ref --sort=-committerdate refs/heads/ refs/remotes/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(objectname:short) %(authorname)'

# Show all commits across all branches (more comprehensive view)
git log --all --decorate --date=format:'%Y-%m-%d %H:%M:%S' --pretty=format:'%C(green)%ad%C(reset) %C(blue)%an%C(reset) %C(cyan)%D%C(reset) %C(yellow)%h%C(reset) %C(magenta)[%S]%C(reset) %s'

# Here's the command to get September 2025 commits and save them to an MD file:
echo "# September 2025 Git Commits" > 2025-09-commits.md
echo "" >> 2025-09-commits.md
git log --all --decorate --date=format:'%Y-%m-%d %H:%M:%S' --pretty=format:'%C(green)%ad%C(reset) %C(blue)%an%C(reset) %C(cyan)%D%C(reset) %C(yellow)%h%C(reset) %C(magenta)[%S]%C(reset) %s' >> 2025-09-commits.md
```

## 2. Branch Merging Sequence

### Complete Merge Process with Backup Tag

```bash
# 1. Switch to main branch
git checkout main

# 2. Pull latest changes from main
git pull origin main

# 3. Create backup tag with hardcoded datetime stamp (2025-09-10)
git tag backup-main-20250910-1430

# 4. Push the backup tag to remote
git push origin backup-main-20250910-1430

# 5. Merge hotfix branch into main with no-fast-forward
git merge hotfix/re-employment-issue --no-ff -m "Merge hotfix/re-employment-issue into main: Fix re-employment issue"

# 5.1 Archive Memory Bank Documentation
mkdir -p documentation/memory-bank/tla-performance-optimization
mv memory-bank/* documentation/memory-bank/tla-performance-optimization/
git add documentation/memory-bank/tla-performance-optimization/
git add memory-bank/  # Stage the empty folder
git commit -m "docs: Archive TLA performance optimization memory-bank to documentation folder"
git commit -m "docs: Restore TLA performance optimization documentation to memory-bank folder after main branch merge."


# 6. Push the updated main branch
git push origin main


### List Commits Specific to Current Branch

```bash
# Just date and time without seconds
git log main..HEAD --pretty=format:"%ad %h %s" --date=format:"%Y-%m-%d %H:%M"

# With commit hash and author
git log main..HEAD --pretty=format:"%h %ad %an %s" --date=format:"%Y-%m-%d %H:%M"
```

## 4. Reset Commands

### Discard Changes and Reset

```bash
# Hard reset - discards all changes and resets to last commit
git reset --hard HEAD

# Restore all files in working directory
git restore .
```