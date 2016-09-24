<?php


namespace Chuntent\Extension\Tools\Marketing\Filter;

/**
 * 过滤结果
 * @author windq
 * $Id: FilterResult.php 15388 2015-06-19 08:21:13Z yangfeng $
 */
class FilterResult
{
    /**
     * 通过手机号
     */
    private $passed = array();
    /**
     * 通过的原因, 结构为 [ 手机号 => [ 原因1, 原因2, ...] ]
     */
    private $passedReason = array();

    /**
     * 禁止手机号
     */
    private $denied = array();
    /**
     * 禁止的原因, 结构为 [ 手机号 => [ 原因1, 原因2, ...] ]
     */
    private $deniedReason = array();

    /**
     * 放弃检查的手机号, 因为在过滤器中, 不知道该怎么办
     */
    private $abstain = array();

    /**
     * @param array $mobileArray
     * @param array $reasonArray 结构为[[手机号,原因],...]
     */
    public function addPassed($mobileArray, $reasonArray = [])
    {
        $this->passed = array_merge($this->passed, $mobileArray);

        foreach ($reasonArray as $r)
        {
            $this->passedReason[$r[0]][] = $r[1];
        }
    }

    public function addDenied($mobileArray, $reasonArray = [])
    {
        $this->denied = array_merge($this->denied, $mobileArray);

        foreach ($reasonArray as $r)
        {
            $this->deniedReason[$r[0]][] = $r[1];
        }
    }

    public function addAbstain($mobileArray)
    {
        $this->abstain = array_unique(array_diff(array_merge($this->abstain, $mobileArray), $this->passed, $this->denied));
    }

    /**
     * 和另外一个过滤结果类合并
     * @param FilterResult $other
     */
    public function merge(FilterResult $other)
    {
        $this->addPassed($other->getPassed());
        $this->passedReason = $this->mergeReason($this->passedReason, $other->getPassedReason());

        $this->addDenied($other->getDenied());
        $this->deniedReason = $this->mergeReason($this->deniedReason, $other->getDeniedReason());

        $this->addAbstain($other->getAbstain());
    }

    private function mergeReason($thisReason, $otherReason)
    {
        foreach ($otherReason as $m => $vals)
        {
            if (isset($thisReason[$m]))
            {
                $thisReason[$m] = array_merge($thisReason[$m], $vals);
            } else
            {
                $thisReason[$m] = $vals;
            }
        }

        return $thisReason;
    }



    public function getPassed()
    {
        return $this->passed;
    }
    public function getPassedReason($m = null)
    {
        if ($m !== null)
        {
            return $this->passedReason[$m];
        }
        return $this->passedReason;
    }
    public function getDenied()
    {
        return $this->denied;
    }
    public function getDeniedReason($m = null)
    {
        if ($m !== null)
        {
            return $this->deniedReason[$m];
        }

        return $this->deniedReason;
    }
    public function getAbstain()
    {
        return $this->abstain;
    }



    public function getTableMessage()
    {
        $fr = $this;
        ob_start();

        $pass = $fr->getPassed();
        if ($pass)
        {
            echo '<table><tr><th>通过号码</th><th>通过原因</th>';

            foreach ($pass as $m)
            {
                echo '<tr><td>', $m, '</td><td>', implode(',', $fr->getPassedReason($m)), '</td></tr>';
            }
            echo '</table>';
        }

        $deny = $fr->getDenied();

        if ($deny)
        {
            echo '<table><tr><th>禁止号码</th><th>禁止原因</th>';

            foreach ($deny as $m)
            {
                echo '<tr><td>', $m, '</td><td>', implode(',', $fr->getDeniedReason($m)), '</td></tr>';
            }
            echo '</table>';
        }

        return ob_get_clean();
    }
}
