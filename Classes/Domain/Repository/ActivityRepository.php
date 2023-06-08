<?php

declare(strict_types=1);

namespace Generator\Generator\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
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
     * @param int $userId
     * @param string $weekStart
     * @param string $weekEnd
     * @return object[]|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByTraineeAndCalendarWeekOrderedByCreationDateAscending(int $userId, string $weekStart, string $weekEnd): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query
            ->matching(
                $query->logicalAnd(
                    $query->equals('trainee', $userId),
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
     * @param int $userId
     * @return QueryResultInterface|array
     */
    public function findByTraineeAndDate(int $userId, string $date): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('trainee', $userId),
                $query->equals('date', $date . ' 00:00:00')
            )
        );

        return $query->execute();
    }
}
