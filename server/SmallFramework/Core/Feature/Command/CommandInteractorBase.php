<?php

declare(strict_types=1);

namespace SmallFramework\Core\Feature\Command;

abstract class CommandInteractorBase
{
    public function __construct(CommandPresenter $presenter)
    {
        // CommandPresenterをnewさせ、デフォルトテンプレートを無効化させるため。
        // 出力なし。
    }
}
