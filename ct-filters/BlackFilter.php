<?php


namespace Chuntent\Extension\Tools\Marketing\Filter;


use Chuntent\Extension\Tools\Marketing\Version2\MkConsts;
/**
 * 黑名单过滤
 * @author windq
 * $Id: BlackFilter.php 15392 2015-06-19 11:52:38Z yangfeng $
 */
class BlackFilter extends M2ConfigFilter
{

    protected function denySource($mobiles)
    {
        return $this->getMoblieArrayByType(MkConsts::CONFIG_TYPE_BLACK, $mobiles);
    }

    protected function getName()
    {
        return '黑名单系统';
    }

}
