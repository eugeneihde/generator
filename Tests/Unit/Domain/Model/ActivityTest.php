<?php

declare(strict_types=1);

namespace Generator\Generator\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author Eugene Ihde <eugene.ihde@brandung.de>
 */
class ActivityTest extends UnitTestCase
{
    /**
     * @var \Generator\Generator\Domain\Model\Activity|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \Generator\Generator\Domain\Model\Activity::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getDateReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getDate()
        );
    }

    /**
     * @test
     */
    public function setDateForDateTimeSetsDate(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDate($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('date'));
    }

    /**
     * @test
     */
    public function getDesignationReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getDesignation()
        );
    }

    /**
     * @test
     */
    public function setDesignationForStringSetsDesignation(): void
    {
        $this->subject->setDesignation('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('designation'));
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription(): void
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('description'));
    }

    /**
     * @test
     */
    public function getCategoryReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getCategory()
        );
    }

    /**
     * @test
     */
    public function setCategoryForStringSetsCategory(): void
    {
        $this->subject->setCategory('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('category'));
    }

    /**
     * @test
     */
    public function getTraineeReturnsInitialValueForTrainee(): void
    {
        self::assertEquals(
            null,
            $this->subject->getTrainee()
        );
    }

    /**
     * @test
     */
    public function setTraineeForTraineeSetsTrainee(): void
    {
        $traineeFixture = new \Generator\Generator\Domain\Model\Trainee();
        $this->subject->setTrainee($traineeFixture);

        self::assertEquals($traineeFixture, $this->subject->_get('trainee'));
    }
}
