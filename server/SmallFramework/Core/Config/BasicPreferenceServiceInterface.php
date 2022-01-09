<?php

declare(strict_types=1);

namespace SmallFramework\Core\Config;

interface BasicPreferenceServiceInterface
{
    /**
     * デフォルトで起動するコントローラー名を設定する
     *
     * @param string $controllerClassName コントローラーの完全修飾クラス名
     */
    public function setDefaultController(string $controllerClassName);

    /**
     * デフォルトで起動するコントローラー名を返す
     * （コントローラーが見つからない場合などに使用）
     */
    public function getDefaultControllerClassName(): string;
}
