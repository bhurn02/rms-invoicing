# Here's how to list local branches sorted by recent activity (most recent on top):

# Just date and time without seconds
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M) %(refname:short)'

# With commit hash and author
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(objectname:short) %(authorname)'

# With commit message
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(contents:subject)'

# With author and commit message
git for-each-ref --sort=-committerdate refs/heads/ --format='%(committerdate:format:%Y-%m-%d %H:%M:%S) %(refname:short) %(authorname) %(contents:subject)'


# Here's the complete code sequence with hardcoded datetime and backup tag for merging a branch into main:
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

# 6. Push the updated main branch
git push origin main