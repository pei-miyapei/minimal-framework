<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Model;

/**
 * throwable_logs
 * Throwable（Error、Exception）ログ
 */
class ThrowableLogModel
{
    /**
     * auto_increment
     *
     * @var int(int:10 unsigned)
     */
    public int $id = 0;

    /**
     * 要求されたコントローラパス
     *
     * @var string(varchar:255)
     */
    public string $request_controller_path = '';

    /**
     * 要求されたアクション
     *
     * @var string(varchar:255)
     */
    public string $request_action = '';

    /**
     * 例外メッセージ
     *
     * @var string(text)
     */
    public string $message = '';

    /**
     * 例外が作られたファイル
     *
     * @var string(text)
     */
    public string $file = '';

    /**
     * 例外が作られた行
     *
     * @var int(int:10 unsigned)
     */
    public int $line = 0;

    /**
     * スタックトレース
     *
     * @var string(text)
     */
    public string $stack_trace = '';

    /**
     * プロセスID
     *
     * @var int(int:10 unsigned)
     */
    public int $pid = 0;

    /**
     * 例外コード
     *
     * @var string(varchar:255)
     */
    public string $code = '';

    /**
     * 前の例外
     *
     * @var string(text)
     */
    public string $previous = '';

    /**
     * 作成日時
     *
     * @var string(datetime)
     */
    public string $created_at = '0000-00-00 00:00:00';

    public function initialize(\Throwable $throwable): void
    {
        $this->request_controller_path = RequestControllerPath;
        $this->request_action = (string) RequestAction;
        $this->message = $throwable->getMessage();
        $this->file = $throwable->getFile();
        $this->line = $throwable->getLine();
        $this->stack_trace = (string) $throwable;
        $this->pid = getmypid();
        $this->code = (string) $throwable->getCode();
        $this->previous = (string) $throwable->getPrevious();
    }

    /**
     * 新規登録時用のカラムと値の定形
     *
     * @return string[]
     */
    public function getCollectionForInsert(): array
    {
        $collection = [];
        $collection['request_controller_path'] = sprintf("'%s'", addslashes($this->request_controller_path));
        $collection['request_action'] = sprintf("'%s'", addslashes($this->request_action));
        $collection['message'] = sprintf("'%s'", addslashes($this->message));
        $collection['file'] = sprintf("'%s'", addslashes($this->file));
        $collection['line'] = (int) $this->line;
        $collection['stack_trace'] = sprintf("'%s'", addslashes($this->stack_trace));
        $collection['pid'] = (int) $this->pid;
        $collection['code'] = sprintf("'%s'", addslashes($this->code));
        $collection['previous'] = sprintf("'%s'", addslashes($this->previous));
        $collection['created_at'] = 'NOW()';

        return $collection;
    }
}
