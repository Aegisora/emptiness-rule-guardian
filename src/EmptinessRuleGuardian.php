<?php

namespace Aegisora\RuleGuardians\EmptinessRuleGuardian;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\Rules\Emptiness\EmptyRule;
use Throwable;

class EmptinessRuleGuardian
{
    private Guardian $guardian;

    public function __construct(
        Guardian $guardian
    ) {
        $this->guardian = $guardian;
    }

    /**
     * @param mixed $value
     * @throws GuardianExecutingRuleException
     * @throws GuardianValidationException
     * @throws Throwable
     */
    public function checkEmpty(
        $value,
        ?Throwable $exception = null
    ): void {
        $this->guardian->check($value, EmptyRule::create(), $exception);
    }
}
