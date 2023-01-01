<?php

namespace ASV\Bags\BagPicker;

interface BagPickerInterface
{
    public function getIndexes(): array;

    public function getParametersToFindElement(): array;
}