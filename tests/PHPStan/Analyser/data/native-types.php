<?php

namespace NativeTypes;

use function PHPStan\Analyser\assertType;
use function PHPStan\Analyser\assertNativeType;

class Foo
{

	/**
	 * @param self $foo
	 * @param \DateTimeImmutable $dateTime
	 * @param \DateTimeImmutable $dateTimeMutable
	 * @param string $nullableString
	 * @param string|null $nonNullableString
	 */
	public function doFoo(
		$foo,
		\DateTimeInterface $dateTime,
		\DateTime $dateTimeMutable,
		?string $nullableString,
		string $nonNullableString
	): void
	{
		assertType(Foo::class, $foo);
		assertNativeType('mixed', $foo);

		// change type after assignment
		$foo = new Foo();
		assertType(Foo::class, $foo);
		assertNativeType(Foo::class, $foo);

		assertType(\DateTimeImmutable::class, $dateTime);
		assertNativeType(\DateTimeInterface::class, $dateTime);

		$f = function (Foo $foo) use ($dateTime) {
			assertType(Foo::class, $foo);
			assertNativeType(Foo::class, $foo);

			assertType(\DateTimeImmutable::class, $dateTime);
			assertNativeType(\DateTimeInterface::class, $dateTime);
		};

		assertType(\DateTime::class, $dateTimeMutable);
		assertNativeType(\DateTime::class, $dateTimeMutable);

		assertType('string|null', $nullableString);
		assertNativeType('string|null', $nullableString);

		if (is_string($nullableString)) {
			// change specified type
			assertType('string', $nullableString);
			assertNativeType('string', $nullableString);

			// preserve other variables
			assertType(\DateTimeImmutable::class, $dateTime);
			assertNativeType(\DateTimeInterface::class, $dateTime);
		}

		// preserve after merging scopes
		assertType(\DateTimeImmutable::class, $dateTime);
		assertNativeType(\DateTimeInterface::class, $dateTime);

		assertType('string', $nonNullableString);
		assertNativeType('string', $nonNullableString);

		unset($nonNullableString);
		assertType('*ERROR*', $nonNullableString);
		assertNativeType('*ERROR*', $nonNullableString);

		// preserve other variables
		assertType(\DateTimeImmutable::class, $dateTime);
		assertNativeType(\DateTimeInterface::class, $dateTime);
	}

	/**
	 * @param array<string, int> $array
	 */
	public function doForeach(array $array): void
	{
		assertType('array<string, int>', $array);
		assertNativeType('array', $array);

		foreach ($array as $key => $value) {
			assertType('array<string, int>', $array);
			assertNativeType('array', $array);

			assertType('string', $key);
			assertNativeType('(int|string)', $key);

			assertType('int', $value);
			assertNativeType('mixed', $value);
		}
	}

	/**
	 * @param self $foo
	 */
	public function doCatch($foo): void
	{
		assertType(Foo::class, $foo);
		assertNativeType('mixed', $foo);

		try {
			throw new \Exception();
		} catch (\InvalidArgumentException $foo) {
			assertType(\InvalidArgumentException::class, $foo);
			assertNativeType(\InvalidArgumentException::class, $foo);
		} catch (\Exception $e) {
			assertType(\Exception::class, $e);
			assertNativeType(\Exception::class, $e);

			assertType(Foo::class, $foo);
			assertNativeType('mixed', $foo);
		}
	}

	/**
	 * @param array<string, array{int, string}> $array
	 */
	public function doForeachArrayDestructuring(array $array)
	{
		assertType('array<string, array(int, string)>', $array);
		assertNativeType('array', $array);
		foreach ($array as $key => [$i, $s]) {
			assertType('array<string, array(int, string)>', $array);
			assertNativeType('array', $array);

			assertType('string', $key);
			assertNativeType('(int|string)', $key);

			assertType('int', $i);
			// assertNativeType('mixed', $i);

			assertType('string', $s);
			// assertNativeType('mixed', $s);
		}
	}

}

/**
 * @param Foo $foo
 * @param \DateTimeImmutable $dateTime
 * @param \DateTimeImmutable $dateTimeMutable
 * @param string $nullableString
 * @param string|null $nonNullableString
 */
function fooFunction(
	$foo,
	\DateTimeInterface $dateTime,
	\DateTime $dateTimeMutable,
	?string $nullableString,
	string $nonNullableString
): void
{
	assertType(Foo::class, $foo);
	assertNativeType('mixed', $foo);

	assertType(\DateTimeImmutable::class, $dateTime);
	assertNativeType(\DateTimeInterface::class, $dateTime);

	assertType(\DateTime::class, $dateTimeMutable);
	assertNativeType(\DateTime::class, $dateTimeMutable);

	assertType('string|null', $nullableString);
	assertNativeType('string|null', $nullableString);

	assertType('string', $nonNullableString);
	assertNativeType('string', $nonNullableString);
}
