<?php

namespace Aegisora\RuleGuardians\EmptinessRuleGuardian\Tests\Unit;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\EmptinessRuleGuardian\EmptinessRuleGuardian;
use ArrayObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class EmptinessRuleGuardianTest extends TestCase
{
    private const EMPTY_RULE_CODE = 'empty_rule';
    private const NOT_EMPTY_RULE_CODE = 'not_empty_rule';

    private EmptinessRuleGuardian $guardian;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guardian = new EmptinessRuleGuardian(
            new Guardian()
        );
    }

    /**
     * @dataProvider getEmptyValuesProvidedData
     * @param mixed $value
     */
    public function testSuccessfullyCheckEmpty(
        $value
    ): void {
        $this->expectNotToPerformAssertions();

        $this->guardian->checkEmpty($value);
    }

    /**
     * @dataProvider getEmptyValuesProvidedData
     * @param mixed $value
     */
    public function testFailedCheckNotEmptyWithDefaultCustomException(
        $value
    ): void {
        $this->expectException(GuardianValidationException::class);

        try {
            $this->guardian->checkNotEmpty($value);
        } catch (GuardianValidationException $exception) {
            self::assertSame(self::NOT_EMPTY_RULE_CODE, $exception->getRuleCode());

            throw $exception;
        }
    }

    public static function getEmptyValuesProvidedData(): array
    {
        return [
            'value - null' => [
                'value' => null,
            ],
            'value - empty string' => [
                'value' => '',
            ],
            'value - empty array' => [
                'value' => [],
            ],
            'value - countable empty object' => [
                'value' => new ArrayObject([]),
            ],
        ];
    }

    /**
     * @dataProvider getNotEmptyValuesProvidedData
     * @param mixed $value
     */
    public function testFailedCheckEmptyWithDefaultCustomException(
        $value
    ): void {
        $this->expectException(GuardianValidationException::class);

        try {
            $this->guardian->checkEmpty($value);
        } catch (GuardianValidationException $exception) {
            self::assertSame(self::EMPTY_RULE_CODE, $exception->getRuleCode());

            throw $exception;
        }
    }

    /**
     * @dataProvider getNotEmptyValuesProvidedData
     * @param mixed $value
     */
    public function testSuccessfullyCheckNotEmpty(
        $value
    ): void {
        $this->expectNotToPerformAssertions();

        $this->guardian->checkNotEmpty($value);
    }

    public static function getNotEmptyValuesProvidedData(): array
    {
        return [
            'value - not empty string' => [
                'value' => 'foo',
            ],
            'value - not empty array' => [
                'value' => [1,],
            ],
            'value - zero integer' => [
                'value' => 0,
            ],
            'value - positive integer' => [
                'value' => 1,
            ],
            'value - negative integer' => [
                'value' => -1,
            ],
            'value - zero float' => [
                'value' => 0.0,
            ],
            'value - positive float' => [
                'value' => 0.01,
            ],
            'value - negative float' => [
                'value' => -0.01,
            ],
            'value - boolean true' => [
                'value' => true,
            ],
            'value - boolean false' => [
                'value' => false,
            ],
            'value - not countable object' => [
                'value' => new stdClass(),
            ],
            'value - countable not empty object' => [
                'value' => new ArrayObject([1,]),
            ],
            'value - callable' => [
                'value' => static function () {
                },
            ],
        ];
    }

    /**
     * @dataProvider getCheckEmptyFailedProvidedData
     * @param mixed $value
     */
    public function testFailedCheckEmpty(
        $value,
        ?Throwable $customRuleValidationException,
        string $expectedExceptionClassName
    ): void {
        $this->expectException($expectedExceptionClassName);

        $this->guardian->checkEmpty($value, $customRuleValidationException);
    }

    public static function getCheckEmptyFailedProvidedData(): array
    {
        return [
            'value - not empty string, custom exception - null' => [
                'value' => 'foo',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - not empty string, custom exception - not null' => [
                'value' => 'foo',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - not empty array, custom exception - null' => [
                'value' => [1,],
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - not empty array, custom exception - not null' => [
                'value' => [1,],
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - zero integer, custom exception - null' => [
                'value' => 0,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - zero integer, custom exception - not null' => [
                'value' => 0,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - positive integer, custom exception - null' => [
                'value' => 1,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - positive integer, custom exception - not null' => [
                'value' => 1,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - negative integer, custom exception - null' => [
                'value' => -1,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - negative integer, custom exception - not null' => [
                'value' => -1,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - zero float, custom exception - null' => [
                'value' => 0.0,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - zero float, custom exception - not null' => [
                'value' => 0.0,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - positive float, custom exception - null' => [
                'value' => 0.01,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - positive float, custom exception - not null' => [
                'value' => 0.01,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - negative float, custom exception - null' => [
                'value' => -0.01,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - negative float, custom exception - not null' => [
                'value' => -0.01,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - boolean true, custom exception - null' => [
                'value' => true,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - boolean true, custom exception - not null' => [
                'value' => true,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - boolean false, custom exception - null' => [
                'value' => false,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - boolean false, custom exception - not null' => [
                'value' => false,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - not countable object, custom exception - null' => [
                'value' => new stdClass(),
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - not countable object, custom exception - not null' => [
                'value' => new stdClass(),
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - countable not empty object, custom exception - null' => [
                'value' => new ArrayObject([1,]),
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - countable not empty object, custom exception - not null' => [
                'value' => new ArrayObject([1,]),
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - callable, custom exception - null' => [
                'value' => static function () {
                },
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - callable, custom exception - not null' => [
                'value' => static function () {
                },
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
        ];
    }

    public function testFailedCheckEmptyCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new EmptinessRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->checkEmpty(null);
    }

    public function testFailedCheckEmptyCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new EmptinessRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->checkEmpty(null);
    }

    public function testCheckEmptyThrowsGuardianExecutingRuleExceptionForResource(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $this->guardian->checkEmpty(tmpfile());
    }

    /**
     * @dataProvider getCheckNotEmptyFailedProvidedData
     * @param mixed $value
     */
    public function testFailedCheckNotEmpty(
        $value,
        ?Throwable $customRuleValidationException,
        string $expectedExceptionClassName
    ): void {
        $this->expectException($expectedExceptionClassName);

        $this->guardian->checkNotEmpty($value, $customRuleValidationException);
    }

    public static function getCheckNotEmptyFailedProvidedData(): array
    {
        return [
            'value - null, custom exception - null' => [
                'value' => null,
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - null, custom exception - not null' => [
                'value' => null,
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - empty string, custom exception - null' => [
                'value' => '',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - empty string, custom exception - not null' => [
                'value' => '',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - empty array, custom exception - null' => [
                'value' => [],
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - empty array, custom exception - not null' => [
                'value' => [],
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
            'value - countable empty object, custom exception - null' => [
                'value' => new ArrayObject([]),
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'value - countable empty object, custom exception - not null' => [
                'value' => new ArrayObject([]),
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
        ];
    }

    public function testFailedCheckNotEmptyCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new EmptinessRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->checkNotEmpty(null);
    }

    public function testFailedCheckNotEmptyCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new EmptinessRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->checkNotEmpty(null);
    }

    public function testCheckNotEmptyThrowsGuardianExecutingRuleExceptionForResource(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $this->guardian->checkNotEmpty(tmpfile());
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianThrowsExceptionOnCheck(string $expectedExceptionClass): Guardian
    {
        $guardian = $this->getGuardianMock();

        $guardian
            ->expects(self::once())
            ->method('check')
            ->willThrowException($this->createMock($expectedExceptionClass));

        return $guardian;
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianMock(): Guardian
    {
        /** @var Guardian|MockObject $mock */
        $mock = $this->createMock(Guardian::class);

        return $mock;
    }
}
