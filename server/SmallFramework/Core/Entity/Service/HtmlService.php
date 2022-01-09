<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

class HtmlService
{
    /** エスケープします */
    public static function escape(?string $text = null): string
    {
        return htmlentities((string) $text, ENT_QUOTES);
    }

    /** '<link rel="stylesheet" href="○○">' を返す */
    public static function getLinkTag(string $href): string
    {
        return sprintf('<link rel="stylesheet" href="%s">'."\n", $href);
    }

    /** '<script src="○○"></script>' を返す */
    public static function getScriptTag(string $src): string
    {
        return sprintf('<script src="%s"></script>'."\n", $src);
    }

    /**
     * サーバー内部パス（フルパス）から
     * プロジェクトルートのパスを取り除き、Url用のパスとして返す
     * ※ ファイル単位で利用するのは非効率なので推奨しない。
     * 　 （対象となる"ディレクトリ"のUrlパスを取得し、再利用を推奨。）
     */
    public static function getUrlPathFromServerPath(string $serverPath, string $baseServerPath = ''): string
    {
        if (empty($baseServerPath) && \defined('BaseServerPath')) {
            $baseServerPath = BaseServerPath;
        }

        if (empty($baseServerPath)) {
            // フルパスで返すとそのまま使用された場合危険なので空文字を返却する
            return '';
        }

        $pattern = sprintf('/^%s/', preg_quote($baseServerPath, '/'));
        $count = 0;
        $urlPath = preg_replace($pattern, '', $serverPath, -1, $count);

        if (empty($count)) {
            // フルパスで返すとそのまま使用された場合危険なので空文字を返却する
            return '';
        }

        return $urlPath;
    }

    /**
     * '<link rel="stylesheet" href="○○">' を返す
     * 通常の構成であればタイムスタンプも付与する
     *
     * @param string $pathFromBasePath BaseUrlPathからのパス
     */
    public static function getLinkTagByPathFromBasePath(string $pathFromBasePath): string
    {
        if ($pathFromBasePath === '') {
            return '';
        }

        return self::getLinkTag(self::getTimestampByPathFromBasePath($pathFromBasePath));
    }

    /**
     * '<script src="○○"></script>' を返す
     * 通常の構成であればタイムスタンプも付与する
     *
     * @param string $pathFromBasePath BaseUrlPathからのパス
     */
    public static function getScriptTagByPathFromBasePath(string $pathFromBasePath): string
    {
        if ($pathFromBasePath === '') {
            return '';
        }

        return self::getScriptTag(self::getTimestampByPathFromBasePath($pathFromBasePath));
    }

    /**
     * BaseUrlPath からのパスのファイルを探し、
     * (BaseUrlPath)(指定されたパス)?t=(ファイルのタイムスタンプ)
     * の形式にして返す
     *
     * ファイルが見つからなかった場合は
     * タイムスタンプのクエリ文字列無しで返す
     *
     * @param string $pathFromBasePath BaseUrlPathからのパス
     */
    public static function getTimestampByPathFromBasePath(string $pathFromBasePath): string
    {
        if ($pathFromBasePath === '' || !\defined('BaseUrlPath')) {
            return $pathFromBasePath;
        }

        $urlPath = BaseUrlPath.$pathFromBasePath;

        if (!\defined('BaseServerPath') || !class_exists('\\SmallFramework\\Core\\Entity\\Service\\FileService')) {
            return $urlPath;
        }

        $timestamp = FileService::getTimesampByServerPath(BaseServerPath.$pathFromBasePath);

        if (!empty($timestamp)) {
            $urlPath .= '?t='.(string) $timestamp;
        }

        return $urlPath;
    }
}
