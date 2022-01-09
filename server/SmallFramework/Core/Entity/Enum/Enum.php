<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Enum;

abstract class Enum
{
    /**
     * @var mixed
     */
    protected $scalar;

    /**
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        $reflectionObject = new \ReflectionObject($this);

        // 自分自身のすべての定数を取得
        $constCollection = $reflectionObject->getConstants();

        if (!\in_array($value, $constCollection, true)) {
            throw new \InvalidArgumentException();
        }

        $this->scalar = $value;
    }

    /**
     * $suit = new Suit(Suit::Spade); を
     * $suit = Suit::Spade(); と省略して書ける
     */
    final public static function __callStatic(string $label, array $args): object
    {
        // 静的遅延束縛の（静的メソッドのコール元の）クラス名を取得
        $className = '\\'.static::class;
        $constant = \constant($className.'::'.$label);

        return new $className($constant);
    }

    /** 元の値を（文字列として）取り出す */
    final public function __toString(): string
    {
        return (string) $this->scalar;
    }

    /**
     * 元の値を取り出す
     *
     * @return mixed
     */
    final public function valueOf()
    {
        return $this->scalar;
    }

    final public function equals(self $enum): bool
    {
        return $this->scalar === $enum->valueOf();
    }

    final public static function getValues(): array
    {
        $reflectionClass = new \ReflectionClass('\\'.static::class);

        return $reflectionClass->getConstants();
    }

    final public static function defined(string $label): bool
    {
        return \defined(sprintf('\\%s::%s', static::class, $label));
    }
}
