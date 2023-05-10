<?php

declare(strict_types=1);

namespace Generator\Generator\Controller;


use Generator\Generator\Domain\Model\Activity;
use Generator\Generator\Domain\Repository\TraineeRepository;
use Generator\Generator\Domain\Repository\ActivityRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

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
    private int $loggedInUserId = 0;

    /**
     * @var array
     */
    private array $filteredActivities = [];

    /**
     * @param ActivityRepository $activityRepository
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
     */
    public function listAction(): ResponseInterface
    {
        $activities = $this->activityRepository->findAll();

//        $selectedCalendarWeek = 36;
//        foreach ($activities as $activity) {
//            if (
//                $activity->getDate()->format('W') == $selectedCalendarWeek && 
//                $activity->getTrainee()->getUid() == $this->loggedInUserId
//            ) {
//                $filteredActivities[] = $activity;
//            }
//        }

        foreach ($activities as $activity) {
            if ($activity->getTrainee()->getUid() == $this->loggedInUserId) {
                $this->filteredActivities[] = $activity;
            }
        }

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

        $propertyMappingConfiguration->forProperty('*')->allowAllProperties();
        $propertyMappingConfiguration->forProperty('*')->allowCreationForSubProperty('*');
        $propertyMappingConfiguration->forProperty('*')->forProperty('*')->allowAllProperties();
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
        $this->redirect('list');
    }
}
