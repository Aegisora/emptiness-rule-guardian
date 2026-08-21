<?php

namespace Aegisora\RuleGuardians\EmptinessRuleGuardian;

use Aegisora\Guardian\Guardian;

class EmptinessRuleGuardian
{
    private Guardian $guardian;

    public function __construct(
        Guardian $guardian
    ) {
        $this->guardian = $guardian;
    }
}
