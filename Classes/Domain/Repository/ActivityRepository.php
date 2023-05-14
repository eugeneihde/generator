<?php

declare(strict_types=1);

namespace Generator\Generator\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * This file is part of the "Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Eugene Ihde <eugene.ihde@brandung.de>, brandung GmbH
 */

/**
 * The repository for Activities
 */
class ActivityRepository extends Repository
{
    /**
     * @param int $calendarWeek
     * @return object[]|QueryResultInterface
     */
    public function findByTraineeOrderedByCreationDateDescending(int $calendarWeek): QueryResultInterface|array
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->equals('trainee', $GLOBALS['TSFE']->fe_user->user['uid']),
//                @todo: get calendar week from DateTime and check
//                $query->equals('date', $calendarWeek)
            )
        );

        $query->setOrderings([
            'date' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
        ]);

        return $query->execute();
    }

    public function findByTraineeAndCalendarWeekOrderedByCreationDateAscending()
    {
        $query = $this->createQuery();
    }
}
