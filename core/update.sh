#!/bin/bash
#
# @file
# Update the core loft_docs files
#
# USAGE:
# 1. Make this file executable
# 2. Backup your project
# 3. ./update.sh
# 4. Test your project
#
# CREDITS:
# In the Loft Studios
# Aaron Klump - Web Developer
# PO Box 29294 Bellingham, WA 98228-1294
# aim: theloft101
# skype: intheloftstudios
#
#
# LICENSE:
# Copyright (c) 2013, In the Loft Studios, LLC. All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#
#   1. Redistributions of source code must retain the above copyright notice,
#   this list of conditions and the following disclaimer.
#
#   2. Redistributions in binary form must reproduce the above copyright notice,
#   this list of conditions and the following disclaimer in the documentation
#   and/or other materials provided with the distribution.
#
#   3. Neither the name of In the Loft Studios, LLC, nor the names of its
#   contributors may be used to endorse or promote products derived from this
#   software without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY IN THE LOFT STUDIOS, LLC "AS IS" AND ANY EXPRESS
# OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
# OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
# EVENT SHALL IN THE LOFT STUDIOS, LLC OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
# INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
# BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
# DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
# OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
# NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
# EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
#
# The views and conclusions contained in the software and documentation are
# those of the authors and should not be interpreted as representing official
# policies, either expressed or implied, of In the Loft Studios, LLC.
#
#
# @ingroup loft_docs
# @{
#

# Find the directory above this script, it is root
root_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
root_dir=${root_dir%/*}

if [ ! -d "$root_dir/core" ] && [ ! -f "$root_dir/core_version.info" ]; then
  echo "`tput setaf 1`Update failed. Corrupt file structure.`tput op`"
  exit
fi

if [ -d "$root_dir/tmp" ]; then
  echo "`tput setaf 3`You must delete $root_dir/tmp before updating.`tput op`"
  exit
else
  mkdir -p $root_dir/tmp
fi

cd $root_dir/tmp

# Download the master branch
curl -O -L https://github.com/aklump/loft_docs/archive/master.zip
unzip -q master.zip;
cd $root_dir

# Update the core files
docs_update="$root_dir/tmp/loft_docs-master/"

cp -v $docs_update/README.md $root_dir/
cp -v $docs_update/core-version.info $root_dir/
rsync -av --delete $docs_update/core/ $root_dir/core/ --exclude=Markdown.pl

rm -rf $root_dir/tmp
echo "`tput setaf 2`Update complete.`tput op`"
