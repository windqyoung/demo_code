<?php



$log = new PmSqlLog(__DIR__ . '/../../pm.my/processmaker/shared/log/propel.log');

echo $log->showTable();

class PmSqlLog
{
    private $logPath;

    private $readLineNum = 100;

    public function __construct($logPath)
    {
        $this->logPath = realpath($logPath);
    }


    public function showTable()
    {
        $rows = $this->splitLines($this->getLines());

        $rs = '<table border=1>';

        foreach ($rows as $r)
        {
            $rs .= '<tr><td>' . implode('</td><td>', $r) . '</td></tr>';
        }

        $rs .= '</table>';

        return $rs;
    }

    public function getLines()
    {
        if (! is_file($this->logPath))
        {
            throw new Exception('文件不存在 ');
        }
        $fp = fopen($this->logPath, 'r');

        if (! $fp)
        {
            throw new Exception('文件打开失败');
        }

        $rs = [];

		$lineNo = 1;

        while (! feof($fp))
        {
            $line = fgets($fp);
            if (! $line)
            {
                continue;
            }

            $rs[] = $lineNo . '|' . $line;
			$lineNo ++;

            if (count($rs) > $this->readLineNum)
            {
                array_shift($rs);
            }
        }

        fclose($fp);

        return array_reverse($rs);
    }

    public function splitLines($lines)
    {
        return array_map(function ($line) {
            return explode('|', $line);
        }, $lines);
    }
}
