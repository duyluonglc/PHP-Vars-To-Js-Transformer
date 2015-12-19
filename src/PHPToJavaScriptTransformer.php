<?php

namespace Laracasts\Utilities\JavaScript;

use stdClass;
use Exception;
use JsonSerializable;

class PHPToJavaScriptTransformer
{
    /**
     * The namespace to nest JS vars under.
     *
     * @var string
     */
    protected $namespace;

    /**
     * All transformable types.
     *
     * @var array
     */
    protected $types = [
        'String',
        'Array',
        'Object',
        'Numeric',
        'Boolean',
        'Null'
    ];
	
    /**
     * Use jquery to extend objects
     * @var bool
     */
	protected $useJquery;

    /**
     * Array to keep all variables
     * @var array
     */
	protected $variables = [];
	
    /**
     * Create a new JS transformer instance.
     * @param string $namespace
     * @param bool $useJquery
     */
    function __construct($namespace = 'window', $useJquery = false)
    {
        $this->namespace = $namespace;
		$this->useJquery = $useJquery;
    }

    /**
     * Bind the given array of variables to the view.
     *
     */
    public function put()
    {
        $arguments = func_get_args();

        if (is_array($arguments[0])) {
            $variables = $arguments[0];
        } elseif (count($arguments) == 2) {
            $variables = [$arguments[0] => $arguments[1]];
        } else {
            throw new Exception('Try JavaScript::put(["foo" => "bar"]');
        }
		
		$this->variables = array_merge($this->variables, $variables);
    }
	
    /**
     * Make string javascript code
     * @return string javascript
     */
	public function render() {
		// First, we have to translate the variables
        // to something JS-friendly.
        $js = $this->buildJavaScriptSyntax($this->variables);

        return "<script>{$js}</script>";
	}

    /**
     * Translate the array of PHP vars to
     * the expected JavaScript syntax.
     *
     * @param  array $vars
     * @return array
     */
    public function buildJavaScriptSyntax(array $vars)
    {
        $js = $this->buildNamespaceDeclaration();

        foreach ($vars as $key => $value) {
            $js .= $this->buildVariableInitialization($key, $value);
        }

        return $js;
    }

    /**
     * Create the namespace to which all vars are nested.
     *
     * @return string
     */
    protected function buildNamespaceDeclaration()
    {
        if ($this->namespace == 'window') {
            return '';
        }

        return "window.{$this->namespace} = window.{$this->namespace} || {};";
    }

    /**
     * Translate a single PHP var to JS.
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected function buildVariableInitialization($key, $value)
    {
		if($this->useJquery) {
			return "{$this->namespace}.{$key} = {$this->namespace}.{$key} || {}; \$.extend(true, {$this->namespace}.{$key}, {$this->optimizeValueForJavaScript($value)});";
		} else {
			return "{$this->namespace}.{$key} = {$this->optimizeValueForJavaScript($value)};";
		}
    }

    /**
     * Format a value for JavaScript.
     *
     * @param  string $value
     * @throws Exception
     * @return string
     */
    protected function optimizeValueForJavaScript($value)
    {
        // For every transformable type, let's see if
        // it needs to be transformed for JS-use.

        foreach ($this->types as $transformer) {
            $js = $this->{"transform{$transformer}"}($value);

            if ( ! is_null($js)) {
                return $js;
            }
        }
    }

    /**
     * Transform a string.
     *
     * @param  string $value
     * @return string
     */
    protected function transformString($value)
    {
        if (is_string($value)) {
            return "'{$this->escape($value)}'";
        }
    }

    /**
     * Transform an array.
     *
     * @param  array $value
     * @return string
     */
    protected function transformArray($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
    }

    /**
     * Transform a numeric value.
     *
     * @param  mixed $value
     * @return mixed
     */
    protected function transformNumeric($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
    }

    /**
     * Transform a boolean.
     *
     * @param  boolean $value
     * @return string
     */
    protected function transformBoolean($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
    }

    /**
     * @param  object $value
     * @return string
     * @throws Exception
     */
    protected function transformObject($value)
    {
        if ( ! is_object($value)) {
            return;
        }

        if ($value instanceof JsonSerializable || $value instanceof StdClass) {
            return json_encode($value);
        }

        // If a toJson() method exists, we'll assume that
        // the object can cast itself automatically.
        if (method_exists($value, 'toJson')) {
            return $value;
        }

        // Otherwise, if the object doesn't even have a
        // __toString() method, we can't proceed.
        if ( ! method_exists($value, '__toString')) {
            throw new Exception('Cannot transform this object to JavaScript.');
        }

        return "'{$value}'";
    }

    /**
     * Transform "null."
     *
     * @param  mixed $value
     * @return string
     */
    protected function transformNull($value)
    {
        if (is_null($value)) {
            return 'null';
        }
    }

    /**
     * Escape any single quotes.
     *
     * @param  string $value
     * @return string
     */
    protected function escape($value)
    {
        return str_replace(["\\", "'"], ["\\\\", "\'"], $value);
    }
}