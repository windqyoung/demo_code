<?php



namespace Chuntent\Extension\Tools\Marketing\Filter;


use Chuntent\Extension\Tools\CI\DbAwareTrait;


/**
 * 退订名单过滤
 * @author windq
 * $Id: UnsubFilter.php 15394 2015-06-19 12:01:38Z yangfeng $
 */
class UnsubFilter extends BaseFilter
{
    use DbAwareTrait;

    protected function denySource($mobiles)
    {
        if (empty($mobiles))
        {
            return [];
        }

        $db = $this->getCiDb();
        $db->where_in('mobile', $mobiles);
        $rs = $db->get('m2_unsub')->result_array();

        return array_map(function ($a) {
            return $a['mobile'];
        }, $rs);
    }

    protected function getName()
    {
        return '退订系统D';
    }

}
