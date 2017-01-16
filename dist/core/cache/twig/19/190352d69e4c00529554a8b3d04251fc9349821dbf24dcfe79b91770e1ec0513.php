<?php

/* page.twig */
class __TwigTemplate_a120b22ee0acf9507fe6aa1212d1360d1dc408b835b5e9b7a6bb240b8295ca86 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>

<html>
<head>
  <title>";
        // line 5
        echo twig_escape_filter($this->env, (isset($context["title"]) ? $context["title"] : null), "html", null, true);
        echo "</title>
  <link href=\"search/tipuesearch.css\" rel=\"stylesheet\">
  <style type=\"text/css\" media=\"all\">
    @import url(\"style.css\");
  </style>
  
  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js\"></script>
  <script src=\"search/tipuesearch_content.js\"></script>
  <script src=\"search/tipuesearch_set.js\"></script>
  <script src=\"search/tipuesearch.min.js\"></script>
  <script src=\"js/core.js\"></script>

</head>

<body class=\"";
        // line 19
        echo twig_escape_filter($this->env, twig_join_filter((isset($context["classes"]) ? $context["classes"] : null), " "), "html", null, true);
        echo "\">
<header>
  ";
        // line 21
        $this->loadTemplate("nav.twig", "page.twig", 21)->display($context);
        // line 22
        echo "</header>

<div class=\"search__wrapper\">
  <form action=\"search--results.html\">
    <input type=\"text\" class=\"search-input\" name=\"q\" id=\"tipue_search_input\" autocomplete=\"off\" required>
  </form>
</div>

";
        // line 30
        if ((isset($context["is_index"]) ? $context["is_index"] : null)) {
            // line 31
            echo "  <h1>Index</h1>
";
        } else {
            // line 33
            echo "  <h1>";
            echo twig_escape_filter($this->env, (isset($context["title"]) ? $context["title"] : null), "html", null, true);
            echo "</h1>
  <div class=\"breadcrumb\"><a href=\"index.html\">Index</a></div>
";
        }
        // line 36
        echo "
<section>";
        // line 37
        echo (isset($context["content"]) ? $context["content"] : null);
        echo "</section>

<div class=\"search__results\">
  <div id=\"tipue_search_content\"></div>
</div>

<footer>
  ";
        // line 44
        $this->loadTemplate("nav.twig", "page.twig", 44)->display($context);
        // line 45
        echo "  
  <div id=\"footer-legaleeze\">
    <p class=\"legaleeze center\">Version: ";
        // line 47
        echo twig_escape_filter($this->env, (isset($context["version"]) ? $context["version"] : null), "html", null, true);
        echo " &bull; Last Updated: ";
        echo twig_escape_filter($this->env, (isset($context["date"]) ? $context["date"] : null), "html", null, true);
        echo "</p>
  </div>
</footer>

<script>
  \$(document).ready(function () {
    \$('#tipue_search_input').tipuesearch();
  });
</script>

</body>
</html>

";
    }

    public function getTemplateName()
    {
        return "page.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  91 => 47,  87 => 45,  85 => 44,  75 => 37,  72 => 36,  65 => 33,  61 => 31,  59 => 30,  49 => 22,  47 => 21,  42 => 19,  25 => 5,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "page.twig", "/Volumes/Data/Users/aklump/Code/Packages/bash/loft_docs/core/plugins/twig/tpl/page.twig");
    }
}
