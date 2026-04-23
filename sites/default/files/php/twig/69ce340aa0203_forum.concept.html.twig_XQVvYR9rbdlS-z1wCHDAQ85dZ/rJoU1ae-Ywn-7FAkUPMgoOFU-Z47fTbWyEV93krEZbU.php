<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* @help_topics/forum.concept.html.twig */
class __TwigTemplate_14c8926e1d971686ed42791464cbaf27 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 5
        yield "<h2>";
        yield t("What is a forum?", array());
        yield "</h2>
<p>";
        // line 6
        yield t("A forum is nested hierarchy of discussions, where users post topics and comment on them.", array());
        yield "</p>
<h2>";
        // line 7
        yield t("What is the forum hierarchy?", array());
        yield "</h2>
<p>";
        // line 8
        yield t("The forum hierarchy consists of:", array());
        yield "</p>
<ul>
    <li>";
        // line 10
        yield t("<em>Forums</em> (for example, <em>Recipes for cooking vegetables</em>)", array());
        yield "</li>
    <li>";
        // line 11
        yield t("Optional <em>containers</em> can be used to group similar forums (for example, <em>Recipes</em>). Forums can be inside containers and vice versa.", array());
        yield "</li>
    <li>";
        // line 12
        yield t("<em>Forum topics</em> submitted by users (for example, <em>How to cook potatoes</em>), which start discussions.", array());
        yield "</li>
    <li>";
        // line 13
        yield t("<em>Comments</em> submitted by users (for example, <em>You wash the potatoes first and then...</em>).", array());
        yield "</li>
</ul>
<p>";
        // line 15
        yield t("The <em>forums</em> and <em>containers</em> in the hierarchy are implemented as terms in a hierarchical taxonomy vocabulary. The topics are content items posted in forums (not in containers), and replies are comments on each topic item. Deeply nested hierarchies of forums and containers are generally difficult for users to understand and navigate, so it is recommended to keep your hierarchy simple.", array());
        yield "</p>
<h2>";
        // line 16
        yield t("Managing and using forums overview", array());
        yield "</h2>
<p>";
        // line 17
        yield t("The core Forum module supplies a content type called <em>Forum topic</em>, along with associated comment type and taxonomy vocabulary. As with other comment types, you can configure comments on forum topics to be threaded or unthreaded. See the related topics listed below for specific tasks.", array());
        yield "</p>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@help_topics/forum.concept.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  87 => 17,  83 => 16,  79 => 15,  74 => 13,  70 => 12,  66 => 11,  62 => 10,  57 => 8,  53 => 7,  49 => 6,  44 => 5,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@help_topics/forum.concept.html.twig", "/var/www/html/ESIM-content-drupal10-9dec/core/modules/forum/help_topics/forum.concept.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["trans" => 5];
        static $filters = [];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['trans'],
                [],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
