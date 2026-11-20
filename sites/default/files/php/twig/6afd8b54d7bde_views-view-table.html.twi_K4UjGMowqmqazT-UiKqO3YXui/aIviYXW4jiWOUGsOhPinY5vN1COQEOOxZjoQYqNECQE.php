<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\MacroNamespace;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedTestError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* core/themes/stable9/templates/views/views-view-table.html.twig */
class __TwigTemplate_9d93b141891d208580b8483533dc02e8 extends Template
{
    private Source $source;
    /**
     * @var array<string, MacroNamespace>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        $this->sandbox = $env->getExtension(SandboxExtension::class)->getChecker();
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 35
        $context["classes"] = [("cols-" . Twig\Extension\CoreExtension::length($this->env->getCharset(),         // line 36
($context["header"] ?? null))), (((($tmp =         // line 37
($context["responsive"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("responsive-enabled") : ("")), (((($tmp =         // line 38
($context["sticky"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("sticky-enabled sticky-header") : (""))];
        // line 41
        yield "<table";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 41), "html", null, true);
        yield ">
  ";
        // line 42
        if ((($tmp = ($context["caption_needed"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 43
            yield "    <caption>
    ";
            // line 44
            if ((($tmp = ($context["caption"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 45
                yield "      ";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["caption"] ?? null), "html", null, true);
                yield "
    ";
            } else {
                // line 47
                yield "      ";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["title"] ?? null), "html", null, true);
                yield "
    ";
            }
            // line 49
            yield "    ";
            if ((!Twig\Extension\CoreExtension::testEmpty(($context["summary_element"] ?? null)))) {
                // line 50
                yield "      ";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["summary_element"] ?? null), "html", null, true);
                yield "
    ";
            }
            // line 52
            yield "    </caption>
  ";
        }
        // line 54
        yield "  ";
        if ((($tmp = ($context["header"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 55
            yield "    <thead>
      <tr>
        ";
            // line 57
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["header"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["column"]) {
                // line 58
                yield "          ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["column"], "default_classes", [], "any", false, false, true, 58)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 59
                    yield "            ";
                    // line 60
                    $context["column_classes"] = ["views-field", ("views-field-" . (($_v0 =                     // line 62
($context["fields"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0[(($_v1 = $context["key"]) instanceof \Stringable && (is_array($_v0) || $_v0 instanceof \ArrayObject || $_v0 instanceof \ArrayIterator) ? (string) $_v1 : $_v1)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["fields"] ?? null), $context["key"], [], "array", false, false, true, 62)))];
                    // line 65
                    yield "          ";
                }
                // line 66
                yield "          <th";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "attributes", [], "any", false, false, true, 66), "addClass", [($context["column_classes"] ?? null)], "method", false, false, true, 66), "setAttribute", ["scope", "col"], "method", false, false, true, 66), "html", null, true);
                yield ">";
                // line 67
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["column"], "wrapper_element", [], "any", false, false, true, 67)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 68
                    yield "<";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "wrapper_element", [], "any", false, false, true, 68), "html", null, true);
                    yield ">";
                    // line 69
                    if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["column"], "url", [], "any", false, false, true, 69)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 70
                        yield "<a href=\"";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "url", [], "any", false, false, true, 70), "html", null, true);
                        yield "\" title=\"";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "title", [], "any", false, false, true, 70), "html", null, true);
                        yield "\" rel=\"nofollow\">";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "content", [], "any", false, false, true, 70), "html", null, true);
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "sort_indicator", [], "any", false, false, true, 70), "html", null, true);
                        yield "</a>";
                    } else {
                        // line 72
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "content", [], "any", false, false, true, 72), "html", null, true);
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "sort_indicator", [], "any", false, false, true, 72), "html", null, true);
                    }
                    // line 74
                    yield "</";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "wrapper_element", [], "any", false, false, true, 74), "html", null, true);
                    yield ">";
                } else {
                    // line 76
                    if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["column"], "url", [], "any", false, false, true, 76)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 77
                        yield "<a href=\"";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "url", [], "any", false, false, true, 77), "html", null, true);
                        yield "\" title=\"";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "title", [], "any", false, false, true, 77), "html", null, true);
                        yield "\" rel=\"nofollow\">";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "content", [], "any", false, false, true, 77), "html", null, true);
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "sort_indicator", [], "any", false, false, true, 77), "html", null, true);
                        yield "</a>";
                    } else {
                        // line 79
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "content", [], "any", false, false, true, 79), "html", null, true);
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "sort_indicator", [], "any", false, false, true, 79), "html", null, true);
                    }
                }
                // line 82
                yield "</th>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['key'], $context['column'], $context['_parent']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 84
            yield "      </tr>
    </thead>
  ";
        }
        // line 87
        yield "  <tbody>
    ";
        // line 88
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["rows"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 89
            yield "      <tr";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["row"], "attributes", [], "any", false, false, true, 89), "html", null, true);
            yield ">
        ";
            // line 90
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["row"], "columns", [], "any", false, false, true, 90));
            foreach ($context['_seq'] as $context["key"] => $context["column"]) {
                // line 91
                yield "          ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["column"], "default_classes", [], "any", false, false, true, 91)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 92
                    yield "            ";
                    // line 93
                    $context["column_classes"] = ["views-field"];
                    // line 97
                    yield "            ";
                    $context['_parent'] = $context;
                    $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["column"], "fields", [], "any", false, false, true, 97));
                    foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
                        // line 98
                        yield "              ";
                        $context["column_classes"] = Twig\Extension\CoreExtension::merge(($context["column_classes"] ?? null), [("views-field-" . $context["field"])]);
                        // line 99
                        yield "            ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_key'], $context['field'], $context['_parent']);
                    $context = array_intersect_key($context, $_parent);
                    $context += $_parent;
                    // line 100
                    yield "          ";
                }
                // line 101
                yield "          <td";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "attributes", [], "any", false, false, true, 101), "addClass", [($context["column_classes"] ?? null)], "method", false, false, true, 101), "html", null, true);
                yield ">";
                // line 102
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["column"], "wrapper_element", [], "any", false, false, true, 102)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 103
                    yield "<";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "wrapper_element", [], "any", false, false, true, 103), "html", null, true);
                    yield ">
              ";
                    // line 104
                    $context['_parent'] = $context;
                    $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["column"], "content", [], "any", false, false, true, 104));
                    foreach ($context['_seq'] as $context["_key"] => $context["content"]) {
                        // line 105
                        yield "                ";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["content"], "separator", [], "any", false, false, true, 105), "html", null, true);
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["content"], "field_output", [], "any", false, false, true, 105), "html", null, true);
                        yield "
              ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_key'], $context['content'], $context['_parent']);
                    $context = array_intersect_key($context, $_parent);
                    $context += $_parent;
                    // line 107
                    yield "              </";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["column"], "wrapper_element", [], "any", false, false, true, 107), "html", null, true);
                    yield ">";
                } else {
                    // line 109
                    $context['_parent'] = $context;
                    $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["column"], "content", [], "any", false, false, true, 109));
                    foreach ($context['_seq'] as $context["_key"] => $context["content"]) {
                        // line 110
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["content"], "separator", [], "any", false, false, true, 110), "html", null, true);
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["content"], "field_output", [], "any", false, false, true, 110), "html", null, true);
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_key'], $context['content'], $context['_parent']);
                    $context = array_intersect_key($context, $_parent);
                    $context += $_parent;
                }
                // line 113
                yield "          </td>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['key'], $context['column'], $context['_parent']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 115
            yield "      </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['row'], $context['_parent']);
        $context = array_intersect_key($context, $_parent);
        $context += $_parent;
        // line 117
        yield "  </tbody>
</table>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["header", "responsive", "sticky", "attributes", "caption_needed", "caption", "title", "summary_element", "fields", "rows"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "core/themes/stable9/templates/views/views-view-table.html.twig";
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
    public function getDefaultEscapeStrategy(): string|false
    {
        return "html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  271 => 117,  263 => 115,  255 => 113,  246 => 110,  242 => 109,  237 => 107,  226 => 105,  222 => 104,  217 => 103,  215 => 102,  211 => 101,  208 => 100,  201 => 99,  198 => 98,  193 => 97,  191 => 93,  189 => 92,  186 => 91,  182 => 90,  177 => 89,  173 => 88,  170 => 87,  165 => 84,  157 => 82,  152 => 79,  142 => 77,  140 => 76,  135 => 74,  131 => 72,  121 => 70,  119 => 69,  115 => 68,  113 => 67,  109 => 66,  106 => 65,  104 => 62,  103 => 60,  101 => 59,  98 => 58,  94 => 57,  90 => 55,  87 => 54,  83 => 52,  77 => 50,  74 => 49,  68 => 47,  62 => 45,  60 => 44,  57 => 43,  55 => 42,  50 => 41,  48 => 38,  47 => 37,  46 => 36,  45 => 35,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "core/themes/stable9/templates/views/views-view-table.html.twig", "/var/www/html/ESIM-content-drupal10-9dec/core/themes/stable9/templates/views/views-view-table.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 35, "if" => 42, "for" => 57];
        static $filters = ["length" => 36, "escape" => 41, "merge" => 98];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "if", 2 => "for"],
                [0 => "length", 1 => "escape", 2 => "merge"],
                [],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            } elseif ($e instanceof SecurityNotAllowedTestError && isset($tests[$e->getTestName()])) {
                $e->setTemplateLine($tests[$e->getTestName()]);
            }

            throw $e;
        }

    }
}
