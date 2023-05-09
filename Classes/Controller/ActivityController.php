<?php

declare(strict_types=1);

namespace Generator\Generator\Controller;


use Generator\Generator\Domain\Model\Activity;
use Generator\Generator\Domain\Repository\TraineeRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Generator\Generator\Domain\Repository\ActivityRepository;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\FrontendLogin\Domain\Repository\FrontendUserRepository;

/**
 * This file is part of the "Generator" Extension for TYPO3 CMS.
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
     * @param ActivityRepository $activityRepository
     */
    public function injectActivityRepository(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
        
    }
    
    public function injectTraineeRepository(TraineeRepository $traineeRepository)
    {
        $this->traineeRepository = $traineeRepository;
    }

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        DebuggerUtility::var_dump($GLOBALS['TSFE']->fe_user->user['uid']);
        $activities = $this->activityRepository->findAll();
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
        $this->traineeRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
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
     * action create
     *
     * @param Activity $newActivity
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function createAction(Activity $newActivity)
    {
//        DebuggerUtility::var_dump($this->traineeRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']));
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
