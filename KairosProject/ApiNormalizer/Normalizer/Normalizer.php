<?php
declare(strict_types=1);
/**
 * This file is part of the kairos project.
 *
 * As each files provides by the CSCFA, this file is licensed
 * under the MIT license.
 *
 * PHP version 7.2
 *
 * @category Api_Normalizer_Normalizer
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiNormalizer\Normalizer;

use KairosProject\ApiController\Event\ProcessEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use KairosProject\ApiLoader\Loader\AbstractApiLoader;
use KairosProject\ApiNormalizer\Normalizer\NormalizerInterface as BaseInterface;
use KairosProject\ApiNormalizer\Event\NormalizingEvent;

/**
 * Normalizer
 *
 * This class is the default NoramlizerInterface implementation
 *
 * @category Api_Normalizer_Normalizer
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class Normalizer implements BaseInterface
{
    /**
     * Normalized data key
     *
     * Define the parameter where put the normalized data
     *
     * @var string
     */
    public const NORMALIZED_DATA = 'data';

    /**
     * Event before normalization
     *
     * Define the event fired before normalization
     *
     * @var string
     */
    public const EVENT_BEFORE = 'event_before_normalization';

    /**
     * Event after normalization
     *
     * Define the event fired after normalization
     *
     * @var string
     */
    public const EVENT_AFTER = 'event_after_normalization';

    /**
     * Logger
     *
     * The application logger
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Normalizer
     *
     * The normalizer instance, used to normalize the given input
     *
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * Context
     *
     * The base normalizer context to be applyed
     *
     * @var array
     */
    private $context;

    /**
     * Input parameter
     *
     * Define the parameter representing the data to be normalized
     *
     * @var string
     */
    private $inputParameter = AbstractApiLoader::EVENT_KEY_STORAGE;

    /**
     * Output parameter
     *
     * Define the parameter where the normalized data will be injected
     *
     * @var string
     */
    private $outputParameter = self::NORMALIZED_DATA;

    /**
     * Before normalize event
     *
     * Define the event fired before normalization
     *
     * @var string
     */
    private $beforeNormalizeEvent = self::EVENT_BEFORE;

    /**
     * After normalize event
     *
     * Define the event fired after normalization
     *
     * @var string
     */
    private $afterNormalizeEvent = self::EVENT_AFTER;

    /**
     * Constructor
     *
     * The default constructor of the Noramlizer store a logger and a normalizer instance to allow normalization
     * process. Optionnaly, a context, the I/O process event parameters and the event names for 'before' and 'after'
     * normalization events can be given.
     *
     * @param LoggerInterface     $logger               The logger instance
     * @param NormalizerInterface $normalizer           The normalizer
     * @param array               $context              The default normalization context
     * @param string              $inputParameter       The input process event parameter name
     * @param string              $outputParameter      The output process event parameter name
     * @param string              $beforeNormalizeEvent The event fired before normalization
     * @param string              $afterNormalizeEvent  The event fired after normalization
     *
     * @return void
     */
    public function __construct(
        LoggerInterface $logger,
        NormalizerInterface $normalizer,
        array $context = [],
        string $inputParameter = AbstractApiLoader::EVENT_KEY_STORAGE,
        string $outputParameter = self::NORMALIZED_DATA,
        string $beforeNormalizeEvent = self::EVENT_BEFORE,
        string $afterNormalizeEvent = self::EVENT_AFTER
    ) {
        $this->logger = $logger;
        $this->normalizer = $normalizer;
        $this->context = $context;
        $this->inputParameter = $inputParameter;
        $this->outputParameter = $outputParameter;
        $this->beforeNormalizeEvent = $beforeNormalizeEvent;
        $this->afterNormalizeEvent = $afterNormalizeEvent;
    }

    /**
     * Normalize
     *
     * Process the normalization
     *
     * @param ProcessEventInterface    $event      The original process event
     * @param string                   $eventName  The original event name
     * @param EventDispatcherInterface $dispatcher The original event dispatcher
     *
     * @return void
     */
    public function normalize(ProcessEventInterface $event, $eventName, EventDispatcherInterface $dispatcher) : void
    {
        if (!$event->hasParameter($this->inputParameter)) {
            $message = sprintf(
                'The expected parameter "%s" does not exist in the process event',
                $this->inputParameter
            );

            $this->logger->error($message);
            throw new \LogicException($message);
        }

        $normalizerEvent = new NormalizingEvent(
            $event,
            $this->context,
            $event->getParameter($this->inputParameter)
        );
        $dispatcher->dispatch($this->beforeNormalizeEvent, $normalizerEvent);

        $normalizerEvent->setData(
            $this->normalizer->normalize(
                $normalizerEvent->getData(),
                null,
                $normalizerEvent->getContext()
            )
        );

        $dispatcher->dispatch($this->afterNormalizeEvent, $normalizerEvent);

        $event->setParameter($this->outputParameter, $normalizerEvent->getData());
    }
}
