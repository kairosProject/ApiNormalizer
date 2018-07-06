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
 * @category Api_Normalizer_Event
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiNormalizer\Event;

use Symfony\Component\EventDispatcher\Event;
use KairosProject\ApiController\Event\ProcessEventInterface;

/**
 * Normalizing event
 *
 * This class is the default NormalizingEventInterface implementation
 *
 * @category Api_Normalizer_Event
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class NormalizingEvent extends Event implements NormalizingEventInterface
{
    /**
     * Process event
     *
     * The original processed event
     *
     * @var ProcessEventInterface
     */
    private $processEvent;

    /**
     * Data
     *
     * The normalized data
     *
     * @var mixed
     */
    private $data;

    /**
     * Context
     *
     * The normalization context
     *
     * @var array
     */
    private $context = [];

    /**
     * Construct
     *
     * The default NormalizingEvent constructor store the original process event. The context and data are optionals
     * but can be setted by this way.
     *
     * @param ProcessEventInterface $processEvent The original processed event
     * @param array                 $context      The normalization context
     * @param mixed                 $data         The data to be normalized or already normalized
     */
    public function __construct(
        ProcessEventInterface $processEvent,
        array $context = [],
        $data = null
    ) {
        $this->processEvent = $processEvent;
        $this->setContext($context);
        $this->setData($data);
    }

    /**
     * Get processed event
     *
     * Return the original processed event
     *
     * @return ProcessEventInterface
     */
    public function getProcessEvent() : ProcessEventInterface
    {
        return $this->processEvent;
    }

    /**
     * Set data
     *
     * Set the data to be normalized or already normalized
     *
     * @param mixed $data The data
     *
     * @return $this
     */
    public function setData($data) : NormalizingEventInterface
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set context
     *
     * Set the normalization context
     *
     * @param array $context The normalization context
     *
     * @return $this
     */
    public function setContext(array $context) : NormalizingEventInterface
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Get context
     *
     * Return the normalization context
     *
     * @return array
     */
    public function getContext() : array
    {
        return $this->context;
    }

    /**
     * Get data
     *
     * Return the data, before or after normalization, depending of the event
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
