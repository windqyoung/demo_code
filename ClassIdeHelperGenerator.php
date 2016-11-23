<?php


class ClassIdeHelperGenerator
{
    private $classes;


    public function __construct($classes)
    {
        $this->classes = $classes;
    }

    private function prefixSpaces($level)
    {
        return str_repeat('    ', $level);
    }

    /**
     * @param ReflectionClass $ref
     */
    private function genNamespaceStart($ref)
    {
        $s = 'namespace ';
        $ns = $ref->getNamespaceName();
        if ($ns) {
            $s .= $ns;
        }
        $s .= "\n{\n\n";

        return $s;
    }

    private function genNamespaceEnd()
    {
        return "}\n";
    }

    /**
     * @param ReflectionClass $ref
     */
    private function genClassComment($ref, $level = 1)
    {
        $cmt = $ref->getDocComment();
        return $cmt ? $this->prefixSpaces($level) . $ref->getDocComment() . "\n" : '';
    }

    /**
     * @param ReflectionClass $ref
     */
    private function genClassStart($ref, $level = 1)
    {
        $s = $this->prefixSpaces($level);

        if ($ref->isInterface()) {
            $s .= 'interface ';
        } else {
            if ($ref->isAbstract()) {
                $s .= 'abstract ';
            }
            $s .= 'class ';
        }

        $s .= $ref->getShortName() . ' ';

        // extends
        $parent = $ref->getParentClass();
        if ($parent) {
            $s .= 'extends \\' . $parent->getName() . ' ';
        }
        // implements
        $imps = $ref->getInterfaceNames();
        if ($imps) {
            $s .= $ref->isInterface() ? 'extends ' : 'implements ';
            $s .= implode(', ', array_map(function ($one) {
                return '\\' . $one;
            }, $imps));
        }

        $s .= "\n";
        $s .= $this->prefixSpaces($level) . "{\n\n";

        return $s;
    }

    private function genClassEnd($level = 1)
    {
        return $this->prefixSpaces($level) . "}\n";
    }

    /**
     * @param ReflectionClass $ref
     * @return string
     */
    private function genConstants($ref, $level = 2)
    {
        $constArray = $ref->getConstants();
        $s = '';
        foreach ($constArray as $name => $value)
        {
            $s .= $this->prefixSpaces($level) . 'const ' . $name . ' = ' . var_export($value, true) . ";\n\n";
        }
        return $s;
    }

    /**
     * @param ReflectionClass $ref
     */
    private function genProperties($ref, $level = 2)
    {
        $propDefaultValues = $ref->getDefaultProperties();

        $props = $ref->getProperties();

        $staticSource = '';
        $objectSource = '';

        /** @var $p ReflectionProperty */
        foreach ($props as $p)
        {
            if ($ref->getName() != $p->getDeclaringClass()->getName()) {
                continue;
            }
            if ($p->isStatic()) {
                $staticSource .= $this->genProperty($p, $propDefaultValues[$p->getName()], $level);
            } else {
                $objectSource .= $this->genProperty($p, $propDefaultValues[$p->getName()], $level);
            }
        }

        return $staticSource . $objectSource;
    }

    /**
     * @param ReflectionProperty $prop
     * @param mixed $defaultValue
     */
    private function genProperty($prop, $defaultValue, $level = 2)
    {
        $s = $this->prefixSpaces($level);
        $cmt = $prop->getDocComment();
        if ($cmt) {
            $s .= $cmt . "\n" . $this->prefixSpaces($level);
        }

        $s .= ($prop->isPublic() ? 'public ' : '')
            . ($prop->isPrivate() ? 'private ' : '')
            . ($prop->isProtected() ? 'protected ' : '')
            . ($prop->isStatic() ? 'static ' : '')
            . '$' . $prop->getName()
        ;
        if ($defaultValue !== null) {
            $s .= ' = ' . var_export($defaultValue, true);
        }

        $s .= ";\n\n";

        return $s;
    }

    /**
     * @param ReflectionClass $ref
     */
    private function genMethods($ref, $level = 2)
    {
        $staticSource = '';
        $objectSource = '';

        $methods = $ref->getMethods();

        /** @var $m ReflectionMethod */
        foreach ($methods as $m)
        {
            if ($ref->getName() != $m->getDeclaringClass()->getName()) {
                continue;
            }

            if ($m->isStatic()) {
                $staticSource .= $this->genMethod($m, $level);
            } else {
                $objectSource .= $this->genMethod($m, $level);
            }
        }

        return $staticSource . $objectSource;
    }

    /**
     * @param ReflectionMethod $m
     * @param number $level
     */
    private function genMethod($m, $level = 2)
    {
        $s = $this->prefixSpaces($level);
        $cmt = $m->getDocComment();
        if ($cmt) {
            $s .= $cmt . "\n" . $this->prefixSpaces($level);
        }
        if (! $m->getDeclaringClass()->isInterface()) {
            $s .= ($m->isAbstract() ? 'abstract ' : '')
                . ($m->isFinal() ? 'final ' : '');
        }

        $s .= ($m->isPublic() ? 'public ' : '')
            . ($m->isPrivate() ? 'private ' : '')
            . ($m->isProtected() ? 'protected ' : '')
            . ($m->isStatic() ? 'static ' : '')
            . 'function ' . $m->getName() . '('
            . $this->genParameters($m->getParameters())
            . ')'
        ;


        if ($m->isAbstract()) {
            $s .= ";\n\n";
        } else {
            $s .= "\n" . $this->prefixSpaces($level) . "{\n"
                . $this->prefixSpaces($level) . "}\n\n";
        }

        return $s;
    }

    /**
     * @param ReflectionParameter[] $params
     * @return string
     */
    private function genParameters($params)
    {
        $args = [];
        foreach ($params as $p)
        {
            $s = '';

            $cls = $p->getClass();
            if ($cls) {
                $s .= '\\' . $cls->getName() . ' ';
            }

            if ($p->isArray()) {
                $s .= 'array ';
            }

            if ($p->isCallable()) {
                $s .= 'callable ';
            }

            if ($p->isPassedByReference()) {
                $s .= '&';
            }
            $s .= '$' . $p->getName();

            if ($p->isDefaultValueAvailable()) {
                if ($p->isDefaultValueConstant()) {
                    $s .= ' = ' . $p->getDefaultValueConstantName();
                } else {
                    $s .= ' = ' . var_export($p->getDefaultValue(), true);
                }
            }

            $args[] = $s;
        }

        return implode(', ', $args);
    }


    public function genOne($cls)
    {
        $ref = new ReflectionClass($cls);

        $s = $this->genNamespaceStart($ref);

        $s .= $this->genClassComment($ref);

        $s .= $this->genClassStart($ref);

        $s .= $this->genConstants($ref);

        $s .= $this->genProperties($ref);

        $s .= $this->genMethods($ref);

        $s .= $this->genClassEnd();

        $s .= $this->genNamespaceEnd();

        return $s;
    }

    public function genSource()
    {
        $source = "<?php namespace { exit('this is a ide helper file, do not include.'); }\n\n";

        foreach ($this->classes as $cls)
        {
            $source .= $this->genOne($cls) . "\n\n";
        }

        return $source;
    }

    public function save($filename)
    {
        file_put_contents($filename, $this->genSource());
    }


    public static function savePhalconTo($filename = '_phalcon_ide_helper.php')
    {
        $classes = array_filter(array_merge(get_declared_classes(), get_declared_interfaces()), function ($c) {
            return strpos($c, 'Phalcon\\') === 0;
        });

        sort($classes);

        $obj = new self($classes);

        $obj->save($filename);
    }

}
