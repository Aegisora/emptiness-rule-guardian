# Aegisora Emptiness Rule Guardian

[![Latest Version](https://img.shields.io/packagist/v/aegisora/emptiness-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/emptiness-rule-guardian)
[![Total Downloads](https://img.shields.io/packagist/dt/aegisora/emptiness-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/emptiness-rule-guardian)
![Code Coverage Badge](./badge.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)

Emptiness Rule Guardian provides a simple shortcut for emptiness validation using `aegisora/guardian` and `aegisora/emptiness-rule`.

It is designed for cases where you want to quickly check whether a value **is empty** or **is not empty**, without manually building an `EmptyRule` / `NotEmptyRule` and a validation pipeline by hand.

This package is built on top of:

* [aegisora/guardian](https://github.com/Aegisora/guardian)
* [aegisora/emptiness-rule](https://github.com/Aegisora/emptiness-rule)

---

## ✨ Features

* 🔹 Simple shortcut API for `EmptyRule` and `NotEmptyRule`
* 🔹 Validates that a value is empty via `checkEmpty()`
* 🔹 Validates that a value is not empty via `checkNotEmpty()`
* 🔹 Works with scalars, arrays and countable objects
* 🔹 Uses `aegisora/guardian` internally
* 🔹 Uses `aegisora/emptiness-rule` internally
* 🔹 Supports a custom validation exception
* 🔹 Keeps rule execution errors separated from validation errors
* 🔹 Fully compatible with the Aegisora ecosystem
* 🔹 Ready to use out of the box

---

## 📦 Installation

```bash
composer require aegisora/emptiness-rule-guardian
```

---

## 🚀 Core Concept

This package wraps the common emptiness validation flow:

```php
$guardian->check(
    $value,
    EmptyRule::create(),
    new ValueIsNotEmptyException()
);

$guardian->check(
    $value,
    NotEmptyRule::create(),
    new ValueIsEmptyException()
);
```

into a dedicated shortcut class:

```php
$emptinessRuleGuardian->checkEmpty($value, new ValueIsNotEmptyException());
$emptinessRuleGuardian->checkNotEmpty($value, new ValueIsEmptyException());
```

Instead of manually creating an `EmptyRule` / `NotEmptyRule` and passing it to `Guardian`, you can use `EmptinessRuleGuardian` directly.

---

## 🏗️ Basic Usage

```php
use Aegisora\Guardian\Guardian;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\RuleGuardians\EmptinessRuleGuardian\EmptinessRuleGuardian;

$guardian = new Guardian();

$emptinessRuleGuardian = new EmptinessRuleGuardian($guardian);

try {
    $emptinessRuleGuardian->checkEmpty($value);
    // $value is empty
} catch (GuardianValidationException $exception) {
    // $value is not empty
}

try {
    $emptinessRuleGuardian->checkNotEmpty($value);
    // $value is not empty
} catch (GuardianValidationException $exception) {
    // $value is empty
}
```

`checkEmpty()` **passes when** `$value` is empty, and **fails otherwise**.

`checkNotEmpty()` **passes when** `$value` is not empty, and **fails otherwise**.

---

## ✅ How the emptiness check works

A value is considered **empty** when it is `null`, an empty string, an empty array or an empty countable object.
Every other value — including `0`, `0.0`, `false` and non-countable objects — is considered **not empty**:

```php
$emptinessRuleGuardian->checkEmpty(null);                // passes (null)
$emptinessRuleGuardian->checkEmpty('');                  // passes (empty string)
$emptinessRuleGuardian->checkEmpty([]);                  // passes (empty array)
$emptinessRuleGuardian->checkEmpty(new ArrayObject([])); // passes (empty countable object)

$emptinessRuleGuardian->checkEmpty('foo');               // fails (non-empty string)
$emptinessRuleGuardian->checkEmpty([1]);                 // fails (non-empty array)
$emptinessRuleGuardian->checkEmpty(0);                   // fails (int)
$emptinessRuleGuardian->checkEmpty(0.0);                 // fails (float)
$emptinessRuleGuardian->checkEmpty(false);               // fails (bool)
$emptinessRuleGuardian->checkEmpty(new stdClass());      // fails (non-countable object)
$emptinessRuleGuardian->checkEmpty(new ArrayObject([1]));// fails (non-empty countable object)
$emptinessRuleGuardian->checkEmpty(static fn () => null);// fails (callable)
```

`checkNotEmpty()` is the exact inverse — it passes for every value listed above as *fails* and fails for every value listed as *passes*.

> ⚠️ A `resource` cannot be evaluated for emptiness. Passing a resource raises a `GuardianExecutingRuleException` (see below) instead of a validation result.

---

## 🧩 Usage with Custom Exception

You may provide your own exception for validation failure. It must be the **last** argument.

```php
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\EmptinessRuleGuardian\EmptinessRuleGuardian;
use App\Exceptions\ValueIsEmptyException;

$guardian = new Guardian();

$emptinessRuleGuardian = new EmptinessRuleGuardian($guardian);

$emptinessRuleGuardian->checkNotEmpty(
    $value,
    new ValueIsEmptyException()
);
```

If the value is empty, the provided exception will be thrown instead of `GuardianValidationException`.

This is useful when validation errors should have domain-specific meaning.

---

## 🧪 Example in Application Service

```php
use Aegisora\RuleGuardians\EmptinessRuleGuardian\EmptinessRuleGuardian;
use App\Exceptions\EmptyPayloadException;

final class PayloadProcessor
{
    private EmptinessRuleGuardian $emptinessRuleGuardian;

    public function __construct(
        EmptinessRuleGuardian $emptinessRuleGuardian
    ) {
        $this->emptinessRuleGuardian = $emptinessRuleGuardian;
    }

    /**
     * @param mixed $payload
     */
    public function process($payload): void
    {
        $this->emptinessRuleGuardian->checkNotEmpty(
            $payload,
            new EmptyPayloadException()
        );

        // business logic for processing a non-empty payload
    }
}
```

---

## 🚨 Exceptions

The package raises validation-related exceptions, all delegated to `Guardian` (the outcome of running the rule):

### `GuardianValidationException`

Thrown when validation fails and no custom exception is provided.

The rule code for a failed `checkEmpty()` is `empty_rule`, and for a failed `checkNotEmpty()` it is `not_empty_rule`.

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;

try {
    $emptinessRuleGuardian->checkEmpty($value);
} catch (GuardianValidationException $exception) {
    echo $exception->getRuleCode(); // "empty_rule"
}

try {
    $emptinessRuleGuardian->checkNotEmpty($value);
} catch (GuardianValidationException $exception) {
    echo $exception->getRuleCode(); // "not_empty_rule"
}
```

### Custom exception

When a custom exception is passed as the last argument, it is thrown instead of `GuardianValidationException` on validation failure.

```php
use App\Exceptions\ValueIsEmptyException;

try {
    $emptinessRuleGuardian->checkNotEmpty($value, new ValueIsEmptyException());
} catch (ValueIsEmptyException $exception) {
    // domain-specific handling
}
```

### `GuardianExecutingRuleException`

Thrown when the underlying rule fails to execute (raises a `RuleException` during validation), as opposed to simply reporting an invalid result.

Emptiness cannot be determined for a `resource`, so passing a resource surfaces this exception instead of a validation result:

```php
use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;

try {
    $emptinessRuleGuardian->checkEmpty(tmpfile());
} catch (GuardianExecutingRuleException $exception) {
    // the rule could not be executed
}
```

---

## 🧩 API

### `EmptinessRuleGuardian::checkEmpty()`

```php
/**
 * @param mixed $value
 * @throws GuardianExecutingRuleException
 * @throws GuardianValidationException
 * @throws \Throwable
 */
public function checkEmpty($value, ?\Throwable $exception = null): void
```

Validates that `$value` is **empty**.

### `EmptinessRuleGuardian::checkNotEmpty()`

```php
/**
 * @param mixed $value
 * @throws GuardianExecutingRuleException
 * @throws GuardianValidationException
 * @throws \Throwable
 */
public function checkNotEmpty($value, ?\Throwable $exception = null): void
```

Validates that `$value` is **not empty**.

Arguments (both methods):

* `$value` — the value to validate
* `$exception` — an optional custom `\Throwable` to be thrown on validation failure

The methods return `void`. They communicate results through exceptions only — they return nothing on success and throw on failure:

* `GuardianValidationException` — the emptiness check failed and no custom exception was provided
* the provided custom exception — the check failed and a custom exception was passed
* `GuardianExecutingRuleException` — the rule could not be executed

---

## 🏛️ Architecture

This package is a small shortcut layer over the Aegisora validation pipeline.

Flow:

1. `EmptinessRuleGuardian::checkEmpty()` / `checkNotEmpty()` is called with a value and an optional exception
2. An `EmptyRule` / `NotEmptyRule` is created (`create()`)
3. `Guardian` executes the rule against the value
4. If the check passes, execution continues normally
5. If the check fails, the custom exception or `GuardianValidationException` is thrown
6. If the rule could not be executed, `GuardianExecutingRuleException` is thrown

Internal flow:

```
value → EmptinessRuleGuardian → Guardian → EmptyRule / NotEmptyRule → Result → Exception
```

---

## 🔗 Related Packages

* [aegisora/guardian](https://github.com/Aegisora/guardian) — validation execution orchestrator
* [aegisora/emptiness-rule](https://github.com/Aegisora/emptiness-rule) — empty and not empty rules
* [aegisora/rule-contract](https://github.com/Aegisora/rule-contract) — base rule contract and validation result architecture

---

## ⚖️ License

This package is open-source and licensed under the MIT License. See the LICENSE for details.

---

## 🌱 Contributing

Contributions are welcome and greatly appreciated!. See the CONTRIBUTING for details.

---

## 🌟 Support

If you find this project useful, please consider giving it a star on GitHub!

It helps the project grow and motivates further development.
