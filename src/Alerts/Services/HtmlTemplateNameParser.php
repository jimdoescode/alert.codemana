<?php namespace Alerts\Services;

use Symfony\Component\Templating;

class HtmlTemplateNameParser implements Templating\TemplateNameParserInterface
{
    private $root;

    /**
     * @param string $root The directory in which template files can be found
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * Parses a view name. Names can use colons ':' to denote sub-directories.
     * Names should NOT include '.php' extensions.
     *
     * @param string $name The name or path to a view file.
     * @return Templating\TemplateReference
     */
    public function parse($name)
    {
        $name = trim($name, '/:');
        if(strpos($name, ':') !== false) {
            $name = str_replace(':', '/', $name);
        }

        return new Templating\TemplateReference("{$this->root}/{$name}.php", 'php');
    }
}