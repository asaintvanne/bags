<?php

namespace ASV\Bags\Bag;

use ASV\Bags\BagPicker\BagPickerInterface;

abstract class AbstractBag
{
    private array $bag = [];

    abstract public function get(BagPickerInterface $bagPicker) :mixed;

    protected function isElementSetInBag(BagPickerInterface $bagPicker): bool
    {
        $current = $this->bag;
        foreach ($bagPicker->getIndexes() as $index) {
            if (!isset($current[$index])) {
                return false;
            }
            $current = $current[$index];
        }

        return true;
    }

    protected function getElementInBag(BagPickerInterface $bagPicker): mixed
    {
        $current = $this->bag;
        foreach ($bagPicker->getIndexes() as $index) {
            $current = $current[$index];
        }

        return $current;
    }

    protected function pushElementInBag(mixed $element, BagPickerInterface $bagPicker): void
    {
        $current = &$this->bag;
        foreach ($bagPicker->getIndexes() as $index) {
            if (!isset($current[$index])) {
                $current[$index] = [];
            }
            $current = &$current[$index];
        }
        $current = $element;
    }
}