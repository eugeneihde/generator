<?php
declare(strict_types=1);

/**
 * This file is part of the "Generator" Extension for TYPO3 CMS.
 *
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Eugene Ihde <eugene.ihde@brandung.de>, brandung GmbH
 */

namespace Generator\Generator\Controller;


use Exception;
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

/**
 * This Class is responsible for controlling requests from the activitymanager frontend plugin.
 */
class ActivityController extends ActionController
{
    /**
     * Activity Repository
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
     * @param DateTime|null $date
     * @return ResponseInterface
     */
    public function listAction(DateTime $date = null): ResponseInterface
    {
        try {
            $date = $this->request->getArgument('datePickerValue');
            if ($date == '') {
                $date = date('Y-m-d');
            }
        } catch (NoSuchArgumentException $exception) {
            $date = date('Y-m-d');
        }

        $activities = $this->activityRepository->findByTraineeAndDate($this->loggedInUserId, $date);

        foreach ($activities as $activity) {
            $this->filteredActivities[] = $activity;
        }

        if ($this->filteredActivities == []) {
            $this->addFlashMessage('', 'Für diesen Tag sind keine Aktivitäten vorhanden: ' . $date, AbstractMessage::NOTICE);
        }
        $this->view->assign('selectedDate', $date);
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
     * @param string $selectedDate
     * @return ResponseInterface
     */
    public function newAction(string $selectedDate = ''): ResponseInterface
    {
        $this->view->assign('selectedDate', $selectedDate);
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
        } catch (IllegalObjectTypeException|UnknownObjectException $exception) {
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
     * @param string $selectedDate
     * @return ResponseInterface
     * @throws InvalidQueryException
     * @throws Exception
     */
    public function generateAction(string $selectedDate = ''): ResponseInterface
    {
        $year = substr($selectedDate, 0, 4);
        $calendarWeek = $this->getCalendarWeekDataFromDate($selectedDate);
        $values = $this->getCalendarWeekInformation($year, $calendarWeek);

        $activities = $this->activityRepository->findByTraineeAndCalendarWeekOrderedByCreationDateAscending(
            $this->loggedInUserId,
            $values['week_start'],
            $values['week_end']
        );

        foreach ($activities as $activity) {
            $this->filteredActivities[] = $activity;
        }

        if ($this->filteredActivities == []) {
            $this->addFlashMessage('Für diese Kalenderwoche sind keine Aktivitäten vorhanden!', '', AbstractMessage::WARNING);
        }

        $this->view->assign('activities', $this->filteredActivities);
        $this->view->assign('year', $year);
        $this->view->assign('calendarWeek', $calendarWeek);
        $this->view->assign('selectedDate', $selectedDate);
        return $this->htmlResponse();
    }

    /**
     * @param $date
     * @return string
     * @throws Exception
     */
    protected function getCalendarWeekDataFromDate($date): string
    {
        $dto = new DateTime($date);
        return $dto->format("W");
    }

    /**
     * @param $year
     * @param $week
     * @return array
     */
    protected function getCalendarWeekInformation($year, $week): array
    {
        $dto = new DateTime();
        $dto->setISODate((int) $year, (int) $week);
        $dayData['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $dayData['week_end'] = $dto->format('Y-m-d');

        return $dayData;
    }
}
