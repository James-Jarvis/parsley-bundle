<?php

namespace JBen87\ParsleyBundle\Tests\Constraint\Factory;

use JBen87\ParsleyBundle\Constraint\Constraint;
use JBen87\ParsleyBundle\Constraint\Constraints as ParsleyAssert;
use JBen87\ParsleyBundle\Constraint\Factory\FactoryInterface;
use JBen87\ParsleyBundle\Constraint\Factory\MaxLengthFactory;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class MaxLengthFactoryTest extends FactoryTestCase
{
    private const LIMIT = 10;
    private const ORIGINAL_MESSAGE = 'This value is too long. It should have {{ limit }} character or less.'
        .'|This value is too long. It should have {{ limit }} characters or less.'
    ;
    private const TRANSLATED_MESSAGE = 'This value is too long. It should have '.self::LIMIT.' characters or less.';

    /**
     * @inheritdoc
     */
    protected function setUpCreate(): void
    {
        $this->translator
            ->expects($this->once())
            ->method('transChoice')
            ->with(static::ORIGINAL_MESSAGE, static::LIMIT, ['{{ limit }}' => static::LIMIT])
            ->willReturn(static::TRANSLATED_MESSAGE)
        ;
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedConstraint(): Constraint
    {
        return new ParsleyAssert\MaxLength(['max' => static::LIMIT, 'message' => static::TRANSLATED_MESSAGE]);
    }

    /**
     * @inheritdoc
     */
    protected function getOriginalConstraint(): SymfonyConstraint
    {
        return new Assert\Length(['max' => static::LIMIT]);
    }

    /**
     * @inheritdoc
     */
    protected function getUnsupportedConstraint(): SymfonyConstraint
    {
        return new Assert\Valid();
    }

    /**
     * @inheritdoc
     */
    protected function createFactory(): FactoryInterface
    {
        return new MaxLengthFactory();
    }
}
