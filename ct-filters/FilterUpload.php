<?php



namespace Chuntent\Extension\Tools\Marketing\Filter;


use Chuntent\Extension\Tools\Marketing\MobileUpload;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Chuntent\Extension\Tools\Marketing\GenericEvent;
use Chuntent\Extension\Tools\Marketing\Mobiles;

/**
 * 有过滤功能, 事件监听功能的上传类.
 * @author windq
 */
class FilterUpload implements EventSubscriberInterface
{
    /**
     * @var MobileUpload
     */
    private $upload;

    /**
     * @var FilterInterface
     */
    private $filter;

    private $uploaded = false;

    private $redisTotal = 0;

    private $uploadTotal = 0;

    /**
     * @var FilterResult
     */
    private $filterResult;

    public function __construct($upload, FilterInterface $filter)
    {
        $this->upload = $upload;
        $this->filter = $filter;

        $this->upload->getDispatcher()->addSubscriber($this);
        $this->filterResult = new FilterResult();
    }

    public function upload()
    {
        if ($this->uploaded)
        {
            return;
        } else
        {
            $this->uploaded = true;
        }

        $this->upload->upload();
    }

    public static function getSubscribedEvents()
    {
        return [
            MobileUpload::EVENT_PRE_SAVE => [
                ['onPreSave'],
            ],
            MobileUpload::EVENT_POST_SAVE => [
                ['onPostSave'],
            ]
        ];
    }

    /**
     *
     * @param GenericEvent $e
     */
    public function onPreSave($e)
    {
        $raw = $e['mobiles']->getRaw();

        $this->uploadTotal += count($raw);

        $frs = $this->filter->filter($raw);

        $e['mobiles'] = new Mobiles($raw, array_merge($frs->getPassed(), $frs->getAbstain()), $frs->getDenied());

        $this->filterResult->merge($frs);
    }

    public function onPostSave($e)
    {
        /**
         * @var $saver \Chuntent\Extension\Tools\Marketing\RedisSetSaver
         */
        $saver = $e['saver'];
        $this->redisTotal = $saver->getTotal();
    }

    public function getTableMessage()
    {
        return $this->filterResult->getTableMessage();
    }
}
