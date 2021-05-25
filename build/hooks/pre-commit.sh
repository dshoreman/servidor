#!/usr/bin/env bash

cEnd=$'\e[0m'
cOrange=$'\e[33m'
cGreen=$'\e[32m'
cRed=$'\e[31m'
cBlue=$'\e[34m'

csFixerBin=./vendor/bin/php-cs-fixer
changedInstallFiles=$(git diff-index --cached --name-only HEAD | grep -c 'build/installer/')

if [[ changedInstallFiles -gt 0 ]]; then
    echo "${cOrange}Detected change in install scripts, rebuilding installer!${cEnd}"

    if make installer && git add setup.sh; then
        echo "${cGreen}setup.sh updated.${cEnd}"
        echo
    else
        echo "${cRed}Installer build failed, aborting commit.${cEnd}"
        echo && exit 1
    fi
fi

if [[ ! -f "$csFixerBin" ]] || [[ ! -x "$csFixerBin" ]]; then
    echo "${cOrange}PHP CS Fixer is not installed or executable. Attempting composer install...${cEnd}"

    if ! composer install -q; then
        echo "${cRed}Composer install failed. PHP CS fixes skipped.${cEnd}"
        echo && exit 0
    fi
fi

mapfile -t stagedPhpFiles < <(git diff-index --cached --name-only --diff-filter=ACMR HEAD | grep -E '\.(php)$')
if [[ ${#stagedPhpFiles[@]} -gt 0 ]]; then
    declare -a safeFiles
    stashed=false

    echo "${cBlue} Checking ${#stagedPhpFiles[@]} files for syntax errors${cEnd}"
    for file in "${stagedPhpFiles[@]}"; do
        if ! php -l -d display_errors=0 "$file"; then
            echo "${cRed} File ${file} contains syntax errors.${cEnd}"
            echo && exit 2;
        fi
        if git diff --quiet --name-only "${file}"; then
            # Working copy is clean so file is fully staged and safe to fix
            safeFiles+=("${file}")
        fi
    done

    # Start by loading unstaged changes in PHP files
    mapfile -t unstagedPhpFiles < <(git diff --name-only | grep -E '\.(php)$')

    # So we can stash the changes we don't want to commit
    if [[ ${#unstagedPhpFiles[@]} -gt 0 ]]; then
        echo "${cBlue} Stashing unstaged changes from ${#unstagedPhpFiles[@]} PHP files${cEnd}"
        git stash push --keep-index -m PRECOMMIT
        stashed=true
    fi
    # Then do a dry-run on staged files and exit if clean (real fix has unreliable exit codes)
    if $csFixerBin fix --dry-run --config=build/php-cs-fixer/config.php; then
        echo "${cGreen} Nothing to fix!${cEnd}"
        [ $stashed = true ] && git stash pop --quiet
        exit 0
    fi

    # Now fix for real, and add the safe files.
    echo "${cGreen} Fixing files...${cEnd}"
    result=$( $csFixerBin fix --config=build/php-cs-fixer/config.php )
    echo "$result" | sed -e "s/\(.*\)/$cBlue\1$cEnd/g; s/\+  /$cGreen+  /g; s/-  /$cRed-  /g;"
    for file in "${safeFiles[@]}"; do
        git add "${file}" && echo "${cOrange} Added fixes from ${file}${cEnd}"
    done

    # All fixes added? If there's no stash, we're good to go!
    mapfile -t postFixDirty < <(git diff --name-only | grep -E '\.(php)$')
    if [[ ${#postFixDirty[@]} -eq 0 ]]; then
        echo "${cGreen} All files fixed!${cEnd}"
        if [ $stashed = true ]; then
            echo "${cBlue} Restoring stashed changes...${cEnd}"
            git stash pop --quiet
        fi
        echo
        echo "${cGreen} Checks complete. Proceeding with commit!${cEnd}"
        exit 0
    fi

    echo
    echo "${cOrange} Could not apply all fixes!${cEnd}"
    echo "${cOrange} Fixes will be re-applied after reverting unstaged changes.${cEnd}"

    # Otherwise, reset any changes and apply the stash...
    echo "${cBlue} Resetting fixes made to non-safe files...${cEnd}"
    for file in "${postFixDirty[@]}"; do
        echo " -- ${file}" && git checkout -- "${file}"
    done
    if [ $stashed = true ]; then
        echo "${cBlue} Applying previously unstaged changes from stash...${cEnd}"
        git stash pop --quiet
    fi

    # ...before fixing one more time, but on all files, for a manual git add -p
    echo "${cBlue} Applying fixes on all files...${cEnd}"
    result=$( $csFixerBin fix --config=build/php-cs-fixer/config.php )
    echo "$result" | sed -e "s/\(.*\)/$cBlue\1$cEnd/g; s/\+  /$cGreen+  /g; s/-  /$cRed-  /g;"

    echo "${cRed} Some files were fixed but could not be added automatically.${cEnd}"
    echo
    echo "${cOrange}  Review the changes manually then re-commit with${cEnd}"
    echo "${cOrange}   git commit -e --file .git/COMMIT_EDITMSG${cEnd}"
    exit 3
fi
