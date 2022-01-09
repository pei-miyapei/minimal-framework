<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service;

class MailService
{
    /**
     * メールを送信する
     *
     * @throws \UnexpectedValueException
     */
    public static function send(string $to, string $subject, string $message, string $from): bool
    {
        // to check
        $toCollection = [];

        foreach (explode(',', $to) as $tempTo) {
            if (!self::checkIfIsValidEmailAddress($tempTo)) {
                throw new \UnexpectedValueException('無効なメールアドレスです。（To: '.(string) $tempTo.'）');
            }

            $toCollection[] = $tempTo;
        }

        $to = implode(',', $toCollection);

        // from check
        if (!self::checkIfIsValidEmailAddress($from)) {
            throw new \UnexpectedValueException('無効なメールアドレスです。（From: '.(string) $from.'）');
        }

        // 件名はサニタイズ
        $subject = filter_var($subject, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK);

        // ヘッダ
        $header = 'From: '.$from."\n";
        $header .= "MIME-Version: 1.0\n";

        // envelope from
        $option = '-f '.$from;

        return mb_send_mail($to, $subject, $message, $header, $option);
    }

    /** 有効なメールアドレスかどうかチェックして返す */
    public static function checkIfIsValidEmailAddress(string $emailAddress): bool
    {
        $tempClean = str_replace(["\r", "\n"], '', trim($emailAddress));
        $tempClean = filter_var($tempClean, FILTER_SANITIZE_EMAIL);

        return $tempClean === $emailAddress && filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
    }
}
