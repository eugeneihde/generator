<?php

declare(strict_types=1);

namespace Generator\Generator\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Test case
 *
 * @author Eugene Ihde <eugene.ihde@brandung.de>
 */
class ActivityControllerTest extends UnitTestCase
{
    /**
     * @var \Generator\Generator\Controller\ActivityController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\Generator\Generator\Controller\ActivityController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllActivitiesFromRepositoryAndAssignsThemToView(): void
    {
        $allActivities = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $activityRepository = $this->getMockBuilder(\Generator\Generator\Domain\Repository\ActivityRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $activityRepository->expects(self::once())->method('findAll')->will(self::returnValue($allActivities));
        $this->subject->_set('activityRepository', $activityRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('activities', $allActivities);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenActivityToView(): void
    {
        $activity = new \Generator\Generator\Domain\Model\Activity();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('activity', $activity);

        $this->subject->showAction($activity);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenActivityToActivityRepository(): void
    {
        $activity = new \Generator\Generator\Domain\Model\Activity();

        $activityRepository = $this->getMockBuilder(\Generator\Generator\Domain\Repository\ActivityRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $activityRepository->expects(self::once())->method('add')->with($activity);
        $this->subject->_set('activityRepository', $activityRepository);

        $this->subject->createAction($activity);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenActivityToView(): void
    {
        $activity = new \Generator\Generator\Domain\Model\Activity();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('activity', $activity);

        $this->subject->editAction($activity);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenActivityInActivityRepository(): void
    {
        $activity = new \Generator\Generator\Domain\Model\Activity();

        $activityRepository = $this->getMockBuilder(\Generator\Generator\Domain\Repository\ActivityRepository::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $activityRepository->expects(self::once())->method('update')->with($activity);
        $this->subject->_set('activityRepository', $activityRepository);

        $this->subject->updateAction($activity);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenActivityFromActivityRepository(): void
    {
        $activity = new \Generator\Generator\Domain\Model\Activity();

        $activityRepository = $this->getMockBuilder(\Generator\Generator\Domain\Repository\ActivityRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $activityRepository->expects(self::once())->method('remove')->with($activity);
        $this->subject->_set('activityRepository', $activityRepository);

        $this->subject->deleteAction($activity);
    }
}
