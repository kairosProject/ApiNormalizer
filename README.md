# ApiNormalizer

A data normalizer listener to be attached to ApiController events

## 1)  Subject

The normalizer part of the API is in charge of the object transformation before the response formatting.

This normalization allows the formatting process to use a regular array type instead of a set of objects.

## 2) Class architecture

The API normalizer is a pure decorator class for more powerful existing normalizer. The choice to use the Symfony serializer was made to allow context configuration and advanced group usage in securitizing output.

## 3) Dependency description and use into the element

A the time of writing, the normalizer is designed to have four production dependencies as:

 * psr/log
 * symfony/event-dispatcher
 * kairos-project/api-loader
 * symfony/serializer

### 3.1) psr/log

The debugging and error retracement in each project parts is currently a fundamental law in development and it's missing is part of the OWASP top ten threats.

As defined by the third PHP standard reference, the logger components have to implement a specific interface. By the way, the logging system will be usable by each existing frameworks.

### 3.2) symfony/event-dispatcher

The normalizing system is designed to be easily extendable and will implement an event dispatching system, allowing the attachment and separation of logic by priority.

### 3.3) kairos-project/api-loader

The normalizer is made to be used by APIs and the generic system into kairos project is the API controller. Even, the system needs access to the API loader constants. This Loader makes use of the API controller itself.

### 3.4) symfony/serializer

The normalization logic can become hard to implement if we want to offer a generic interface. The Symfony serializer will be used to bypass this difficulty.

## 4) Implementation specification

The normalizer component will provide a unique method to normalize, that process the normalization.

The configuration of the component will be part of the instantiation arguments.

A normalizing event will be created to store the process event, the data, and the context.

#### 4.1) Dependency injection specification

The processing logic will need a normalizer instance, a parameter key to retrieve the elements to normalize and another one to store the result. The parameter key will be the API loader's one by default.

A default context will be provided to allow normalization settings, such as grouping.

Additionally, two event names will be injected to define the dispatched events.

#### 4.2) algorithm

#### 4.2) normalize algorithm

The normalize method is in charge of the data normalization.

```txt
We assume to receive the process event from the parameters.
We assume the process event parameter key to be part of the attributes.

Get the data to normalize from the process event, using the parameter key.
Create a distinct normalizer event. This element encloses the data to be normalized, the normalizer context, and the initial one.

Dispatch the before normalizing event.
Normalize the data and replace the normalize event content.
Dispatch the after normalizing event.

Set a new parameter into the process event to store the normalized data.
```

## 5) Usage

The symfony/property-access is needed to use ObjectNormalizer, and sensio/framework-extra-bundle required for annotation grouping.

#### 5.1) Basic usage

```PHP
use KairosProject\ApiNormalizer\Normalizer\Normalizer;
use KairosProject\ApiLoader\Loader\AbstractApiLoader;
use KairosProject\ApiController\Event\ProcessEvent;

$normalizer = new Normalizer(
    $objectNormalizer
);

$processEvent = new ProcessEvent();
$processEvent->setParameter(AbstractApiLoader::EVENT_KEY_STORAGE, $data);

$dispatcher->addListener('event', [$normalizer, 'normalize']);
$dispatcher->dispatch('event', $processEvent);

$normalizedData = $processEvent->getParameter(Normalizer::NORMALIZED_DATA);
```

#### 5.1) With property grouping

 ```PHP
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

AnnotationRegistry::registerLoader('class_exists');
$objectNormalizer = new ObjectNormalizer(
    new ClassMetadataFactory(
        new AnnotationLoader(
            new AnnotationReader()
        )
    )
);

$normalizer = new Normalizer(
    $objectNormalizer,
    ['groups' => ['output_group']]
);
```

#### 5.1) Complete constructor

 ```PHP
public function __construct(
    NormalizerInterface $normalizer,
    array $context = [],
    string $inputParameter = AbstractApiLoader::EVENT_KEY_STORAGE,
    string $outputParameter = Normalizer::NORMALIZED_DATA,
    string $beforeNormalizeEvent = Normalizer::EVENT_BEFORE,
    string $afterNormalizerEvent = Normalizer::EVENT_AFTER
);
```
