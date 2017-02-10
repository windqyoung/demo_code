<?php

class TestClass
{
	/**
     * @doc
     * @method   GET
     * @uri /user/doc
     * @desc 测试生成文档
     *      说明的内容
     * 说明内容第二行
     * @param string $id required 说明
     * @param string $code 说明code
     * @param string desc 说明desc
     * @errorCode 123 说明123
     *
     * @return array
     * string account  required 说明1
     * string vcode  vcode说明2
     * @arraykeytype arraykey  required 说明
     *
     * @arraykeytype
     * string key1 required 说明3
     * @arraykey2 arraykey2  数组说明2
     *
     * @arraykey2
     * type2 key2 说明4
     * type3 key3 说明5
     *
     * @example {"json": "返回示例",
     *      "json2": "返回2"
     *
     * }
     */
    public function docAction($id)
    {

    }
}


$doc = new DocGen();
$doc->gen(TestClass::class);







class DocGen
{
    public function gen($cls)
    {
        $ref = new \ReflectionClass($cls);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        $documents = [];
        foreach ($methods as $m /** @var $m \ReflectionMethod */) {
            $cmt = $m->getDocComment();
            $documents[] = $this->parseCmt($cmt);
        }

        $documents = array_filter($documents);

        foreach ($documents as $doc) {
            echo $this->genDocHtml($doc);
            echo "</p>\n";
        }

    }

    public function genDocHtml($doc)
    {
        ob_start();

        ?>
    <h1><?=$doc['desc'][0]?> [<?=$doc['method']?> <?=$doc['uri']?>]</h1>
    <p>说明: </p>
    <?php for ($i = 1, $len = count($doc['desc']); $i < $len; $i ++) { ?>
    <p><?=$doc['desc'][$i]?></p>
    <?php } ?>

    <p>参数: </p>
    <table border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th>字段</th>
        <th>类型</th>
        <th>必需</th>
        <th>说明</th>
      </tr>
      <?php foreach ($doc['param'] as $one) { ?>
      <tr>
        <td><?=$one['name']?></td>
        <td><?=$one['type']?></td>
        <td><?=$one['required'] ? '是' : '否'?></td>
        <td><?=$one['desc']?></td>
      </tr>
      <?php } ?>
    </table>


    <p>错误代码: </p>
    <table border="1" cellpadding="0" cellspacing="0">
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


    <p>返回: </p>
    <table border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th>字段</th>
        <th>类型</th>
        <th>必需</th>
        <th>说明</th>
      </tr>
      <?php foreach ($doc['return'] as $one) { ?>
      <tr>
        <td><?=$one['name']?></td>
        <td><?=$one['type']?></td>
        <td><?=$one['required'] ? '是' : '否'?></td>
        <td><?=$one['desc']?></td>
      </tr>
      <?php } ?>
    </table>

    <?php foreach ($doc['custom'] as $type => $typeValue) { ?>
    <p><?=$type?> 说明: </p>
    <table border="1" cellpadding="0" cellspacing="0">
          <tr>
            <th>字段</th>
            <th>类型</th>
            <th>必需</th>
            <th>说明</th>
          </tr>
          <?php foreach ($typeValue as $one) { ?>
          <tr>
            <td><?=$one['name']?></td>
            <td><?=$one['type']?></td>
            <td><?=$one['required'] ? '是' : '否'?></td>
            <td><?=$one['desc']?></td>
          </tr>
        <?php } ?>
    </table>

    <?php } ?>


    <p>示例</p>

<?php
    $exp = implode("\n", $doc['example']);
    $json = json_decode($exp);
    if ($json) {
        $text = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    else {
        $text = $exp;
    }
?>
    <pre>
<?=$text?>
    </pre>

        <?php



        return ob_get_clean();
    }

    public function parseCmt($cmt)
    {
        if (strpos($cmt, '@doc') === false) {
            return;
        }
        $doc = [];
        $last = null;
        $lastData = null;
        $lines = explode("\n", $cmt);
        foreach ($lines as $one) {
            $subject = preg_replace('/^\s*\*\\/?\s?/', '', $one);

            if (empty($subject)) {
                continue;
            }
            else if (preg_match('/@doc/', $subject)) {
                continue;
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
            else if (preg_match('/@param\s+(\S+)\s+\$?(\S+)\s+(required)?\s*(.*)/', $subject, $mat)) {
                $doc['param'][$mat[2]] = [
                    'type' => $mat[1],
                    'name' => $mat[2],
                    'required' => $mat[3] == 'required',
                    'desc' => $mat[4],
                ];
            }
            else if (preg_match('/@errorCode\s+(\S+)\s+(\S+)/', $subject, $mat)) {
                $doc['errorCode'][$mat[1]] = $mat[2];
            }
            else if (preg_match('/@example\s+(.*)/', $subject, $mat)) {
                $doc['example'][] = $mat[1];
                $last = 'example';
            }
            else if (preg_match('/@return/', $subject, $mat)) {
                $last = 'return';
            }
            else if (preg_match('/@(\S+)\s*$/', $subject, $mat)) {
                $last = 'custom';
                $lastData = $mat[1];
            }
            else {
                if ($last == 'return') {
                    if (preg_match('/(\S+)\s+(\S+)\s+(required)?\s*(\S+)/', $subject, $mat)) {
                        $doc['return'][$mat[2]] = [
                            'type' => $mat[1],
                            'name' => $mat[2],
                            'required' => $mat[3] == 'required',
                            'desc' => $mat[4],
                        ];
                    }
                }
                else if ($last == 'custom') {
                    if (preg_match('/(\S+)\s+(\S+)\s+(required)?\s*(\S+)/', $subject, $mat)) {
                        $doc['custom'][$lastData][$mat[2]] = [
                            'type' => $mat[1],
                            'name' => $mat[2],
                            'required' => $mat[3] == 'required',
                            'desc' => $mat[4],
                        ];
                    }
                }
                else if (isset($doc[$last])) {
                    $doc[$last][] = $subject;
                }
            }
        }

        return $doc;
    }
}

