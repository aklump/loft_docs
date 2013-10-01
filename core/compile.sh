#!/bin/bash

##
 # End execution with a message
 #
 # @param string $1
 #   A message to display
 #
function end() {
  echo
  echo $1
  echo
  exit;
}

##
 # Checks to see if a file was generated and displays a message
 #
 # @param string $1
 #   filename to check
 #
 # @return NULL
 #   Sets the value of global $func_name_return
 #
function _check_file() {
  if [ -f "$1" ]
  then
    echo "`tput setaf 2`$1 has been generated.`tput op`"
  else
    echo "`tput setaf 1`Failed generating $1`tput op`"
  fi
}

##
 # Load the configuration file
 #
 # Lines that begin with [ or # will be ignored
 # Format: Name = "Value"
 # Value does not need wrapping quotes if no spaces
 #
function load_config() {
  if [ ! -f core-config.sh ]; then
    cp "core/config-example" core-config.sh
    installing=1
  fi

  # defaults
  docs_php=$(which php)
  docs_lynx=$(which lynx)
  docs_markdown='core/Markdown.pl'
  docs_source_dir='source'
  docs_kit_dir='kit'
  docs_doxy_dir='doxygene'
  docs_html_dir='html'
  docs_text_dir='text'
  docs_drupal_dir='advanced_help'
  docs_tmp_dir="core/tmp"

  #Determine which is our tpl dir
  docs_tpl_dir='core/tpl'
  if [ -d 'tpl' ]; then
    docs_tpl_dir='tpl'
  fi

  # Ini file
  docs_help_ini=$(ls source/*help.ini)

  # custom
  parse_config core-config.sh

  docs_text_enabled=1
  if ! lynx_loc="$(type -p "$docs_lynx")" || [ -z "$lynx_loc" ]; then
    echo "`tput setaf 3`Lynx not found; .txt files will not be created.`tput op`"
    docs_text_enabled=0
  fi
}

##
 # Parse a config file
 #
 # @param string $1
 #   The filepath of the config file
 #
function parse_config() {
  if [ -f $1 ]
  then
    while read line; do
      if [[ "$line" =~ ^[^#[]+ ]]; then
        name=${line% =*}
        value=${line##*= }
        if [ "$name" ]
        then
          eval docs_$name=$value
        fi
      fi
    done < $1
  fi
}

# Pull in config vars
installing=0
load_config

# These dirs need to be created
declare -a dirs=("$docs_html_dir" "$docs_text_dir" "$docs_drupal_dir" "$docs_kit_dir" "$docs_tmp_dir" "$docs_source_dir" "$docs_doxy_dir");

# These dirs need to be emptied
declare -a dirs_to_empty=("$docs_html_dir" "$docs_text_dir" "$docs_drupal_dir" "$docs_tmp_dir");

# If source does not exist then copy core example
if [ ! -d "$docs_source_dir" ]; then
  rsync -av "core/source-example/" $docs_source_dir/
fi

# Empty dirs
for var in "${dirs_to_empty[@]}"
do
  if [ "$var" ] && [ -d "$var" ]; then
    rm -rf $var;
  fi
done

# Assert dir exists
for var in "${dirs[@]}"
do
  if [ ! "$var" ]; then
    end "`tput setaf 1`Bad Config`tput op`"
    return
  fi
  if [ ! -d "$var" ]
  then
    # Create new empty compiled dir
    mkdir $var
  fi
done

# Delete the text directory if no lynx
if [ "$docs_text_enabled" -eq 0 ]; then
  rmdir $docs_text_dir
fi


# Installation steps
if [ $installing -eq 1 ]; then
  echo "`tput setaf 3`Installing Loft Docs...`tput op`"
  if [ -f .gitignore ]; then
    rm .gitignore
  fi
fi

# Build index.html from home.php
echo '' > "$docs_kit_dir/index.kit"
$docs_php "core/page_vars.php" "$docs_help_ini" "index" >> "$docs_kit_dir/index.kit"
$docs_php "core/home.php" "$docs_help_ini" "$docs_tpl_dir" >> "$docs_kit_dir/index.kit"

_check_file "$docs_kit_dir/index.kit"

# Copy over files in the tmp directory, but compile anything with a .md
# extension as it goes over; this is our baseline html that we will further
# process for the intended audience.
for file in $docs_source_dir/*; do
  if [ -f "$file" ]
  then
    basename=${file##*/}

    # Process .md files and output as .htmlll

    if echo "$file" | grep -q '.md$'; then
      basename=$(echo $basename | sed 's/\.md$//g').html
      perl $docs_markdown --html4tags $file > $docs_tmp_dir/$basename

    # Css files pass through to the html dir
    elif echo "$file" | grep -q '.css$'; then
      cp $file $docs_html_dir/$basename
      _check_file "$docs_html_dir/$basename"

    # Html files pass through to drupal and html
    elif echo "$file" | grep -q '.html$'; then
      cp $file $docs_drupal_dir/$basename
      _check_file "$docs_drupal_dir/$basename"
      cp $file $docs_html_dir/$basename
      _check_file "$docs_html_dir/$basename"

    # text files pass through to drupal, html and txt
    elif echo "$file" | grep -q '.txt$'; then
      cp $file $docs_drupal_dir/$basename
      _check_file "$docs_drupal_dir/$basename"
      cp $file $docs_html_dir/$basename
      _check_file "$docs_html_dir/$basename"
      cp $file $docs_text_dir/$basename
      _check_file "$docs_text_dir/$basename"

    # Rename the .ini file; we should only ever have one
    elif echo "$file" | grep -q '.ini$' && [ ! -f "$docs_drupal_dir/$docs_drupal_module.$basename" ]; then
      cp $file "$docs_drupal_dir/$docs_drupal_module.$basename"
      _check_file "$docs_drupal_dir/$docs_drupal_module.$basename"

    # All files types pass through to drupal
    else
      cp $file $docs_drupal_dir/$basename
      _check_file "$docs_drupal_dir/$basename"
    fi

  elif [ -d "$file" ]; then
    basename=${file##*/}
    echo "Copying dir $basename..."
    rsync -rv $docs_source_dir/$basename/ $docs_drupal_dir/$basename/
    rsync -rv $docs_source_dir/$basename/ $docs_html_dir/$basename/
  fi
done

# Now process all tmp files into kit>html and advanced help files>html
for file in $docs_tmp_dir/*.html; do
  if [ -f "$file" ]
  then
    basename=${file##*/}
    basename=$(echo $basename | sed 's/\.html$//g')
    html_file=$basename.html
    kit_file=$basename.kit
    tmp_file=$basename.kit.txt

    # Convert to plaintext
    if lynx_loc="$(type -p "$docs_lynx")" && [ ! -z "$lynx_loc" ]; then
      textname=`basename $file html`
      textname=${textname}txt
      $docs_lynx --dump $file > "$docs_text_dir/${textname}"
      _check_file "$docs_text_dir/${textname}"
    fi

    # Process each file for advanced help markup
    $docs_php "core/advanced_help.php" "$docs_tmp_dir/$html_file" "$docs_drupal_module" > $docs_drupal_dir/$html_file

    # Convert to offline .html
    echo '' > $docs_kit_dir/$tmp_file
    $docs_php "core/page_vars.php"  "$docs_help_ini" "$basename" >> $docs_kit_dir/$tmp_file
    echo '<!-- @include ../'$docs_tpl_dir'/header.kit -->' >> $docs_kit_dir/$tmp_file
    cat $file >> $docs_kit_dir/$tmp_file
    echo '<!-- @include ../'$docs_tpl_dir'/footer.kit -->' >> $docs_kit_dir/$tmp_file

    $docs_php "core/iframes.php" "$docs_kit_dir/$tmp_file" "$docs_credentials" > $docs_kit_dir/$kit_file
    rm $docs_kit_dir/$tmp_file
    _check_file "$docs_kit_dir/$kit_file"

  fi
done

# Get all stylesheets
for file in $docs_tpl_dir/*.css; do
  if [ -f "$file" ]; then
    basename=${file##*/}
    cp $file $docs_html_dir/$basename
    _check_file "$docs_html_dir/$basename"
  fi
done

# Drupal likes to have a README.txt file in the module root directory; this
# little step facilitates that need. It also supports other README type
# files.
if [ "$docs_README" ]; then
  destinations=($docs_README)
  for destination in "${destinations[@]}"
  do
    readme_file=${destination##*/}
    readme_dir=${destination%/*}
    if echo "$readme_file" | grep -q '.txt$'; then
      readme_source="$docs_text_dir/$readme_file"
    elif echo "$readme_file" | grep -q '.md$'; then
      readme_source="$docs_source_dir/$readme_file"
    fi
    if [ -d "$readme_dir" ]; then
      echo  "$readme_source" "$readme_dir/"
      cp "$readme_source" "$destination"
      _check_file "$destination"
    fi
  done
fi

# Doxygene implementation
echo 'Not yet implemented' > "$docs_doxy_dir/README.md"

# Cleanup tmp dir
if [ -d "$docs_tmp_dir" ]; then
  rm -rf $docs_tmp_dir
fi
