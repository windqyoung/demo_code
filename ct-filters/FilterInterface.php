<?php



namespace Chuntent\Extension\Tools\Marketing\Filter;


interface FilterInterface
{

    /**
     * @param array $mobiles
     * @return FilterResult
     */
    public function filter(array $mobiles);

}
