<?php


namespace Wqy;
use Exception;


class GenHelperFromZep
{
    public function gen($sourceDir, $outputDir)
    {
        $realSource = realpath($sourceDir);

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sourceDir));

        foreach ($it as $one /** @var $one \SplFileInfo */) {
            if ($one->getExtension() == 'zep') {
                $realZep = $one->getRealPath();
                $code = $this->genZepToPhp($realZep);

                $this->save($outputDir, $realSource, $realZep, $code);
            }
        }
    }

    public function save($outputDir, $realSource, $realZep, $code)
    {
        $path = rtrim($outputDir, '/\\') . '/' . ltrim(str_replace($realSource, '', $realZep), '/\\');
        $path = str_replace('.zep', '.php', $path);

        if (! is_dir($dir = dirname($path))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($path, $code);
    }


    public function genZepToPhp($filename)
    {
        $content = file_get_contents($filename);

        $code = "<?php \n\n";

        $classStartPat = '/^((abstract |final )?class|interface) .*?{/sm';
        if (preg_match($classStartPat, $content, $mat, PREG_OFFSET_CAPTURE)) {
            $classStart = substr($content, 0, $mat[0][1]) . str_replace('$', '', $mat[0][0]);
            $code .= $classStart;
        }
        else {
            throw new Exception($filename . ' no class start');
        }

        $innerClassStr = substr($content, $st = strlen($classStart), strlen($content) - $st);

        $constPat = '#^\tconst.*;$#m';
        if (preg_match_all($constPat, $innerClassStr, $mat)) {
            $code .= "\n";
            foreach ($mat[0] as $one) {
                $code .= $one . "\n\n";
            }
        }

        $propPat = '#^\t(public|protected|private).*$#m';

        if (preg_match_all($propPat, $innerClassStr, $mat)) {
            $code .= "\n";
            $props = array_filter($mat[0], function ($v) {
                return strpos($v, 'function') === false;
            });

            foreach ($props as $one) {

                if (preg_match('#(\w+)\s*\{(.*)\}#', $one, $propMdMat)) {
                    $tmpName = ltrim($propMdMat[1], '_');
                    $setterCode = array_map(function ($one) use ($tmpName){
                        $one = trim($one);
                        $name = in_array($one, ['get', 'set']) ? $one . ucfirst($tmpName) : $one;
                        return "\n\tpublic function $name() {}\n";
                    }, explode(',', $propMdMat[2]));

                    $code .= implode("\n", $setterCode);
                }

                $one = preg_replace('#(public|protected|private)( static)?(\s+)#', '$1$2$3\$', $one);
                $one = preg_replace('#\s*\{.*\}#', '', $one);

                $code .= $one . "\n\n";
            }
        }

        $methodPat = '#(^\t/\*[^/]*/.*\n)((^\t.*function.*)(\(.*\))(.*))$#m';

        if (preg_match_all($methodPat, $innerClassStr, $mat, PREG_SET_ORDER)) {

            foreach ($mat as $one) {
                $code .= "\n";

                $cmt = $one[1];
                if (strpos($one[5], '->')) {
                    $rt = trim(str_replace(['->', '<', '>'], '', $one[5]));
                    $cmt = str_replace('*/', "* @return $rt\n\t */", $cmt);
                }
                $code .= $cmt;

                $code .= $one[3];

                $fnArgs = $one[4];

                $fnArgs= str_replace(['<', '>', '!'], '', $fnArgs);

                $fnArgs = implode(', ', array_map(function ($a) {
                    $pat = '#^(\s*)(\S*)(\s*)(\w+)(\s*)(=)?(\s*)(.*)$#';
                    preg_match($pat, $a, $m);
                    return preg_replace_callback($pat, function ($m) {
                        $type = trim($m[2]);
                        $rt = $type == 'var' ? '' : "$type ";
                        $rt .= '$' . $m[4];

                        $def = trim($m[8]);
                        if (! empty($def)) {
                            $rt .= ' = ' . $def;
                        }
                        return $rt;
                    }, $a);
                }, explode(',', trim($fnArgs, '()'))));

                    $code .= '(' . $fnArgs . ')';

                    if (strpos($one[2], ';') === false) {
                        $code .= "\n\t{\n\t}\n";
                    }
                    else {
                        $code .= ";\n";
                    }
            }
        }

        $code .= "\n\n}\n";

        return $code;
    }
}