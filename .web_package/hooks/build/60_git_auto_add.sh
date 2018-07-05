#!/usr/bin/env bash

#
# @file
# Automatically add certain generated files to git during build
#
git=$(type git >/dev/null 2>&1 && which git)
if [ "$git" ]; then
    # Note to support symlinks, we should cd first (per git).
    (cd $7 git add README.md)
    (cd $7 git add html)
    (cd $7 git add public_html)
    (cd $7 git add my_pretend_module/help)
    (cd $7/core && git add vendor)
    (cd $7/core && git add composer.lock)
fi
