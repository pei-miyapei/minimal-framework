<?php

declare(strict_types=1);

namespace App\Entity\Enum;

use SmallFramework\Core\Entity\Enum\Enum;

final class HttpStatusCode extends Enum
{
    /**
     * OK。リクエストは成功し、レスポンスとともに要求に応じた情報が返される。
     *
     * @var int
     */
    public const Ok = 200;

    /**
     * 作成。リクエストは完了し、新たに作成されたリソースのURIが返される。
     *
     * @var int
     */
    public const Created = 201;

    /**
     * 内容なし。リクエストを受理したが、返すべきレスポンスエンティティが存在しない場合に返される。
     *
     * @var int
     */
    public const NoContent = 204;

    /**
     * リクエストが不正である。定義されていないメソッドを使うなど、クライアントのリクエストがおかしい場合に返される。
     *
     * @var int
     */
    public const BadRequest = 400;

    /**
     * 認証が必要である。Basic認証やDigest認証などを行うときに使用される。
     *
     * @var int
     */
    public const Unauthorized = 401;

    /**
     * 禁止されている。リソースにアクセスすることを拒否された。リクエストはしたが処理できないという意味。
     *
     * @var int
     */
    public const Forbidden = 403;

    /**
     * 未検出。リソース・ページが見つからなかった。
     *
     * @var int
     */
    public const NotFound = 404;

    /**
     * 競合。要求は現在のリソースと競合するので完了出来ない。
     *
     * @var int
     */
    public const Conflict = 409;

    /**
     * 処理できないエンティティ。WebDAVの拡張ステータスコード。
     * RESTにおいて、入力値の検証の失敗（バリデーションエラー）を伝える目的で使用する場合もある。
     *
     * @var int
     */
    public const UnprocessableEntity = 422;

    /**
     * サーバ内部エラー。サーバ内部にエラーが発生した場合に返される。
     *
     * @var int
     */
    public const InternalServerError = 500;
}
