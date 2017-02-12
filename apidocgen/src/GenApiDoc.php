<?php

class GenApiDoc
{

    private $returnDefault = [
        'errorCode' => [
            'name' => 'errorCode',
            'type' => 'int',
            'required' => true,
            'desc' => 'code',
        ],
        'errorMsg' => [
            'name' => 'errorMsg',
            'type' => 'string',
            'required' => true,
            'desc' => '消息',
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
        $prefix = $doc['idPrefix'];

        ob_start();

        ?>
<h1><?=isset($doc['desc'][0]) ? $doc['desc'][0] : ''?> [<?=$doc['method']?> <?=$doc['uri']?>]</h1>

<?php if (count($doc['desc']) > 1) { ?>
<p>说明: </p>
<?php for ($i = 1, $len = count($doc['desc']); $i < $len; $i ++) { ?>
<p><?=$doc['desc'][$i]?></p>
<?php } ?>
<?php } ?>

<?php if (! empty($doc['param'])) { ?>
<p>参数: </p>
<table border="1">
  <tr>
    <th>字段</th>
    <th>类型</th>
    <th>必需</th>
    <th>说明</th>
  </tr>
  <?php foreach ($doc['param'] as $one) { ?>
  <tr>
    <td><?=$one['name']?></td>
    <td><?=$this->typeAnchor($one['type'], $prefix) ?></td>
    <td><?=$one['required'] ? '是' : '否'?></td>
    <td><?=$one['desc']?></td>
  </tr>
  <?php } ?>

</table>

<?php } ?>


<?php if (! empty($doc['errorCode'])) { ?>
<p>错误代码: </p>
<table border="1">
  <tr>
    <th>code</th>
    <th>说明</th>
  </tr>
  <?php foreach ($doc['errorCode'] as $code => $desc) { ?>
  <tr>
    <td><?=$code?></td>
    <td><?=$desc?></td>
  </tr>
  <?php } ?>

</table>
<?php } ?>
<p>返回: <?=$doc['return']['desc']?> </p>
<table border="1">
  <tr>
    <th>字段</th>
    <th>类型</th>
    <th>必需</th>
    <th>说明</th>
  </tr>
  <?php foreach ($doc['return']['types'] as $one) { ?>
  <tr>
    <td><?=$one['name']?></td>
    <td><?=$this->typeAnchor($one['type'], $prefix)?></td>
    <td><?=$one['required'] ? '是' : '否'?></td>
    <td><?=$one['desc']?></td>
  </tr>
  <?php } ?>

</table>

<?php foreach ($doc['custom'] as $type => $typeValue) { ?>
<p id="<?=$this->typeId($type, $prefix)?>"><?=$type?> 说明: </p>
<table border="1">
      <tr>
        <th>字段</th>
        <th>类型</th>
        <th>必需</th>
        <th>说明</th>
      </tr>
      <?php foreach ($typeValue as $one) { ?>
      <tr>
        <td><?=$one['name']?></td>
        <td><?=$this->typeAnchor($one['type'], $prefix)?></td>
        <td><?=$one['required'] ? '是' : '否'?></td>
        <td><?=$one['desc']?></td>
      </tr>
    <?php } ?>

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

        $paramPattern = '(\S+)\s+\$?(\S+)\s+(required)?\s*(.*)';

        $doc = [
            'method' => 'GET',
            'uri' => '',
            'desc' => [],
            'param' => [],
            'errorCode' => [],
            'example' => [],
            'return' => ['desc' => null, 'types' => $this->getReturnDefault()],
            'custom' => [],
            'idPrefix' => '',
        ];
        $last = null;
        $lastData = null;
        $lines = explode("\n", $cmt);
        foreach ($lines as $one) {
            $subject = preg_replace('/^\s*\*\\/?\s?/', '', $one);

            if (empty($subject)) {
                continue;
            }
            else if (preg_match('/@gendoc/', $subject)) {
                continue;
            }
            else if (strpos($subject, '@end') !== false) {
                break;
            }
            else if (preg_match('/@idPrefix\s+(\S+)/', $subject, $mat)) {
                $doc['idPrefix'] = $mat[1];
            }
            else if (preg_match('/@method\s+(.*)/', $subject, $mat)) {
                $doc['method'] = $mat[1];
            }
            else if (preg_match('/@uri\s+(.*)/', $subject, $mat)) {
                $doc['uri'] = $mat[1];
            }
            else if (preg_match('/@desc\s+(.*)/', $subject, $mat)) {
                $doc['desc'][] = $mat[1];
                $last = 'desc';
            }
            else if (preg_match("/@param\s+$paramPattern/", $subject, $mat)) {
                $doc['param'][$mat[2]] = $this->paramArray($mat);
            }
            else if (preg_match('/@errorCode\s+(\S+)\s+(\S+)/', $subject, $mat)) {
                $doc['errorCode'][$mat[1]] = $mat[2];
            }
            else if (preg_match('/@example\s*(.*$)/', $subject, $mat)) {
                $last = 'example';
                $lastData = $mat[1] ?: count($doc['example']);
            }
            else if (preg_match('/@return\s*(.*)/', $subject, $mat)) {
                $doc['return']['desc'] = $mat[1];
                $last = 'return';
            }
            else if (preg_match('/@(\S+)\s*$/', $subject, $mat)) {
                $last = 'custom';
                $lastData = $mat[1];
            }
            else {
                if ($last == 'return') {
                    if (preg_match("/$paramPattern/", $subject, $mat)) {
                        $doc['return']['types'][$mat[2]] = $this->paramArray($mat);
                    }
                }
                else if ($last == 'example') {
                    $doc['example'][$lastData][] = $subject;
                }
                else if ($last == 'custom') {
                    if (preg_match("/$paramPattern/", $subject, $mat)) {
                        $doc['custom'][$lastData][$mat[2]] = $this->paramArray($mat);
                    }
                }
                else if (isset($doc[$last])) {
                    $doc[$last][] = $subject;
                }
            }
        }

        if (empty($doc['idPrefix'])) {
            $doc['idPrefix'] = str_replace('/', '_', $doc['uri']) . '_v_';
        }

        return $doc;
    }

    private function paramArray($mat)
    {
        return [
            'type' => $mat[1],
            'name' => $mat[2],
            'required' => $mat[3] == 'required',
            'desc' => $mat[4],
        ];
    }

}

