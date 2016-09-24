<?php


namespace Chuntent\Extension\Tools\Marketing\Filter;

/**
 * 自定义数组过滤
 * @author windq
 * $Id: ArrayFilter.php 15388 2015-06-19 08:21:13Z yangfeng $
 */
class ArrayFilter extends BaseFilter
{
    /**
     * 通过名单
     */
    private $passed;
    /**
     * 禁止名单
     */
    private $denied;

    private $name;

    public function __construct($passed = [], $denied = [], $name = 'af')
    {
        $this->passed = $passed;
        $this->denied = $denied;
        $this->name = $name;

        $this->setDefaultSources();
    }


    protected function passSource($mobiles)
    {
        return array_intersect($this->passed, $mobiles);
    }

    protected function denySource($mobiles)
    {
        return array_intersect($this->denied, $mobiles);
    }

    protected function getName()
    {
        return $this->name;
    }
}
