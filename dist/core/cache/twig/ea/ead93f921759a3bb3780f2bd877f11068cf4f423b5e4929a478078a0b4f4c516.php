<?php

/* nav.twig */
class __TwigTemplate_9d6769566a0c2874f7d15e234181aa65e644b2461a232eae5e1e509f32f27900 extends Twig_Template
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
        echo "<div class=\"pager\">
  <a href=\"";
        // line 2
        echo twig_escape_filter($this->env, (isset($context["prev"]) ? $context["prev"] : null), "html", null, true);
        echo "\" class=\"prev ";
        echo twig_escape_filter($this->env, (isset($context["prev_id"]) ? $context["prev_id"] : null), "html", null, true);
        echo "\">&laquo;";
        echo twig_escape_filter($this->env, (isset($context["prev_title"]) ? $context["prev_title"] : null), "html", null, true);
        echo "</a>
  <a href=\"index.html\" class=\"index\">Index</a>
  <a href=\"";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["next"]) ? $context["next"] : null), "html", null, true);
        echo "\" class=\"next ";
        echo twig_escape_filter($this->env, (isset($context["next_id"]) ? $context["next_id"] : null), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, (isset($context["next_title"]) ? $context["next_title"] : null), "html", null, true);
        echo "&raquo;</a>
</div>
";
    }

    public function getTemplateName()
    {
        return "nav.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 4,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nav.twig", "/Volumes/Data/Users/aklump/Code/Packages/bash/loft_docs/core/plugins/twig/tpl/nav.twig");
    }
}
