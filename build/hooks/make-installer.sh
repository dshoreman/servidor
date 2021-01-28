#!/usr/bin/env bash

install_files_changed=$(git diff --cached --name-only | grep 'build/installer/' | wc -l | xargs)

if [[ install_files_changed -gt 0 ]]; then
    make installer
    git add setup.sh
fi
