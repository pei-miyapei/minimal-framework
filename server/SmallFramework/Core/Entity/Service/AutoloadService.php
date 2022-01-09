<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

final class AutoloadService
{
    /**
     * 指定したディレクトリ以下で、
     * 完全修飾クラス名とパスが一致するphpファイルを自動読み込みする
     *
     * @param string $serverPathToTargetDirectory オートロード対象の起点ディレクトリ
     */
    public static function setAutoLoader(string $serverPathToTargetDirectory): void
    {
        spl_autoload_register(function ($className) use ($serverPathToTargetDirectory): void {
            $fileName = sprintf('/%s.php', str_replace('\\', '/', $className));

            if (is_file($serverPathToTargetDirectory.$fileName)) {
                require_once $serverPathToTargetDirectory.$fileName;
            }
        });
    }
}
