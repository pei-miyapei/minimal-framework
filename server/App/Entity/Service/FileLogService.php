<?php

declare(strict_types=1);

namespace App\Entity\Service;

use SmallFramework\Core\Config\ConfigService;

final class FileLogService
{
    /**
     * @param mixed $data
     *
     * @throws \RuntimeException
     */
    public static function add($data, string $fileNameSuffix = ''): bool
    {
        // ログ出力先
        $serverPathToFileLogDirectory = ConfigService::getConstant('ServerPathToFileLogDirectory');

        if (empty($serverPathToFileLogDirectory) || !is_dir($serverPathToFileLogDirectory)) {
            // 大本の出力先がない場合は処理しない
            return false;
        }

        $tempTime = time();

        // ディレクトリはとりあえず年月単位で分ける。
        $serverPathToFileLogDirectory .= date('/Y/Ym', $tempTime);

        if (!is_dir($serverPathToFileLogDirectory)) {
            exec('mkdir -p '.$serverPathToFileLogDirectory);

            if (!is_dir($serverPathToFileLogDirectory)) {
                throw new \RuntimeException('ファイルログのディレクトリを作成できませんでした（'.$serverPathToFileLogDirectory.'）');
            }
        }

        $log = [];
        $log[] = date('[Y-m-d H:i:s]', $tempTime);
        $log[] = \is_string($data) ? $data : var_export($data, true);

        $serverPathToLogFile = sprintf(
            '%s/%s%s.log',
            $serverPathToFileLogDirectory,
            date('Ymd', $tempTime),
            $fileNameSuffix
        );

        file_put_contents(
            $serverPathToLogFile,
            str_replace("\r\n", "\n", implode("\n", $log))."\n\n",
            FILE_APPEND
        );

        return true;
    }

    public static function delete(): void
    {
        // ログ出力先
        $serverPathToFileLogDirectory = ConfigService::getConstant('ServerPathToFileLogDirectory');

        if (empty($serverPathToFileLogDirectory) || !is_dir($serverPathToFileLogDirectory)) {
            // 大本の出力先がない場合は処理しない
            return;
        }

        // ファイルログ
        exec('/usr/bin/find '.$serverPathToFileLogDirectory.' -type f -mtime +'.ConfigService::getConstant('RetentionDays').' -name "*.log" -delete');

        // 空のディレクトリも掃除
        exec('/usr/bin/find '.$serverPathToFileLogDirectory.' -type d -empty -delete');
    }
}
