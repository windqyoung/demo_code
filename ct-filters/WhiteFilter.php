<?php


namespace Chuntent\Extension\Tools\Marketing\Filter;


use Chuntent\Extension\Tools\Marketing\Version2\MkConsts;
/**
 * 管理员白名单过滤
 * @author windq
 * $Id: WhiteFilter.php 15392 2015-06-19 11:52:38Z yangfeng $
 */
class WhiteFilter extends M2ConfigFilter
{

    protected function passSource($mobiles)
    {
        return $this->getMoblieArrayByType(MkConsts::CONFIG_TYPE_WHITE, $mobiles);
    }

    protected function getName()
    {
        return '白名单系统';
    }

}
