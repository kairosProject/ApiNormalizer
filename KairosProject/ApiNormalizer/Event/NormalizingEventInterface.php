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

use KairosProject\ApiController\Event\ProcessEventInterface;

/**
 * Normalizing event interface
 *
 * This interface define the basic methods of the NormalizingEvent.
 *
 * @category Api_Normalizer_Event
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
interface NormalizingEventInterface
{
    /**
     * Get data
     *
     * Return the data, before or after normalization, depending of the event
     *
     * @return mixed
     */
    public function getData();

    /**
     * Set data
     *
     * Set the data to be normalized or already normalized
     *
     * @param mixed $data The data
     *
     * @return $this
     */
    public function setData($data) : NormalizingEventInterface;

    /**
     * Get processed event
     *
     * Return the original processed event
     *
     * @return ProcessEventInterface
     */
    public function getProcessEvent() : ProcessEventInterface;

    /**
     * Get context
     *
     * Return the normalization context
     *
     * @return array
     */
    public function getContext() : array;

    /**
     * Set context
     *
     * Set the normalization context
     *
     * @param array $context The normalization context
     *
     * @return $this
     */
    public function setContext(array $context) : NormalizingEventInterface;
}
