<?php

namespace Chuntent\Extension\Tools\Marketing\Filter;



/**
 * 多个过滤器, 优先级从前到后.
 * @author windq
 * $Id: ChainFilter.php 15388 2015-06-19 08:21:13Z yangfeng $
 *
 */
class ChainFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters;


    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param array $mobiles
     * @return FilterResult
     */
    public function filter(array $mobiles)
    {
        $result = new FilterResult();

        foreach ($this->filters as $f)
        {
            $tempRs = $f->filter($mobiles);
            $result->merge($tempRs);
            // mobiles的值要动态变
            $mobiles = $tempRs->getAbstain();
        }
        return $result;
    }
}
