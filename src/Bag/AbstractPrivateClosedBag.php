<?php

namespace ASV\Bags\Bag;

use ASV\Bags\BagPicker\BagPickerInterface;

abstract class AbstractPrivateClosedBag extends AbstractClosedBag
{
    abstract protected function initializeBag(): void;

    public function get(BagPickerInterface $bagPicker): mixed
    {
        if (!$this->isBagInitialized) {
            $this->initializeBag();
        }

        if ($this->isElementSetInBag($bagPicker)) {
            return $this->getElementInBag($bagPicker);
        } else {
            static::throwElementNotFoundInBagException($bagPicker);
        }
    }
}