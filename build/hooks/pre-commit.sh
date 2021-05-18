#!/usr/bin/env bash

cEnd=$'\e[0m'
cOrange=$'\e[39m'
cGreen=$'\e[36m'
cRed=$'\e[31m'
cBlue=$'\e[34m'

csFixerBin=./vendor/bin/php-cs-fixer
stagedProjectFiles=$(git diff-index --cached --name-only --diff-filter=ACMR HEAD -- )
changedInstallFiles=$(git diff --cached --name-only | grep 'build/installer/' | wc -l | xargs)

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

if [[ -n "${stagedProjectFiles}" ]]; then
    let stashed=false
    if [[ "$(git diff --name-only)" != "" ]]; then
        echo "${cOrange} Found unstaged changes, stashing first!"
        git stash push --keep-index -m PRECOMMIT
        stashed=true
    fi

    for file in $stagedProjectFiles; do if echo "$file" | egrep -q "\.(php)$"; then
        if ! php -l -d display_errors=0 "$file"; then
            echo "${cRed} File ${file} contains syntax errors.${cEnd}"
            echo && exit 2;
        fi

        echo "${cGreen} Fixing file ${file}...${cEnd}"
        result=$( $csFixerBin fix --config=build/php-cs-fixer/config.php $file )
        echo "$result" | sed -e "s/\(.*\)/$cBlue\1$cEnd/g; s/\+  /$cGreen+  /g; s/-  /$cRed-  /g;"
        git add "$file"
    fi; done

    if [ "$stashed" = true ]; then
        echo "Restoring stashed changes..."
        git stash pop
    fi
fi
