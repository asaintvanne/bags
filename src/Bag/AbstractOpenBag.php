<?php

namespace ASV\Bags\Bag;

use ASV\Bags\BagPicker\BagPickerInterface;

abstract class AbstractOpenBag extends AbstractBag
{
    abstract protected function findElement(BagPickerInterface $bagPicker): mixed;

    public function get(BagPickerInterface $bagPicker): mixed
    {
        if ($this->isElementSetInBag($bagPicker)) {
            return $this->getElementInBag($bagPicker);
        } else {
            $element = $this->findElement($bagPicker);
            $this->pushElementInBag($element, $bagPicker);

            return $element;
        }
    }
}