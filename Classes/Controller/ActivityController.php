<?php

declare(strict_types=1);

namespace Generator\Generator\Controller;


use Generator\Generator\Domain\Model\Activity;
use Generator\Generator\Domain\Repository\TraineeRepository;
use Generator\Generator\Domain\Repository\ActivityRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use DateTime;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * This file is part of the "Generator" Extension for TYPO3 CMS.
 *
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Eugene Ihde <eugene.ihde@brandung.de>, brandung GmbH
 */

/**
 * ActivityController
 */
class ActivityController extends ActionController
{
    /**
     * activityRepository
     *
     * @var ActivityRepository|null
     */
    protected ?ActivityRepository $activityRepository = null;

    /**
     * @var TraineeRepository|null
     */
    protected ?TraineeRepository $traineeRepository = null;

    /**
     * @var int
     */
    protected int $loggedInUserId = 0;

    /**
     * @var array
     */
    protected array $filteredActivities = [];

    /**
     * @param ActivityRepository $activityRepository
     * @return void
     */
    public function injectActivityRepository(ActivityRepository $activityRepository): void
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * @param TraineeRepository $traineeRepository
     * @return void
     */
    public function injectTraineeRepository(TraineeRepository $traineeRepository): void
    {
        $this->traineeRepository = $traineeRepository;
    }

    public function __construct()
    {
        $this->loggedInUserId = $GLOBALS['TSFE']->fe_user->user['uid'];
    }

    /**
     * action list
     *
     * @return ResponseInterface
     * @throws InvalidQueryException
     */
    public function listAction(): ResponseInterface
    {
        try {
            $data = $this->request->getArgument('datePickerValue');
        } catch (NoSuchArgumentException $e) {
            $data = date('Y-m-d');
        }

        $activities = $this->activityRepository->findByTraineeAndDate($data);

        foreach ($activities as $activity)
            $this->filteredActivities[] = $activity;

        if ($this->filteredActivities == [])
            $this->addFlashMessage(
                '',
                'Für diese Woche sind keine Aktivitäten vorhanden',
                AbstractMessage::WARNING
            );
        $this->view->assign('activities', $this->filteredActivities);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param Activity $activity
     * @return ResponseInterface
     */
    public function showAction(Activity $activity): ResponseInterface
    {
//        $this->generateAction();
        $this->view->assign('activity', $activity);
        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return ResponseInterface
     */
    public function newAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * @throws NoSuchArgumentException
     */
    public function initializeCreateAction()
    {
        $propertyMappingConfiguration = $this->arguments->getArgument('newActivity')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty('date')->setTypeConverterOption(
            'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
            \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'Y-m-d'
        );

        $propertyMappingConfiguration->forProperty('date')->allowAllProperties();
        $propertyMappingConfiguration->forProperty('date')->allowCreationForSubProperty('date');
        $propertyMappingConfiguration->forProperty('date')->forProperty('date')->allowAllProperties();
    }

    /**
     * action create
     *
     * @param Activity $newActivity
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function createAction(Activity $newActivity)
    {
        $newActivity->setTrainee($this->traineeRepository->findByUid($this->loggedInUserId));
        $this->activityRepository->add($newActivity);
        $this->addFlashMessage('Aktivität wurde erstellt', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param Activity $activity
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("activity")
     * @return ResponseInterface
     */
    public function editAction(Activity $activity): ResponseInterface
    {
        $this->view->assign('activity', $activity);
        return $this->htmlResponse();
    }

    /**
     * @throws NoSuchArgumentException
     */
    public function initializeUpdateAction()
    {
        $propertyMappingConfiguration = $this->arguments->getArgument('activity')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty('date')->setTypeConverterOption(
            'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
            \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'Y-m-d'
        );

        $propertyMappingConfiguration->forProperty('date')->allowAllProperties();
        $propertyMappingConfiguration->forProperty('date')->allowCreationForSubProperty('date');
        $propertyMappingConfiguration->forProperty('date')->forProperty('date')->allowAllProperties();
    }

    /**
     * action update
     *
     * @param Activity $activity
     * @throws StopActionException
     */
    public function updateAction(Activity $activity)
    {
        try {
            $this->activityRepository->update($activity);
        } catch (IllegalObjectTypeException|UnknownObjectException $e) {
        }
        $this->addFlashMessage('Aktivität wurde aktualisiert', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param Activity $activity
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     */
    public function deleteAction(Activity $activity)
    {
        $this->activityRepository->remove($activity);
        $this->addFlashMessage('Aktivität wurde gelöscht', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->redirect('list');
    }

    /**
     * action generate
     *
     * @param int|null $calendarWeek
     * @return void
     * @throws InvalidQueryException
     *
     * WIP
     */
    public function generateAction(int $calendarWeek = null): void
    {
        $activities = $this->activityRepository->findByTraineeAndCalendarWeekOrderedByCreationDateDescending();

        $selectedCalendarWeek = (int) ($calendarWeek ?? date('W'));

        foreach ($activities as $activity) {
            if ($activity->getDate()->format('W') == $selectedCalendarWeek) {
                $this->filteredActivities[] = [
                    $activity->getDate(),
                    $activity->getDesignation(),
                    $activity->getDescription(),
                    $activity->getCategory()
                ];
            }
        }

        printf(
            '<pre>%s</pre>',
            print_r($this->filteredActivities, true)
        );
    }

    /**
     * @param $year
     * @param $week
     * @return array
     */
    protected function convertCalendarWeekToStartAndEndDate($year, $week): array
    {
//      https://stackoverflow.com/questions/4861384/php-get-start-and-end-date-of-a-week-by-weeknumber

        $dto = new DateTime();
        $dto->setISODate((int) $year, (int) $week);
        $dayData['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $dayData['week_end'] = $dto->format('Y-m-d');

        return $dayData;
    }
}
