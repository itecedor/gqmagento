<?php

namespace POSIMWebExt\GCLink\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Posimgiftcard extends AbstractTotal
{
    public function collect(Invoice $invoice)
    {
        $invoice->setPosimgiftcard(0);
        $invoice->setBasePosimgiftcard(0);
        $amount = $invoice->getOrder()->getPosimgiftcard();
        $invoice->setPosimgiftcard($amount);
        $amount = $invoice->getOrder()->getBasePosimgiftcard();
        $invoice->setBasePosimgiftcard($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() - $invoice->getPosimgiftcard());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $invoice->getPosimgiftcard());

        return $this;
    }
}