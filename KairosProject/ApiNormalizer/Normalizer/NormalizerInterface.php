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

/**
 * Normalizer interface
 *
 * This interface define the basic methods of the Normalizer.
 *
 * @category Api_Normalizer_Normalizer
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
interface NormalizerInterface
{
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
    public function normalize(
        ProcessEventInterface $event,
        string $eventName,
        EventDispatcherInterface $dispatcher
    ) : void;
}
