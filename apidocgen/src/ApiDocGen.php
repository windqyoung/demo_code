<?php

class ApiDocGen
{

    private $returnDefault = [
        'errorCode' => [
            'name' => 'errorCode',
            'type' => 'int',
            'required' => true,
            'desc' => '错误码',
        ],
        'errorMsg' => [
            'name' => 'errorMsg',
            'type' => 'string',
            'required' => true,
            'desc' => '错误消息',
        ],
        'data' => [
            'name' => 'data',
            'type' => '@data',
            'required' => true,
            'desc' => '数据',
        ],
    ];

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
<h1><?=isset($doc['desc'][0]) ? $doc['desc'][0] : ''?> [<?=$doc['method']?> <?=$doc['uri']?>]</h1>

<?php if (($len = count($doc['desc'])) > 1) { ?>
<p>说明: </p>
<?php for ($i = 1; $i < $len; $i ++) { ?>
<p><?=$doc['desc'][$i]?></p>
<?php } ?>
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
  <?php foreach ($doc['param'] as $one) { ?>
  <tr>
    <td><?=$one['name']?></td>
    <td><?=$one['type']?></td>
    <td><?=$one['required'] ? '是' : '否'?></td>
    <td><?=$one['desc']?></td>
  </tr>
  <?php } ?>

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
  <?php foreach ($doc['errorCode'] as $code => $desc) { ?>
  <tr>
    <td><?=$code?></td>
    <td><?=$desc?></td>
  </tr>
  <?php } ?>

</tbody>
</table>
<?php } ?>

<?php foreach ($doc['return'] as $return) { ?>

<p>返回: <?=isset($return['desc'][0]) ? $return['desc'][0] : ''?> </p>
<?php if (! empty($return['http'])) { ?>
<p>HTTP状态码: <?=$return['http']?></p>
<?php } ?>
<?php if (($len = count($return['desc'])) > 1) {
    for ($i = 1; $i < $len; $i ++) {
?>
<p><?=$return['desc'][$i]?></p>
<?php }} ?>


<?php if (isset($return['fields'])) { ?>
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
  <?php foreach ($return['fields'] as $one) { ?>
  <tr>
    <td><?=$one['name']?></td>
    <td><?=$one['type']?></td>
    <td><?=$one['required'] ? '是' : '否'?></td>
    <td><?=$one['desc']?></td>
  </tr>
  <?php } ?>

</tbody>
</table>
<?php } ?>

<?php } ?>

<?php foreach ($doc['custom'] as $typeValue) { ?>
<p><?=$typeValue['name']?> 说明: </p>
<?php if (isset($typeValue['desc'])) {
    foreach ($typeValue['desc'] as $desc) {
    ?>
<p><?=$desc?></p>
<?php }} ?>

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
    <?php foreach ($typeValue['fields'] as $one) { ?>
      <tr>
        <td><?=$one['name']?></td>
        <td><?=$one['type']?></td>
        <td><?=$one['required'] ? '是' : '否'?></td>
        <td><?=$one['desc']?></td>
      </tr>
    <?php } ?>

</tbody>
</table>
<?php } ?>
<?php foreach ($doc['example'] as $key => $val) { ?>
<p>示例</p>
<pre>
<?=$this->exampleText($val)?>
</pre>
<?php } ?>
        <?php
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

    private function typeAnchor($type, $prefix)
    {
        if ($type[0] == '@') {
            $t = substr($type, 1);
            return "<a href='#${prefix}${t}'>$t</a>";
        }
        else {
            return $type;
        }
    }

    private function typeId($type, $prefix)
    {
        return $prefix . $type;
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
            'desc' => [],
            'tags' => [],
            'param' => [],
            'errorCode' => [],
            'example' => [],
            'return' => [['desc' => null, 'http' => 'default', 'fill' => true, 'fields' => $this->getReturnDefault()]],
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
                $doc['desc'][] = $mat[1];
                $last = 'desc';
            }
            else if (preg_match('/@tags?\s+(.*)/', $subject, $mat)) {
                $doc['tags'] = array_merge($doc['tags'], preg_split('/[,\s]+/', $mat[1]));
            }
            else if (preg_match("/@param\s+$paramPattern/", $subject, $mat)) {
                $doc['param'][$mat[2]] = $this->paramArray($mat);
            }
            else if (preg_match('/@errorCode\s+(\S+)\s+(.+)/', $subject, $mat)) {
                $doc['errorCode'][$mat[1]] = $mat[2];
            }
            else if (preg_match('/@example\s*(.*)/', $subject, $mat)) {
                $last = 'example';
                $lastData = $mat[1] ?: count($doc['example']);
            }
            else if (preg_match('/@return\s*(.*)/', $subject, $mat)) {
                $last = 'return';
                $lastData = $this->lastReturnKey($doc['return']);
                $doc['return'][$lastData]['desc'][] = $mat[1];
            }
            else if ($last == 'return' && preg_match('/@http\s+(\S+)/', $subject, $mat)) {
                $doc['return'][$lastData]['http'] = $mat[1];
            }
            else if (preg_match('/@(\S+)\s*$/', $subject, $mat)) {
                $last = 'custom';
                $lastData = $mat[1];
                $doc['custom'][$lastData]['name'] = $lastData;
            }
            else {
                if ($last == 'return') {
                    if (preg_match("/$paramPattern/", $subject, $mat)) {
                        $doc['return'][$lastData]['fields'][$mat[2]] = $this->paramArray($mat);
                    }
                    else {
                        $doc['return'][$lastData]['desc'][] = $subject;
                    }
                }
                else if ($last == 'example') {
                    $doc['example'][$lastData][] = $subject;
                }
                else if ($last == 'custom') {
                    if (preg_match("/$paramPattern/", $subject, $mat)) {
                        $doc['custom'][$lastData]['fields'][$mat[2]] = $this->paramArray($mat);
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

}

