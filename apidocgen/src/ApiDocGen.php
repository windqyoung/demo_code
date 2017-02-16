<?php

class ApiDocGen
{

    private $returnDefault = [
        [
            'name' => 'errorCode',
            'type' => 'int',
            'required' => true,
            'desc' => '错误码',
        ],
        [
            'name' => 'errorMsg',
            'type' => 'string',
            'required' => true,
            'desc' => '错误消息',
        ],
        [
            'name' => 'data',
            'type' => '@data',
            'required' => true,
            'desc' => '数据',
        ],
    ];


    public $swagger= array(
        'swagger' => '2.0',
        'info' => array(
            'version' => '1.0.0',
            'title' => 'Swagger Data',
            'description' => 'generated by PHP',
        ),
        'host' => 'localhost', // !!!
        'basePath' => '/', // !!!
        'schemes' => array( 'http', 'https' ),
        'consumes' => array( 'application/x-www-form-urlencoded' ),
        'produces' => array( 'application/json' ),
        'paths' => array(),
        'definitions' => array(),
    );

    /**
     * @return array the $returnDefault
     */
    public function getReturnDefault()
    {
        return $this->returnDefault;
    }

    /**
     * @param array
     */
    public function setReturnDefault($returnDefault)
    {
        $this->returnDefault = $returnDefault;
    }

    public function genClass($cls, $glue = "\n\n\n")
    {
        $parsed = $this->parseClass($cls);
        return $this->genDocsHtml($parsed, $glue);
    }

    public function parseClass($cls)
    {
        $ref = new \ReflectionClass($cls);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        $parsed = [];
        foreach ($methods as $m /** @var $m \ReflectionMethod */) {
            $cmt = $m->getDocComment();
            $parsed[] = $this->parseCmt($cmt);
        }
        $parsed = array_filter($parsed);

        return $parsed;
    }

    public function genFile($file, $glue = "\n\n\n")
    {
        $parsed = $this->parseFile($file);
        return $this->genDocsHtml($parsed, $glue);
    }

    public function parseFile($file)
    {
        if (! is_file($file)) {
            throw new Exception('file: ' . $file . ' does not exists');
        }

        $ctt = file_get_contents($file);
        $tks = token_get_all($ctt);
        $parsed = [];
        foreach ($tks as $one) {
            if ($one[0] == T_DOC_COMMENT) {
                $parsed[] = $this->parseCmt($one[1]);
            }
        }
        $parsed = array_filter($parsed);

        return $parsed;
    }

    public function genDir($dir, $glue = "\n\n\n")
    {
        $parsed = $this->parseDir($dir);
        return $this->genDocsHtml($parsed, $glue);
    }

    public function parseDir($dir)
    {
        $it = new CallbackFilterIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir)),
            [$this, 'acceptPhp']);

        $parsed = [];
        foreach ($it as $k => $v) {
            foreach ($this->parseFile($k) as $one) {
                $parsed[] = $one;
            }
        }
        $parsed = array_filter($parsed);

        return $parsed;
    }

    /**
     * @param SplFileInfo $v
     */
    public function acceptPhp($v)
    {
        return substr($v->getFilename(), -4) == '.php';
    }

    public function genDocsHtml($parsed, $glue)
    {
        foreach ($parsed as $one) {
            $docs[] = $this->genHtml($one);
        }
        return implode($glue, $docs);
    }

    public function genHtml($doc)
    {

        ob_start();

        ?>
<h1><?=isset($doc['summary']) ? $doc['summary'] : ''?> [<?=$doc['method']?> <?=$doc['uri']?>]</h1>

<?php if (! empty($doc['desc'])) { ?>
<p>说明: </p>
<?php   foreach ($doc['desc'] as $one) { ?>
<p><?=$one?></p>
<?php   } ?>
<?php } ?>

<?php if (! empty($doc['param'])) { ?>
<p>参数: </p>
<table border="1">
<thead>
  <tr>
    <th>字段</th>
    <th>类型</th>
    <th>必需</th>
    <th>说明</th>
  </tr>
</thead>
<tbody>
<?php   foreach ($doc['param'] as $one) { ?>
  <tr>
    <td><?=$one['name']?></td>
    <td><?=$one['type']?></td>
    <td><?=$one['required'] ? '是' : '否'?></td>
    <td><?=$one['desc']?></td>
  </tr>
<?php   } ?>

</tbody>
</table>

<?php } ?>


<?php if (! empty($doc['errorCode'])) { ?>
<p>错误码: </p>
<table border="1">
<thead>
  <tr>
    <th>错误码</th>
    <th>说明</th>
  </tr>
</thead>
<tbody>
<?php   foreach ($doc['errorCode'] as $code => $desc) { ?>
  <tr>
    <td><?=$code?></td>
    <td><?=$desc?></td>
  </tr>
<?php   } ?>

</tbody>
</table>
<?php } ?>

<?php foreach ($doc['return'] as $return) { ?>

<p>返回: <?=isset($return['type']) ? $return['type'] : ''?> </p>
<?php   if (false && ! empty($return['http'])) { ?>
<p>HTTP状态码: <?=$return['http']?></p>
<?php   } ?>
<?php   if (isset($return['desc'])) {
            foreach ($return['desc'] as $one) {
?>
<p><?=$one?></p>
<?php       }} ?>


<?php   if (isset($return['fields'])) { ?>
<table border="1">
<thead>
  <tr>
    <th>字段</th>
    <th>类型</th>
    <th>必需</th>
    <th>说明</th>
  </tr>
</thead>
<tbody>
<?php       foreach ($return['fields'] as $one) { ?>
  <tr>
    <td><?=$one['name']?></td>
    <td><?=$one['type']?></td>
    <td><?=$one['required'] ? '是' : '否'?></td>
    <td><?=$one['desc']?></td>
  </tr>
  <?php     } ?>

</tbody>
</table>
<?php   } ?>

<?php } ?>

<?php foreach ($doc['custom'] as $typeValue) { ?>
<p><?=$typeValue['name']?> 说明: </p>
<?php   if (isset($typeValue['desc'])) {
            foreach ($typeValue['desc'] as $desc) {
    ?>
<p><?=$desc?></p>
<?php       }} ?>

<?php   if (isset($typeValue['fields'])) { ?>
<table border="1">
<thead>
  <tr>
    <th>字段</th>
    <th>类型</th>
    <th>必需</th>
    <th>说明</th>
  </tr>
</thead>
<tbody>
<?php       foreach ($typeValue['fields'] as $one) { ?>
  <tr>
    <td><?=$one['name']?></td>
    <td><?=$one['type']?></td>
    <td><?=$one['required'] ? '是' : '否'?></td>
    <td><?=$one['desc']?></td>
  </tr>
<?php       } ?>

</tbody>
</table>
<?php   } ?>

<?php } ?>
<?php foreach ($doc['example'] as $key => $val) { ?>
<p>示例</p>
<pre>
<?=$this->exampleText($val)?>
</pre>
<?php }

        return ob_get_clean();
    }

    private function exampleText($exampleArray)
    {
        $exp = implode("\n", $exampleArray);
        $json = json_decode($exp);
        if ($json) {
            $text = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        else {
            $text = $exp;
        }

        return $text;
    }


    public function parseCmt($cmt)
    {
        if (strpos($cmt, '@gendoc') === false) {
            return;
        }

        $paramPattern = '(?<type>\S+)\s+\$?(?<name>\S+)\s+(@in(?<in>query|header|path|formData|body))?\s*(?<required>required)?\s*(?<desc>.*)';

        $doc = [
            'method' => 'GET',
            'uri' => '',
            'prefix' => '',
            'summary' => '',
            'desc' => [],
            'tags' => [],
            'param' => [],
            'errorCode' => [],
            'example' => [],
            'return' => [['desc' => null, 'http' => 'default',
                'fill' => true, 'fields' => $this->getReturnDefault()]],
            'custom' => [],
        ];
        $last = null;
        $lastData = null;
        $lines = explode("\n", $cmt);
        foreach ($lines as $one) {
            $subject = trim($one, '/* ');

            if (empty($subject)) {
                continue;
            }
            else if (preg_match('/@gendoc/', $subject)) {
                continue;
            }
            else if (strpos($subject, '@end') !== false) {
                break;
            }
            else if (preg_match('/@method\s+(.+)/', $subject, $mat)) {
                $doc['method'] = $mat[1];
            }
            else if (preg_match('/@uri\s+(.+)/', $subject, $mat)) {
                $doc['uri'] = $mat[1];
            }
            else if (preg_match('/@desc\s*(.*)/', $subject, $mat)) {
                $doc['summary'] = $mat[1];
                $last = 'desc';
            }
            else if (preg_match('/@tags?\s*(.*)/', $subject, $mat)) {
                $doc['tags'] = array_merge($doc['tags'], preg_split('/[,\s]+/', $mat[1]));
            }
            else if (preg_match("/@param\s+$paramPattern/", $subject, $mat)) {
                $doc['param'][] = $this->paramArray($mat);
            }
            else if (preg_match('/@errorCode\s+(\S+)\s+(.+)/', $subject, $mat)) {
                $doc['errorCode'][$mat[1]] = $mat[2];
                if (! empty($doc['return'])) {
                    $doc['return'][count($doc['return']) - 1]['errorCode'][$mat[1]] = $mat[2];
                }
            }
            else if (preg_match('/@prefix\s+(\w+)/', $subject, $mat)) {
                $doc['prefix'] = $mat[1];
            }
            else if (preg_match('/@example\s*(.*)/', $subject, $mat)) {
                $last = 'example';
                $lastData = $mat[1] ?: count($doc['example']);
            }
            else if (preg_match('/@return\s*(\S*)\s*(.*)/', $subject, $mat)) {
                $last = 'return';
                $lastData = $this->lastReturnKey($doc['return']);
                $doc['return'][$lastData]['desc'][] = $mat[2];
                $doc['return'][$lastData]['type'] = $mat[1];
            }
            else if ($last == 'return' && preg_match('/@http\s+(\S+)/', $subject, $mat)) {
                $doc['return'][$lastData]['http'] = $mat[1];
            }
            else if (preg_match('/@(\S+)\s*$/', $subject, $mat)) {
                $last = 'custom';
                $lastData = count($doc['custom']);
                $doc['custom'][$lastData]['name'] = $mat[1];
            }
            else {
                if ($last == 'return') {
                    if (preg_match("/$paramPattern/", $subject, $mat)) {
                        $doc['return'][$lastData]['fields'][] = $this->paramArray($mat);
                    }
                    else {
                        $doc['return'][$lastData]['desc'][] = $subject;
                    }
                }
                else if ($last == 'example') {
                    $doc['example'][$lastData][] = $subject;
                    if (! empty($doc['return'])) {
                        $doc['return'][count($doc['return']) - 1]['example'][$lastData][] = $subject;
                    }
                }
                else if ($last == 'custom') {
                    if (preg_match("/$paramPattern/", $subject, $mat)) {
                        $doc['custom'][$lastData]['fields'][] = $this->paramArray($mat);
                    }
                    else if (empty($doc['custom'][$lastData]['fields'])) {
                        $doc['custom'][$lastData]['desc'][] = $subject;
                    }
                }
                else if (isset($doc[$last])) {
                    $doc[$last][] = $subject;
                }
            }
        }

        if (empty($doc['prefix'])) {
            $doc['prefix'] = preg_replace_callback('#\W+(\w)#', function ($mat) {
                return strtoupper($mat[1]);
            }, $doc['uri']);
        }

        return $doc;
    }

    private function lastReturnKey(& $return)
    {
        $count = count($return);

        if ($count === 0) {
            return $count;
        }

        $last = $return[$count - 1];

        if (! empty($last['fill'])) {
            unset($return[$count - 1]['fill']);
            return $count - 1;
        }
        return $count;
    }


    private function paramArray($mat)
    {
        return [
            'type' => $mat['type'],
            'name' => $mat['name'],
            'required' => $mat['required'] == 'required',
            'desc' => $mat['desc'],
            'in' => $mat['in'],
        ];
    }


    public function genDocsSwagger($parsed, $host = null, $basePath = null)
    {
        $sw = $this->swagger;
        if ($host !== null) {
            $sw['host'] = $host;
        }
        if ($basePath !== null) {
            $sw['basePath'] = $basePath;
        }

        foreach ($parsed as $one) {
            $sw = $this->genSwagger($one, $sw);
        }

        return $sw;
    }

    private function paramInValue($p, $method)
    {
        if (! empty($p['in'])) {
            return $p['in'];
        }

        $ins = ['get' => 'query', 'post' => 'formData'];

        if (isset($ins[$method])) {
            return $ins[$method];
        }

        throw new Exception('in required');
    }

    private function fillTypeValue($p, $prefix, & $param = [])
    {
        if (empty($p['type'])) {
            throw new Exception('type required');
        }
        $type = $p['type'];

        if (isset($p['desc'])) {
            $param['description'] = $p['desc'];
        }

        if (preg_match('#(?<at>@?)(?<type>\w+)(?<bra>(\[\])?)#', $type, $mat)) {
            $name = $mat['type'];
            $isArray = !! $mat['bra'];
            $isRef = !! $mat['at'];

            if ($isArray) {
                $param['type'] = 'array';
                $param['collectionFormat'] = 'multi';

                $typeCt = & $param['items'];
            }
            else {
                $typeCt = & $param;
            }

            if ($isRef) {
                $typeCt['$ref'] = $this->definitionPath($this->definitionName($prefix, $name));
            }
            else {
                $nt = $this->normalType($name);
                $typeCt['type'] = $nt['type'];
                $typeCt['format'] = $nt['format'];
            }
        }

        return $param;
    }

    private function normalType($type)
    {
        $nmTypes = [
            'int' => ['type' => 'integer', 'format' => 'int32', ],
            'integer' => ['type' => 'integer', 'format' => 'int32', ],
            'long' => ['type' => 'integer', 'format' => 'int64', ],
            'float' => ['type' => 'number', 'format' => 'float', ],
            'double' => ['type' => 'number', 'format' => 'double', ],
            'string' => ['type' => 'string', 'format' => 'byte', ],
            'binary' => ['type' => 'string', 'format' => 'binary', ],
            'bool' => ['type' => 'boolean', 'format' => 'date', ],
            'boolean' => ['type' => 'boolean', 'format' => 'date', ],
            'dateTime' => ['type' => 'string', 'format' => 'date', ],
            'datetime' => ['type' => 'string', 'format' => 'date', ],
            'date' => ['type' => 'string', 'format' => 'date', ],
            'password' => ['type' => 'string', 'format' => 'password', ],
        ];

        if (! isset($nmTypes[$type])) {
            $type = 'string';
        }

        return $nmTypes[$type];
    }

    private function returnTypeDefinitionName($rt, $doc)
    {
        $type = preg_replace_callback('#\W(\w)#', function ($mat) {
            return strtoupper($mat[1]);
        }, $rt['type']);

        return $doc['prefix']
            . '_' . strtolower($doc['method'])
            . '_return_'
            . $rt['http'] . '_'
            . $type;
    }

    private function definitionName($prefix, $name)
    {
        return $prefix . '_' . $name;
    }

    private function definitionPath($name)
    {
        return '#/definitions/' . $name;
    }

    private function buildDefinitionObject($defi, $ctx)
    {
        $prefix = $ctx['prefix'];
        $fields = isset($defi['fields']) ? $defi['fields'] : [];
        $ret = [ 'type' => 'object', ];

        if (isset($defi['desc'])) {
            $ret['title'] = $defi['desc'][0];
            $ret['description'] = implode("\n", $defi['desc']);
        }

        $required = [];
        $properties = [];
        foreach ($fields as $fd) {
            if (! empty($fd['required'])) {
                $required[] = $fd['name'];
            }
            $properties[$fd['name']] = $this->fillTypeValue($fd, $prefix);
        }

        $ret['required'] = $required;
        $ret['properties'] = $properties;

        if (isset($defi['example'])) {
            $exp = [];
            foreach ($defi['example'] as $k => $ex) {
                $exp[$k] = $this->normalExample($ex);
            }
            $ret['example'] = $exp;
        }

        if (isset($defi['errorCode'])) {
            $ret['example']['errorCode'] = $defi['errorCode'];
        }

        return $ret;
    }

    private function normalExample($ex)
    {
        $exT = is_array($ex) ? implode("\n", $ex) : $ex;
        $json = json_decode($exT, true);
        return $json ?: $exT;
    }

    public function genSwagger($doc, $sw = [])
    {
        if (empty($doc['uri'])) {
            throw new Exception('uri required');
        }
        $uri = $doc['uri'];

        if (empty($doc['method'])) {
            $doc['method'] = 'GET';
        }
        $method = strtolower(trim($doc['method']));

        $prefix = $doc['prefix'];

        if (isset($sw['paths'][$uri][$method])) {
            throw new Exception("paths:$uri:$method exists");
        }

        $op = [];

        if (isset($doc['summary'])) {
            $op['summary'] = $doc['summary'];
        }

        if (isset($doc['desc'])) {
            $op['description'] = '<div>' . implode("</div>\n<div>", $doc['desc']) . '</div>';
        }

        if (isset($doc['tags'])) {
            $op['tags'] = array_filter($doc['tags']);
        }

        $op['operationId'] = $prefix . '_' . $method;

        // 参数
        if (isset($doc['param'])) {
            foreach ($doc['param'] as $p) {
                $param = [
                    'name' => $p['name'],
                    'description' => $p['desc'],
                    'required' => $p['required'],
                ];
                $param['in'] = $this->paramInValue($p, $method);
                $this->fillTypeValue($p, $prefix, $param);

                $op['parameters'][] = $param;
            }
        }

        // responses, required
        if (empty($doc['return'])) {
            throw new Exception('return required');
        }
        $respTypes = []; // 保存返回的类型, 一会儿要塞到definitions里面
        foreach ($doc['return'] as $one) {
            if (empty($one['desc'])) {
                throw new Exception('return desc required');
            }
            $resp = ['description' => implode("\n", $one['desc'])];
            $http = empty($one['http']) ? 'default' : $one['http'];
            $defiName = $this->returnTypeDefinitionName($one, $doc);
            $resp['schema']['$ref'] = $this->definitionPath($defiName);

            $respTypes[] = ['name' => $defiName, 'defi' => $one];
            $op['responses'][$http] = $resp;
        }
        $sw['paths'][$uri][$method] = $op;

        $op['x-errorCode'] = $doc['errorCode'];
        $op['x-example'] = $doc['example'];

        foreach ($respTypes as $t) {
            $sw['definitions'][$t['name']] = $this->buildDefinitionObject($t['defi'], $doc);
        }
        // custom
        if (isset($doc['custom'])) {
            foreach ($doc['custom'] as $one) {
                $name = $this->definitionName($prefix, $one['name']);
                $sw['definitions'][$name] = $this->buildDefinitionObject($one, $doc);
            }
        }

        // 把null值改为""
        array_walk_recursive($sw, function (& $v) {
            if ($v === null) {
                $v = '';
            }
        });

        return $sw;
    }

    public function genClassSwagger($cls, $host = null, $basePath = null)
    {
        $parsed = $this->parseClass($cls);
        return $this->genDocsSwagger($parsed, $host, $basePath);
    }

    public function genDirSwagger($dir, $host = null, $basePath = null)
    {
        $parsed = $this->parseDir($dir);
        return $this->genDocsSwagger($parsed, $host, $basePath);
    }

    public function genFileSwagger($file, $host = null, $basePath = null)
    {
        $parsed = $this->parseFile($file);
        return $this->genDocsSwagger($parsed, $host, $basePath);
    }




}

