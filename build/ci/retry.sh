#!/usr/bin/env bash

# Pulled from Github to replace the travis_retry function which is unavailable within scripts:
# https://github.com/travis-ci/travis-build/blob/9ec05b37295a6d9/lib/travis/build/bash/travis_retry.bash

travis_retry() {
    local result=0
    local count=1
    while [[ "${count}" -le 3 ]]; do
        [[ "${result}" -ne 0 ]] && {
            echo -e "\\n${ANSI_RED}The command \"${*}\" failed. Retrying, ${count} of 3.${ANSI_RESET}\\n" >&2
        }
        "${@}"
        result="${?}"
        if [[ $result -eq 0 ]]; then break; fi
        count="$((count + 1))"
        sleep 1
    done

    [[ "${count}" -gt 3 ]] && {
        echo -e "\\n${ANSI_RED}The command \"${*}\" failed 3 times.${ANSI_RESET}\\n" >&2
    }

    return "${result}"
}
