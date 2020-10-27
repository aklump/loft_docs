#!/usr/bin/env bash
bump_sass=$(type sass >/dev/null &2>&1 && which sass)

$bump_sass  --style=compressed --update "$7/core/plugins/twig/tpl/sass/style.scss:$7/core/plugins/twig/tpl/style.css"
