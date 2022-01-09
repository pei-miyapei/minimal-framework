<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service\Request;

interface RequestServiceInterface
{
    public function __construct();

    /**
     * $_GETの値を取得
     *
     * @return mixed
     */
    public function get(?string $key = null);

    /**
     * $_POSTの値を取得
     *
     * @return mixed
     */
    public function getPost(?string $key = null);

    /**
     * $_SERVERの値を取得
     *
     * @return mixed
     */
    public function getServer(?string $key = null);

    /**
     * $_REQUEST の値を取得
     *
     * @return mixed
     */
    public function getRequest(?string $key = null);

    /**
     * $_FILES の値を取得
     *
     * @param ?string $key
     * @param ?string $key2
     *
     * @return mixed
     */
    public function getFiles(?string $key = null, ?string $key2 = null);

    /** PUTリクエスト（標準入力の内容）を取得 */
    public function getPut(): string;
}
