<?php

namespace SchematiconTests;

use Nette\Neon\Neon;
use Schematicon\Validator\Validator;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


$config = Neon::decode(<<<NEON
basic:
	type: float
nullable:
	type: float|null
	minValue: 0
	maxValue: 9.9
NEON
);


$validator = new Validator(prepareSchema($config['basic']));

Assert::same(
	[],
	$validator->validate(1.0)->getErrors()
);

Assert::same(
	["Wrong data type in '/'; expected 'float'; got 'int'"],
	$validator->validate(1)->getErrors()
);

Assert::same(
	["Wrong data type in '/'; expected 'float'; got 'string'"],
	$validator->validate("string")->getErrors()
);

Assert::same(
	["Wrong data type in '/'; expected 'float'; got 'array'"],
	$validator->validate([])->getErrors()
);


// =====================================================================================================================


$validator = new Validator(prepareSchema($config['nullable']));

Assert::same(
	[],
	$validator->validate(null)->getErrors()
);

Assert::same(
	[],
	$validator->validate(0.0)->getErrors()
);

Assert::same(
	[],
	$validator->validate(9.9)->getErrors()
);

Assert::same(
	["Wrong data type in '/'; expected 'float|null'; got 'array'"],
	$validator->validate([])->getErrors()
);

Assert::same(
	["Wrong value in '/'; expected number of maximal value '9.9'; got value '9.91'"],
	$validator->validate(9.91)->getErrors()
);

Assert::same(
	["Wrong value in '/'; expected number of minimal value '0'; got value '-0.1'"],
	$validator->validate(-0.1)->getErrors()
);
