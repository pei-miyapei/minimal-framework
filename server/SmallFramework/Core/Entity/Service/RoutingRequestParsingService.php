<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

use SmallFramework\Core\Entity\Service\Request\CommandRequestService;
use SmallFramework\Core\Entity\Service\Request\RequestServiceInterface;

/**
 * ルーティング要求の解析サービス
 *
 * リクエストURIを元に
 * プロジェクトの基点までのパス、
 * コントローラのパスに分解して保持する
 */
final class RoutingRequestParsingService
{
    /** プロジェクトの基点までのパス */
    public string $baseUrlPath = '';

    /** 要求されたコントローラーのパス */
    public string $controllerPath = '';

    /** 要求されたアクション */
    public ?string $action = '';

    public function __construct(RequestServiceInterface $request)
    {
        if (\PHP_SAPI === 'cli' && $request instanceof CommandRequestService) {
            // コマンドラインの場合
            // /path/to/php /path/to/index.php /controllerPath?a=hoge

            // コントローラーパスをセット
            if (null !== $request->getArgv(1)) {
                $this->controllerPath = strtolower(parse_url($request->getArgv(1), PHP_URL_PATH));
            } else {
                $temp = debug_backtrace();
                $this->controllerPath = $temp[\count($temp) - 1]['file'];
            }

            // アクションをセット
            $get = [];
            parse_str((string) parse_url($request->getArgv(1), PHP_URL_QUERY), $get);
            $this->action = $get['a'] ?? null;
        } else {
            // 通常の場合
            // パス解析基点の階層深さを取得
            $pathBaseDepth = $this->getPathBaseDepth($request);

            // RequestUriを分解
            $explodedRequestUri = $this->getExplodedRequestUri($request);

            // ベースパスをセット
            $this->setBasePath($explodedRequestUri, $pathBaseDepth);

            // コントローラーパスをセット
            $this->setControllerPath($explodedRequestUri, $pathBaseDepth);

            // アクションをセット
            $this->action = $request->getRequest('a');
        }
    }

    /**
     * 最上位のindex.phpが実行基点となっているので
     * $_SERVER["SCRIPT_NAME"] で実行基点までのパスを取得し、
     * そこまでの階層数をパスを解析する基点となる階層深さとして返す
     */
    private function getPathBaseDepth(RequestServiceInterface $request): int
    {
        $urlPathToCurrentDirectory = \dirname($request->getServer('SCRIPT_NAME'));

        if ($urlPathToCurrentDirectory === '/') {
            return 0;
        }

        // SCRIPT_NAMEのパスを分解してカウント
        return \count(explode('/', $urlPathToCurrentDirectory)) - 1;
    }

    /** $_SERVER["REQUEST_URI"] を階層ごとに分解した配列として返す */
    private function getExplodedRequestUri(RequestServiceInterface $request): array
    {
        $temp = $request->getServer('REQUEST_URI');
        $temp = parse_url($temp, PHP_URL_PATH);
        $temp = ltrim($temp, '/');   // 先頭のスラッシュを破棄

        return explode('/', $temp); // 分解
    }

    /** 分解したRequestUriのうち、パス解析基点までをベースパスとして保持する */
    private function setBasePath(array $explodedRequestUri, int $pathBaseDepth): void
    {
        $baseUrlPath = implode('/', \array_slice($explodedRequestUri, 0, $pathBaseDepth));

        if ($baseUrlPath !== '') {
            $this->baseUrlPath = '/'.$baseUrlPath;
        }
    }

    /** 分解したRequestUriのうち、パス解析基点以降をコントローラーのパスとして保持する */
    private function setControllerPath(array $explodedRequestUri, int $pathBaseDepth): void
    {
        $this->controllerPath = '/'.implode('/', \array_slice($explodedRequestUri, $pathBaseDepth, \count($explodedRequestUri)));

        // コントローラーパスの末尾がスラッシュで終わる場合は末尾を /index に変える
        if (substr($this->controllerPath, -1) === '/') {
            $this->controllerPath .= 'index';
        }
    }
}
