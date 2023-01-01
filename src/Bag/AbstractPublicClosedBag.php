<?php

namespace ASV\Bags\Bag;

use ASV\Bags\BagPicker\BagPickerInterface;

abstract class AbstractPublicClosedBag extends AbstractClosedBag
{
    public function get(BagPickerInterface $bagPicker): mixed
    {
        if (!$this->isBagInitialized) {
            static::throwNotInitializedBagException();
        }

        if ($this->isElementSetInBag($bagPicker)) {
            return $this->getElementInBag($bagPicker);
        } else {
            static::throwElementNotFoundInBagException($bagPicker);
        }
    }

    protected static function throwNotInitializedBagException(): void
    {
        $class = static::class;
        throw new NotInitializedBagException("$class is not initialized");
    }
}