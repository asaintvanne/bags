<?php

namespace ASV\Bags\Bag;

use ASV\Bags\BagPicker\BagPickerInterface;
use ASV\Bags\Exception\ElementNotFoundInBagException;

abstract class AbstractClosedBag extends AbstractBag
{
    protected bool $isBagInitialized = false;

    protected static function throwElementNotFoundInBagException(BagPickerInterface $bagPicker): void
    {
        $class = static::class;
        $indexes = implode(',', $bagPicker->getIndexes());
        throw new ElementNotFoundInBagException("Element not found in $class for indexes [$indexes]");
    }
}