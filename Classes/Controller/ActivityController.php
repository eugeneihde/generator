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
use Generator\Generator\Domain\Model\Trainee;
use Generator\Generator\Domain\Repository\TraineeRepository;
use Generator\Generator\Domain\Repository\ActivityRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use DateTime;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;

/**
 * This Class is responsible for controlling requests from the activitymanager frontend plugin.
 */
class ActivityController extends ActionController
{
    public const DATE_FORMAT = 'd.m.Y';

    /**
     * @var int
     */
    protected int $loggedInUserId = 0;

    public function __construct(
        protected ActivityRepository $activityRepository, 
        protected TraineeRepository $traineeRepository
    )
    {

    }

    /**
     * @return void
     * @throws NoSuchArgumentException
     */
    public function initializeListAction(): void
    {
        if (!$this->request->hasArgument('date')) {
            $this->request->setArgument('date', new DateTime());
        }

        $propertyMappingConfiguration = $this->arguments->getArgument('date')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->setTypeConverterOption(
            DateTimeConverter::class,
            DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'Y-m-d'
        );
    }

    /**
     * action list
     *
     * @param DateTime $date
     * @return ResponseInterface
     * @throws PropagateResponseException|AspectNotFoundException
     * @throws InvalidQueryException
     */
    public function listAction(DateTime $date): ResponseInterface
    {
        $trainee = $this->getTrainee();

        if ($trainee === null) {
            $this->throwStatus(403);
        }

        $activities = $this->activityRepository->findByTraineeAndDate($trainee, $date);

        if ($activities->count() === 0) {
            $this->addFlashMessage(
                '',
                'Für diesen Tag sind keine Aktivitäten vorhanden: ' . $date->format(self::DATE_FORMAT),
                AbstractMessage::NOTICE
            );
        }

        $this->view->assign('date', $date);
        $this->view->assign('dateFormat', self::DATE_FORMAT);
        $this->view->assign('activities', $activities);
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
        $this->view->assign('activity', $activity);
        return $this->htmlResponse();
    }

    /**
     * @throws NoSuchArgumentException
     */
    public function initializeNewAction()
    {
        $this->convertDateForActivityWithTypeConverter();
    }

    /**
     * action new
     *
     * @param Activity $activity
     * @return ResponseInterface
     * @IgnoreValidation("activity")
     */
    public function newAction(Activity $activity): ResponseInterface
    {
        $this->view->assign('activity', $activity);
        return $this->htmlResponse();
    }

    /**
     * @throws NoSuchArgumentException
     * @throws AspectNotFoundException
     * @throws PropagateResponseException
     */
    public function initializeCreateAction()
    {
        $this->convertDateForActivityWithTypeConverter(true);

        $trainee = $this->getTrainee();
        if ($trainee === null) {
            $this->throwStatus(403);
        }

        $activity = $this->request->getArgument('activity');
        $activity['trainee'] = $trainee->getUid();
        $this->request->setArgument('activity', $activity);
    }

    /**
     * action create
     *
     * @param Activity $activity
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function createAction(Activity $activity)
    {
        $this->activityRepository->add($activity);
        $this->addFlashMessage('Aktivität wurde erstellt');
        $this->redirect(
            'list',
            null,
            null,
            ['date' => $activity->getDate()->format('Y-m-d')]
        );
    }

    /**
     * @throws NoSuchArgumentException
     */
    public function initializeEditAction()
    {
        $this->convertDateForActivityWithTypeConverter();
    }

    /**
     * action edit
     *
     * @param Activity $activity
     * @IgnoreValidation("activity")
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
        $this->convertDateForActivityWithTypeConverter();
    }

    /**
     * action update
     *
     * @param Activity $activity
     * @throws StopActionException
     */
    public function updateAction(Activity $activity)
    {
        $this->activityRepository->update($activity);
        $this->addFlashMessage('Aktivität wurde aktualisiert');
        $this->redirect(
            'list',
            null,
            null,
            ['date' => $activity->getDate()->format('Y-m-d')]
        );

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
        $this->addFlashMessage('Aktivität wurde gelöscht');
        $this->redirect(
            'list',
            null,
            null,
            ['date' => $activity->getDate()->format('Y-m-d')]
        );
    }

    /**
     * @throws NoSuchArgumentException
     */
    public function initializeGenerateAction()
    {
        $propertyMappingConfiguration = $this->arguments->getArgument('date')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->setTypeConverterOption(
            DateTimeConverter::class,
            DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'Y-m-d'
        );
    }

    /**
     * action generate
     *
     * @param DateTime $date
     * @return ResponseInterface
     * @throws InvalidQueryException
     * @throws Exception
     */
    public function generateAction(DateTime $date): ResponseInterface
    {
        $trainee = $this->getTrainee();
        if ($trainee === null) {
            $this->throwStatus(403);
        }

        $year = $date->format('Y');
        $calendarWeek = $date->format('W');

        $activities = $this->activityRepository->findByTraineeAndCalendarWeekOrderedByCreationDateAscending($trainee, $date);

        if ($activities->count() === 0) {
            $this->addFlashMessage(
                'Für diese Kalenderwoche sind keine Aktivitäten vorhanden!',
                '',
                AbstractMessage::WARNING
            );
        }

        $this->view->assign('activities', $activities);
        $this->view->assign('year', $year);
        $this->view->assign('calendarWeek', $calendarWeek);
        $this->view->assign('date', $date);
        return $this->htmlResponse();
    }

    /**
     * @return Trainee|null
     * @throws AspectNotFoundException
     */
    protected function getTrainee(): ?Trainee
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $userId = $context->getPropertyFromAspect('frontend.user', 'id');

        if ($userId === 0) {
            return null;
        }

        return $this->traineeRepository->findByUid($userId);
    }

    /**
     * @throws NoSuchArgumentException
     */
    protected function convertDateForActivityWithTypeConverter(bool $withTrainee = false)
    {
        $propertyMappingConfiguration = $this->arguments->getArgument('activity')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty('date')->setTypeConverterOption(
            DateTimeConverter::class,
            DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'Y-m-d'
        );

        if ($withTrainee) {
            $propertyMappingConfiguration->allowProperties('trainee');
        }
    }
}
