<?php

declare(strict_types=1);

namespace SmallFramework\Core;

use SmallFramework\Core\Entity\Service\ClassService;

final class Timer
{
    /**
     * コレクション
     *
     * @var array
     */
    private static $collection;

    /**
     * 対応するコメント用
     *
     * @var array
     */
    private static $messageCollection;

    /** 初期化 */
    public static function initialize(bool $useRequestTimeFloat = true): void
    {
        self::$collection = self::$messageCollection = [];

        if ($useRequestTimeFloat && isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            self::$collection[] = $_SERVER['REQUEST_TIME_FLOAT'];
        } else {
            self::$collection[] = microtime(true);
        }

        self::$messageCollection[] = \count(self::$collection);
    }

    /** 計測タイミングの挿入 */
    public static function push(?string $message = null): void
    {
        self::$collection[] = microtime(true);
        self::$messageCollection[] = sprintf(
            '[%s] %03s:%s',
            getmypid(),
            \count(self::$collection) - 1,
            empty($message) ? '' : sprintf(' (%s)', $message)
        );
    }

    /**
     * self::push()のラッパー
     * クラスメソッド用の計測タイミングの挿入
     */
    public static function pushForClassMethod(string $className, string $functionName, ?string $message = null): void
    {
        self::push(sprintf(
            '%s::%s%s',
            ClassService::getClassName($className),
            $functionName,
            empty($message) ? '' : ' # '.$message
        ));
    }

    /**
     * 結果を取得
     *
     * 引数のしきい値（秒）を超える場合のみ結果を返す。
     * （未指定時0秒）
     */
    public static function getResult(int $threshold = 0): string
    {
        if ((end(self::$collection) - self::$collection[0]) <= $threshold) {
            return '';
        }

        $result = [];
        $result[] = sprintf(
            '%s%s',
            RequestControllerPath,
            RequestAction === false ? '' : '?a='.RequestAction
        );

        for ($key = 0; $key < (\count(self::$collection) - 1); ++$key) {
            $result[] = self::$messageCollection[$key + 1];
            $result[] = sprintf('%.6f', self::$collection[$key + 1] - self::$collection[$key]).'秒';
        }

        $result[] = sprintf('[%s] all:', getmypid());
        $result[] = self::getCurrentTime().'秒';

        return implode("\n", $result)."\n";
    }

    public static function getCurrentTime(): string
    {
        return sprintf('%.6f', microtime(true) - self::$collection[0]);
    }
}
