<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

final class FileService
{
    /** 相対パスからファイルのタイムスタンプの取得を試みる */
    public static function getTimestampByRelativePath(string $relativePath): int
    {
        if ($relativePath === '') {
            return 0;
        }

        $serverPathToFile = realpath($relativePath);

        return self::getTimesampByServerPath($serverPathToFile);
    }

    /** サーバーの内部パス（フルパス）からファイルのタイムスタンプの取得を試みる */
    public static function getTimesampByServerPath(string $serverPathToFile): int
    {
        if ($serverPathToFile === '' || !is_file($serverPathToFile)) {
            return 0;
        }

        return filemtime($serverPathToFile);
    }
}
