<?php

namespace POSIMWebExt\GCLink\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use POSIMWebExt\GCLink\Helper\Data;

class Posimgiftcard extends AbstractTotal
{
    protected $gcHelper;

    public function __construct(Data $gcHelper, array $data = [])
    {
        $this->gcHelper = $gcHelper;
        parent::__construct($data);
    }

    public function collect(Creditmemo $creditmemo)
    {
        $creditmemo->setPosimgiftcard(0);
        $creditmemo->setBasePosimgiftcard(0);
        $amount = $creditmemo->getOrder()->getPosimgiftcard();
        $creditmemo->setPosimgiftcard($amount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $amount);
        $baseAmount = $creditmemo->getOrder()->getBasePosimgiftcard();
        $creditmemo->setBasePosimgiftcard($baseAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseAmount);

        return $this;
    }
}