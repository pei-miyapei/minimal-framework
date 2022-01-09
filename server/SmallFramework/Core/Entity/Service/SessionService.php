<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

final class SessionService
{
    /** セッション開始 */
    public function start(): void
    {
        session_cache_expire(180);
        session_start();
    }

    /**
     * セッション情報を取得
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return !isset($_SESSION[$key]) ? null : $_SESSION[$key];
    }

    /**
     * セッションに値をセット
     *
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /** セッション情報を全て破棄 */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /** CSRFトークンの生成 */
    public function generateToken(): string
    {
        // セッションIDからハッシュを生成
        return hash('sha256', session_id());
    }

    /** CSRFトークンの検証 */
    public function validateToken(string $token): bool
    {
        // 送信されてきた$tokenがこちらで生成したハッシュと一致するか検証
        return $token === $this->generateToken();
    }

    public function regenerateId(bool $isDeleteOldSession = false): bool
    {
        return session_regenerate_id($isDeleteOldSession);
    }
}
