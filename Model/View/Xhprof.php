<?php

namespace Daseraf\Debug\Model\View;

use Daseraf\Debug\Helper\Formatter;
use Daseraf\Debug\Model\View\Renderer\XhprofProfile;
use Daseraf\Debug\Model\View\Renderer\XhprofProfileFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Xhprof implements ArgumentInterface
{
    /**
     * @var XhprofProfileFactory
     */
    private $xhprofProfileFactory;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var XhprofProfile
     */
    private $calculatedProfile;

    /**
     * @param XhprofProfileFactory $xhprofProfileFactory
     * @param Formatter $formatter
     * @param RequestInterface $request
     */
    public function __construct(
        XhprofProfileFactory $xhprofProfileFactory,
        Formatter $formatter,
        RequestInterface $request
    ) {
        $this->xhprofProfileFactory = $xhprofProfileFactory;
        $this->formatter = $formatter;
        $this->request = $request;
    }

    public function getXhprofProfile($runData)
    {
        if ($this->calculatedProfile) {
            return $this->calculatedProfile;
        }

        $profileData['profile'] = $runData;
        /** @var XhprofProfile $profile */
        $profile = $this->xhprofProfileFactory->create($profileData);
        $profile->calculateSelf();

        $this->calculatedProfile = $profile;

        return $this->calculatedProfile;
    }

    public function getRelatives(XhprofProfile $profile)
    {
        $functionName = $this->request->getParam('function');

        return $profile->getRelatives($functionName, null, 1);
    }

    public function formatTime($value)
    {
        return $this->formatter->revertMicrotime($value);
    }

    public function formatBytes($value)
    {
        return $this->formatter->formatBytes(abs($value), 'M');
    }
}
