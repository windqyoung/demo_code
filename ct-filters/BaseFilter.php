<?php


namespace Chuntent\Extension\Tools\Marketing\Filter;

/**
 * 负责过滤手机号的基类
 * @author windq
 * $Id: BaseFilter.php 15388 2015-06-19 08:21:13Z yangfeng $
 */
class BaseFilter implements FilterInterface
{
    /**
     * 要求返回结构为: [手机号,手机号,...]
     * @var callable
     */
    private $passSource;
    /**
     * 要求返回结构为: [[手机号,原因],[手机号,原因],...]
     * @var callable
     */
    private $passReasonSource;
    /**
     * 要求返回结构为: [手机号,手机号,...]
     * @var callable
     */
    private $denySource;
    /**
     * 要求返回结构为: [[手机号,原因],[手机号,原因],...]
     * @var callable
     */
    private $denyReasonSource;


    public function __construct($setDefault = true)
    {
        if ($setDefault)
        {
            $this->setDefaultSources();
        }
    }

    /**
     * 设置数据来源
     * @param callable $passSource
     * @param callable $passReasonSource
     * @param callable $denySource
     * @param callable $denyReasonSource
     */
    public function setSources($passSource, $passReasonSource,
            $denySource, $denyReasonSource)
    {
        $this->passSource = $passSource;
        $this->passReasonSource = $passReasonSource;
        $this->denySource = $denySource;
        $this->denyReasonSource = $denyReasonSource;

        $this->checkSources();
    }

    /**
     * 检查所有变量都为回调
     * @throws \InvalidArgumentException
     */
    private function checkSources()
    {
        if (! is_callable($this->passSource)) {
            throw new \InvalidArgumentException('$passSource必须为回调函数');
        }
        if (! is_callable($this->passReasonSource)) {
            throw new \InvalidArgumentException('$passReasonSource必须为回调函数');
        }
        if (! is_callable($this->denySource)) {
            throw new \InvalidArgumentException('$denySource必须为回调函数');
        }
        if (! is_callable($this->denyReasonSource)) {
            throw new \InvalidArgumentException('$denyReasonSource必须为回调函数');
        }
    }

    /**
     * 设置默认的回调函数, 是在本对象中.
     */
    protected function setDefaultSources()
    {
        $this->setSources([ $this, 'passSource' ], [ $this, 'passReasonSource' ],
                [ $this, 'denySource' ], [ $this, 'denyReasonSource' ]);
    }

    /**
     * 执行过滤
     * @see FilterInterface::filter()
     */
    public function filter(array $mobiles)
    {
        // 检查需要的几个函数能不能
        $this->checkSources();

        // 保存过滤结果
        $res = new FilterResult();

        // 获取通过的数据
        $passedMobile = call_user_func($this->passSource, $mobiles);
        // 构建通过原因
        $passedReason = call_user_func($this->passReasonSource, $passedMobile, $mobiles);
        $res->addPassed($passedMobile, $passedReason);

        // 获取禁止数据
        $denyMobile = call_user_func($this->denySource, $mobiles);
        // 构建禁止原因
        $denyReason = call_user_func($this->denyReasonSource, $denyMobile, $mobiles);
        $res->addDenied($denyMobile, $denyReason);

        // 非通过, 非禁止, 则为未知数据.
        $res->addAbstain(array_diff($mobiles, $passedMobile, $denyMobile));

        return $res;
    }

    /**
     * 默认啥也不通过
     */
    protected function passSource($mobiles)
    {
        return [];
    }

    /**
     * 默认的通过原因(全体)
     */
    protected function passReasonSource($passedMobile, $mobiles)
    {
        return array_map(function ($a) {
             return [$a, $this->buildPassReason($a)];
        }, $passedMobile);
    }

    /**
     * 通过原因, 单条
     */
    protected function buildPassReason($m)
    {
        return sprintf('%s 由 %s 通过', $m, $this->getName());
    }

    /**
     * 名字, 在原因中使用
     * @return string
     */
    protected function getName()
    {
        return '基类';
    }

    /**
     * 默认没有禁止的数据
     */
    protected function denySource($mobiles)
    {
        return [];
    }

    /**
     *  默认的禁止原因(全体)
     */
    protected function denyReasonSource($deniedMobiles, $mobiles)
    {
        return array_map(function ($a) {
            return [$a, $this->buildDeniedReason($a)];
        }, $deniedMobiles);
    }

    /**
     * 默认禁止原因(单条)
     */
    protected function buildDeniedReason($m)
    {
        return sprintf('%s 由 %s 禁止', $m, $this->getName());
    }
}
