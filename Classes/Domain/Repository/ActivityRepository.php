<?php

declare(strict_types=1);

namespace Generator\Generator\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * @param string $weekStart
     * @param string $weekEnd
     * @return object[]|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByTraineeAndCalendarWeekOrderedByCreationDateDescending(string $weekStart, string $weekEnd): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('trainee', $GLOBALS['TSFE']->fe_user->user['uid']),
//                @todo: get calendar week from DateTime and check
                $query->greaterThanOrEqual('date', $weekStart . ' 00:00:00'),
                $query->lessThanOrEqual('date', $weekEnd . ' 23:59:59')
            )
        )
        ->setOrderings([
            'date' => QueryInterface::ORDER_ASCENDING
        ]);

        return $query->execute();
    }

    /**
     * @param string $date
     * @return QueryResultInterface|array
     */
    public function findByTraineeAndDate(string $date): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('trainee', $GLOBALS['TSFE']->fe_user->user['uid']),
                $query->equals('date', $date . ' 00:00:00')
            )
        );

        return $query->execute();
    }

    public function findByTraineeAndCalendarWeekOrderedByCreationDateAscending()
    {
        $query = $this->createQuery();
    }
}
