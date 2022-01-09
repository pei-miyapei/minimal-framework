<?php

declare(strict_types=1);

namespace App\Config;

use App\Config\Enum\Environment;
use SmallFramework\Core\Config\EnvironmentService as CoreEnvironmentService;

/**
 * 環境サービス
 *
 * 環境判別＆環境別定数サービスクラス読み込み
 */
final class EnvironmentService extends CoreEnvironmentService
{
    /**
     * 環境判別ロジックを記述（仮想環境でない際の例）
     *
     * 環境ごとの、configディレクトリまでのサーバ内部パスの違いによって
     * 対応する環境の値（Environment）を返却する
     */
    protected function getEnvironmentByServerPath(): Environment
    {
        switch (__DIR__) {
            case '/path/to/release/App/Config':
                // 本番
                return Environment::Production();

            case '/path/to/test/App/Config':
                // テスト
                return Environment::Test();

            default:
                // 開発
                return Environment::Development();
        }
    }
}
