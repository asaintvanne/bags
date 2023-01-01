# Bag system

## Presentation

Concept is simple. You have a bag. You push data in, and you pick them.
A bag helps you to retrieve easily data once, so as not to query a database more times than necessary, for example, without thinking of limiting the number of queries.

## How it works

To create your own bags it's necessary to extend one of bag classes in this package. Although each class can be extended, there are three main models that differ in how they retrieve data.

### BagPicker

A BagPicker is an object used to pick elements in a bag. It should implement `BagPickerInterface`:
- `getIndexes` returns indexes used to pick element into bag.
- `getParametersToFindElement` returns arguments used to find element from data provider.
```php

namespace MyApp\Bag;

use MyApp\Entity\Country;

class MyCustomBagPicker implements BagPickerInterface
{
    public Country $country;

    public string $elementCode;

    public function __construct(Country $country, string $elementCode)
    {
        $this->country = $country;
        $this->elementCode = $elementCode;
    }

    public function getIndexes(): array
    {
        return [$this->country->getId(), $this->elementCode];
    }

    public function getParametersToFindElement(): array
    {
        return [$this->country, $this->elementCode];
    }
}
```
### Bag

#### AbstractOpenBag

- `Open` means you can push elements during all bag lifetime. If the element you search is not set, bag retrieve it and provide you the element.

```php
<?php

namespace MyApp\Bag;

use ASV\Bags\Bag\AbstractOpenBag;
use ASV\Bags\BagPicker\BagPickerInterface;
use MyApp\Entity\Country;

class MyCustomOpenBag extends AbstractOpenBag
{
  //For reminder, the get function in AbstractOpenBag
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

  protected function findElement(BagPickerInterface $bagPicker)
  {
    $arguments = $bagPicker->getParametersToFindElement();
    $elementProvider = //Get your repository or anything else
    
    return $elementProvider->findElementByCountryAndCode(...$arguments);

  }
}

$bag = new MyCustomOpenBag();

$datas = //Get from source
foreach ($datas as $data) {
  $element = $bag->get(new MyCustomBagPicker($data['country'], $data['code'));
  //Do something with element
}
```

#### AbstractClosedPrivateBag

- `Closed` means you cannot push elements during bag lifetime, only at initialization.
- `Private` means bag is initialized from inside.

```php
<?php

namespace MyApp\Bag;

use ASV\Bags\BagPicker\BagPickerInterface;

class MyCustomClosedPrivateBag extends AbstractClosedPrivateBag
{
    //For reminder, the get function in AbstractClosedPrivateBag
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

    protected function initializeBag(): void
    {
        $elementProvider = //Get your repository or anything else
   
        $elements = $elementProvider->findAll();
        foreach ($elements as $element) {
            $this->pushElementInBag($element, new MyCustomBagPicker($element->getCountry(), $element->code))
        }
    }
}

$bag = new MyCustomClosedPrivateBag();

$datas = //Get from provider
foreach ($datas as $data) {
  $element = $bag->get(new MyCustomBagPicker($data['country'], $data['code'));
  //Do something with element
}
```

#### AbstractClosedPublicBag

- `Closed` means you cannot push elements during bag lifetime, only at initialization.
- `Public` means bag is initialized from outside.

```php
<?php

namespace MyApp\Bag;

use ASV\Bags\BagPicker\BagPickerInterface;

class MyCustomClosedPublicBag extends AbstractClosedPublicBag
{
    //For reminder, the get function in AbstractClosedPublicBag
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

    public function customInitializeBag(array $codes): void
    {
        $elementProvider = //Get your repository or anything else
   
        $elements = $elementProvider->findElementsByCodesForAllCountries($codes);
        foreach ($elements as $element) {
            $this->pushElementInBag($element, new MyCustomBagPicker($element->getCountry(), $element->code))
        }
        
        $this->isBagInitialized = true;
    }
}

$codes = //Get from provider
$bag = new MyCustomClosedPublicBag();
$bag->customInitializeBag($codes);

$datas = //Get from provider
foreach ($datas as $data) {
  $element = $bag->get(new MyCustomBagPicker($data['country'], $data['code'));
  //Do something with element
}
```