<?php


namespace Chuntent\Extension\Tools\Marketing\Filter;


use Chuntent\Extension\Tools\CI\DbAwareTrait;

/**
 * m2_config里的数据
 * @author windq
 * $Id: M2ConfigFilter.php 15388 2015-06-19 08:21:13Z yangfeng $
 */
class M2ConfigFilter extends BaseFilter
{

    use DbAwareTrait;

    public function getMoblieArrayByType($type, $mobiles)
    {
        if (empty($mobiles))
        {
            return [];
        }

        $db = $this->getCiDb();
        $db->where('m_type', $type);
        $db->where_in('m_value', $mobiles);
        $rs = $db->get('m2_config')->result_array();

        return array_map(function ($a) {
            return $a['m_value'];
        }, $rs);
    }
}
