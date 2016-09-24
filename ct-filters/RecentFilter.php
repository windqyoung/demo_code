<?php


namespace Chuntent\Extension\Tools\Marketing\Filter;


use Chuntent\Extension\Tools\CI\DbAwareTrait;


/**
 * 从某时间之后不发送
 * @author windq
 */
class RecentFilter extends BaseFilter
{
    use DbAwareTrait;


    private $date;

    public function __construct($dateStr = '-15 days')
    {
        parent::__construct();
        $this->date = date('Y-m-d H:i:s', strtotime($dateStr));
    }

    private $mbTimeMap = [];

    protected function denySource($mobiles)
    {
        if (empty($mobiles))
        {
            return [];
        }

        $db = $this->getCiDb();

        $db->where('create_time >=', $this->date);
        $db->where_in('mobile', $mobiles);

        $rs = $db->get('m2_send_log')->result_array();

        return array_map(function ($a) {
            $this->mbTimeMap[$a['mobile']] = $a['create_time'];
            return $a['mobile'];
        }, $rs);
    }

    /**
     * 默认禁止原因(单条)
     */
    protected function buildDeniedReason($m)
    {
        return sprintf('%s 由 %s 禁止, 上次发送时间: %s', $m, $this->getName()
                , $this->mbTimeMap[$m]);
    }

    protected function getName()
    {
        return '发送日志系统D(' . $this->date . ')';
    }


}
