<?php

declare(strict_types=1);

namespace Generator\Generator\Domain\Model;


use DateTime;

/**
 * This file is part of the "Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 Eugene Ihde <eugene.ihde@brandung.de>, brandung GmbH
 */

/**
 * Activity
 */
class Activity extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * date
     *
     * @var DateTime|null
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected ?DateTime $date = null;

    /**
     * designation
     *
     * @var string|null
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected ?string $designation = null;

    /**
     * description
     *
     * @var string|null
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected ?string $description = null;

    /**
     * category
     *
     * @var string|null
     */
    protected ?string $category = null;

    /**
     * trainee
     *
     * @var Trainee|null
     */
    protected ?Trainee $trainee = null;

    /**
     * Returns the date
     *
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * Sets the date
     *
     * @param DateTime $date
     * @return void
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * Returns the designation
     *
     * @return string|null
     */
    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    /**
     * Sets the designation
     *
     * @param string $designation
     * @return void
     */
    public function setDesignation(string $designation): void
    {
        $this->designation = $designation;
    }

    /**
     * Returns the description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns the category
     *
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * Sets the category
     *
     * @param string $category
     * @return void
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * Returns the trainee
     *
     * @return Trainee|null
     */
    public function getTrainee(): ?Trainee
    {
        return $this->trainee;
    }

    /**
     * Sets the trainee
     *
     * @param Trainee $trainee
     * @return void
     */
    public function setTrainee(Trainee $trainee): void
    {
        $this->trainee = $trainee;
    }
}
