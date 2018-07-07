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
 * @category Api_Normalizer_Normalizer_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiNormalizer\Tests\Normalizer;

use KairosProject\Tests\AbstractTestClass;
use KairosProject\ApiNormalizer\Normalizer\Normalizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use KairosProject\ApiLoader\Loader\AbstractApiLoader;
use KairosProject\ApiController\Event\ProcessEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use KairosProject\ApiNormalizer\Event\NormalizingEventInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Normalizer test
 *
 * This class is used to validate the Normalizer instance.
 *
 * @category Api_Normalizer_Normalizer_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class NormalizerTest extends AbstractTestClass
{
    /**
     * Data
     *
     * The data to normalize
     *
     * @var MockObject
     */
    private $data;

    /**
     * Normalized data
     *
     * The normalized data
     *
     * @var MockObject
     */
    private $normalizedData;

    /**
     * Original event
     *
     * The original process event
     *
     * @var MockObject
     */
    private $originalEvent;

    /**
     * Context
     *
     * The normalizer context
     *
     * @var MockObject
     */
    private $context;

    /**
     * Setup
     *
     * This method is called before each test.
     *
     * @see    \PHPUnit\Framework\TestCase::setUp()
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->context = [$this->createMock(\stdClass::class)];
        $this->data = $this->createMock(\stdClass::class);
        $this->normalizedData = $this->createMock(\stdClass::class);
        $this->originalEvent = $this->createMock(ProcessEventInterface::class);
    }

    /**
     * Test constructor.
     *
     * This method validate the KairosProject\ApiNormalizer\Normalizer\Normalizer::_construct method.
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertConstructor(
            [
                'same:logger' => $this->createMock(LoggerInterface::class),
                'same:normalizer' => $this->createMock(NormalizerInterface::class)
            ],
            [
                'context' => [],
                'inputParameter' => AbstractApiLoader::EVENT_KEY_STORAGE,
                'outputParameter' => Normalizer::NORMALIZED_DATA,
                'beforeNormalizeEvent' => Normalizer::EVENT_BEFORE,
                'afterNormalizeEvent' => Normalizer::EVENT_AFTER
            ]
        );

        $this->assertConstructor(
            [
                'same:logger' => $this->createMock(LoggerInterface::class),
                'same:normalizer' => $this->createMock(NormalizerInterface::class),
                'same:context' => [$this->createMock(\stdClass::class)],
                'inputParameter' => 'loaderKey',
                'outputParameter' => 'normalizerKey',
                'beforeNormalizeEvent' => 'firstEvent',
                'afterNormalizeEvent' => 'lastEvent'
            ]
        );
    }

    /**
     * Test normalize.
     *
     * This method validate the KairosProject\ApiNormalizer\Normalizer\Normalizer::normalize method.
     *
     * @return void
     */
    public function testNormalize()
    {
        $normalizer = $this->createMock(NormalizerInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->getInvocationBuilder($this->originalEvent, $this->once(), 'hasParameter')
            ->with($this->equalTo(AbstractApiLoader::EVENT_KEY_STORAGE))
            ->willReturn(true);
        $this->getInvocationBuilder($this->originalEvent, $this->once(), 'getParameter')
            ->with($this->equalTo(AbstractApiLoader::EVENT_KEY_STORAGE))
            ->willReturn($this->data);

        $this->getInvocationBuilder($dispatcher, $this->exactly(2), 'dispatch')
            ->withConsecutive(
                $this->callback(\Closure::fromCallable([$this, 'validateBeforeNormalizeEvent'])),
                $this->callback(\Closure::fromCallable([$this, 'validateAfterNormalizeEvent']))
            );

        $this->getInvocationBuilder($normalizer, $this->once(), 'normalize')
            ->with(
                $this->identicalTo($this->data),
                $this->isNull(),
                $this->identicalTo($this->context)
            )->willReturn($this->normalizedData);

        $instance = $this->getInstance(
            [
                'logger' => $this->createMock(LoggerInterface::class),
                'normalizer' => $normalizer,
                'context' => $this->context
            ]
        );

        $instance->normalize($this->originalEvent, '', $dispatcher);
    }

    /**
     * Test normalize with error.
     *
     * This method validate the KairosProject\ApiNormalizer\Normalizer\Normalizer::normalize method in case of missing
     * input parameter.
     *
     * @return void
     */
    public function testNormalizeError()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The expected parameter "%s" does not exist in the process event',
                AbstractApiLoader::EVENT_KEY_STORAGE
            )
        );

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->getInvocationBuilder($this->originalEvent, $this->once(), 'hasParameter')
            ->with($this->equalTo(AbstractApiLoader::EVENT_KEY_STORAGE))
            ->willReturn(false);

        $instance = $this->getInstance(
            [
                'logger' => $this->createMock(LoggerInterface::class)
            ]
        );
        $instance->normalize($this->originalEvent, '', $dispatcher);
    }

    /**
     * Validate before normalize event
     *
     * Validate the before normalization dispatching event parameters
     *
     * @param string                    $eventName The dispatched event name
     * @param NormalizingEventInterface $event     The dispatched event
     *
     * @return boolean
     */
    public function validateBeforeNormalizeEvent(string $eventName, NormalizingEventInterface $event)
    {
        $this->assertEquals(Normalizer::EVENT_BEFORE, $eventName);
        $this->assertSame($this->context, $event->getContext());
        $this->assertSame($this->data, $event->getData());
        $this->assertSame($this->originalEvent, $event->getProcessEvent());

        return true;
    }

    /**
     * Validate after normalize event
     *
     * Validate the after normalization dispatching event parameters
     *
     * @param string                    $eventName The dispatched event name
     * @param NormalizingEventInterface $event     The dispatched event
     *
     * @return boolean
     */
    public function validateAfterNormalizeEvent(string $eventName, NormalizingEventInterface $event)
    {
        $this->assertEquals(Normalizer::EVENT_AFTER, $eventName);
        $this->assertSame($this->context, $event->getContext());
        $this->assertSame($this->normalizedData, $event->getData());
        $this->assertSame($this->originalEvent, $event->getProcessEvent());

        return true;
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
        return Normalizer::class;
    }
}
