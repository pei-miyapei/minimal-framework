<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config;

use Dependency\Dependency;
use SmallFramework\Core\Config\Enum\Environment;
use SmallFramework\Core\Debug;
use SmallFramework\Core\Entity\Service\ConvertCaseService;

class DatabaseService implements DatabaseServiceInterface
{
    /**
     * /Path/To/Config/Environment/(環境) ディレクトリの中の
     * PdoConnection(n).php ファイルから、
     * singletonインスタンスを生成・登録
     * ※ 変更不要
     */
    public function __construct(Environment $environment)
    {
        $pascalCaseEnvironmentName = ConvertCaseService::snakeToPascal((string) $environment);
        $serverPathToEnvironmentDirectory = ServerPathToConfigDirectory.'/Environment/'.$pascalCaseEnvironmentName;

        if (!is_dir($serverPathToEnvironmentDirectory)) {
            return;
        }

        if ($handle = opendir($serverPathToEnvironmentDirectory)) {
            // ディレクトリ内をループ
            while (($file = readdir($handle)) !== false) {
                if (
                       \in_array($file, ['.', '..'], true)
                    || !is_file($serverPathToEnvironmentDirectory.'/'.$file)
                    || !preg_match('/^PdoConnection\\d\\.php$/', $file)
                ) {
                    continue;
                }
                // 環境別定数サービスクラス読み込み
                require_once $serverPathToEnvironmentDirectory.'/'.$file;

                $className = str_replace('.php', '', $file);
                $fullyQualifiedClassName = sprintf(
                    '%s\\Environment\\%s\\%s',
                    NamespaceToConfigDirectory,
                    $pascalCaseEnvironmentName,
                    $className
                );
                $pdo = $fullyQualifiedClassName::getInstance();

                if (!empty($pdo)) {
                    // インスタンスをセット
                    Dependency::singleton($pdo);

                    // インターフェースとモデルを関連付け
                    Dependency::bind(
                        sprintf('%s\\Environment\\%sInterface', NamespaceToConfigDirectory, $className),
                        $fullyQualifiedClassName
                    );

                    Debug::trace(sprintf('データベース：%s（%s）', $pdo->getHostName(), $pdo->getTitle()));
                }
            }

            closedir($handle);
        }
    }
}
