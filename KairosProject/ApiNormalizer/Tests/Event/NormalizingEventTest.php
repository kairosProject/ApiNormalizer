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
 * @category Api_Normalizer_Event_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiNormalizer\Tests\Event;

use KairosProject\Tests\AbstractTestClass;
use KairosProject\ApiNormalizer\Event\NormalizingEvent;
use KairosProject\ApiController\Event\ProcessEventInterface;

/**
 * NormalizingEvent test
 *
 * This class is used to validate the NormalizingEvent instance.
 *
 * @category Api_Normalizer_Event_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class NormalizingEventTest extends AbstractTestClass
{
    /**
     * Test constructor.
     *
     * This method validate the KairosProject\ApiNormalizer\Event\NormalizingEvent::_construct method.
     *
     * @return void
     */
    public function testConstructor() : void
    {
        $this->assertConstructor(
            [
                'same:processEvent' => $this->createMock(ProcessEventInterface::class)
            ],
            [
                'context' => [],
                'data' => null
            ]
        );

        $this->assertConstructor(
            [
                'same:processEvent' => $this->createMock(ProcessEventInterface::class),
                'same:context' => [
                    $this->createMock(\stdClass::class)
                ]
            ],
            [
                'data' => null
            ]
        );

        $this->assertConstructor(
            [
                'same:processEvent' => $this->createMock(ProcessEventInterface::class),
                'same:context' => [
                    $this->createMock(\stdClass::class)
                ],
                'same:data' => $this->createMock(\stdClass::class)
            ]
        );
    }

    /**
     * Test getProcessEvent.
     *
     * This method validate the KairosProject\ApiNormalizer\Event\NormalizingEvent::getProcessEvent method.
     *
     * @return void
     */
    public function testGetProcessEvent()
    {
        $this->assertIsSimpleGetter(
            'processEvent',
            'getProcessEvent',
            $this->createMock(ProcessEventInterface::class)
        );
    }

    /**
     * Test data accessor.
     *
     * This method validate the KairosProject\ApiNormalizer\Event\NormalizingEvent::getData and
     * KairosProject\ApiNormalizer\Event\NormalizingEvent::setData method.
     *
     * @return void
     */
    public function testDataAccessor()
    {
        $this->assertHasSimpleAccessor('data', $this->createMock(\stdClass::class));
    }

    /**
     * Test context accessor.
     *
     * This method validate the KairosProject\ApiNormalizer\Event\NormalizingEvent::getContext and
     * KairosProject\ApiNormalizer\Event\NormalizingEvent::setContext method.
     *
     * @return void
     */
    public function testContextAccessor()
    {
        $this->assertHasSimpleAccessor('context', [$this->createMock(\stdClass::class)]);
    }

    /**
     * Get tested class
     *
     * Return the tested class name
     *
     * @return string
     */
    protected function getTestedClass() : string
    {
        return NormalizingEvent::class;
    }
}
