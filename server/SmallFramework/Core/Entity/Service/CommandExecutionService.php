<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

use SmallFramework\Core\Config\ConfigService;

class CommandExecutionService
{
    /**
     * コントローラアクションのコマンドライン実行の定形
     *
     * @param mixed $data
     * @param bool  $isAsynchronous 非同期実行か
     */
    public static function execute(string $controllerPathAndAction, $data, bool $isAsynchronous = true): ?array
    {
        $data = self::encode($data);

        // PHPコマンドのパス
        $phpCommandPath = ConfigService::getConstant('PhpCommandPath');

        if (empty($phpCommandPath)) {
            $phpCommandPath = 'php';
        }

        $command = sprintf(
            '%s %s %s%s > /dev/null%s',
            $phpCommandPath,
            ServerPathToFronController,
            $controllerPathAndAction,
            empty($data) ? '' : ' '.$data,
            !$isAsynchronous ? '' : ' &'
        );

        $output = null;
        exec($command, $output);

        return $output;
    }

    /**
     * データをコマンドラインで渡すために
     * serialize、base64_encodeをかける
     *
     * @param mixed $data
     */
    public static function encode($data): string
    {
        $data = serialize($data);

        return base64_encode($data);
    }

    /**
     * コマンドライン用に変換されたデータを復元する
     * base64_decode, unserializeをかける
     *
     * @return mixed
     */
    public static function decode(string $data)
    {
        $data = base64_decode($data, true);

        return unserialize($data);
    }
}
