<?php

declare(strict_types=1);

namespace App\Config\Environment\Development;

//use SmallFramework\Core\Pdo;
use App\Config\Environment\PdoConnection1Interface;
use App\Config\Environment\PdoConnectionBase;

//final class PdoConnection1 extends Pdo implements PdoConnection1Interface
final class PdoConnection1 extends PdoConnectionBase implements PdoConnection1Interface
{
    /**
     * デバッグ用
     * データベース環境識別用の名称
     *
     * @var string
     */
    protected static $title = '開発';

    /**
     * ホスト名
     *
     * @var string
     */
    protected static $hostName = 'db';

    /**
     * データベース名
     *
     * @var string
     */
    protected static $database = 'dev_db';

    /**
     * ユーザー名
     *
     * @var string
     */
    protected static $username = 'root';

    /**
     * パスワード
     *
     * @var string
     */
    protected static $password = 'root';
}
