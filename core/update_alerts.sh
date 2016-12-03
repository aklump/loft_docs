#!/usr/bin/env bash
# Messages in this file will display to the user after updates.

# Warnings about breaking changes.
if [ "${get_version_return:0:3}" == "0.8" ]; then
    echo_yellow "Review CHANGELOG.txt for breaking changes in version 0.8"
fi
