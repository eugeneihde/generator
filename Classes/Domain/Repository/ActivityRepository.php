<?php

declare(strict_types=1);

namespace Generator\Generator\Domain\Repository;


use DateTime;
use Generator\Generator\Domain\Model\Trainee;
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
 * (c) 2023 Eugene Ihde <eugeneihde.business@gmail.com>
 */

/**
 * The repository for Activities
 */
class ActivityRepository extends Repository
{
    /**
     * @param Trainee $trainee
     * @param DateTime $date
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByTraineeAndCalendarWeekOrderedByCreationDateAscending(Trainee $trainee, DateTime $date): QueryResultInterface
    {
        $startOfWeek = clone $date;
        $startOfWeek->modify('monday');
        $endOfWeek = clone $date;
        $endOfWeek->modify('monday + 1 week');

        $query = $this->createQuery();
        $query
            ->matching(
                $query->logicalAnd(
                    $query->equals('trainee', $trainee),
                    $query->greaterThanOrEqual('date', $startOfWeek),
                    $query->lessThan('date', $endOfWeek)
                )
            )
            ->setOrderings([
                'date' => QueryInterface::ORDER_ASCENDING
            ]);

        return $query->execute();
    }

    /**
     * @param Trainee $trainee
     * @param DateTime $date
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByTraineeAndDate(Trainee $trainee, DateTime $date): QueryResultInterface
    {
        $dayStart = clone $date;
        $dayStart->modify('midnight');
        $dayEnd = clone $date;
        $dayEnd->modify('tomorrow');

        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('trainee', $trainee),
                $query->greaterThanOrEqual('date', $dayStart),
                $query->lessThan('date', $dayEnd)
            )
        );

        return $query->execute();
    }
}
