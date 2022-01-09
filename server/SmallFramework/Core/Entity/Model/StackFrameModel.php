<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Model;

final class StackFrameModel
{
    /*
        getTrace(), debug_backtrace() で返される要素
        http://php.net/manual/ja/function.debug-backtrace.php
    */

    /** Currentの関数名 */
    public string $function = '';

    /** Currentの行番号 */
    public int $line = 0;

    /** Currentのファイル名 */
    public string $file = '';

    /** Currentのクラス名 */
    public string $class = '';

    /** Currentのオブジェクト */
    public ?object $object;

    /**
     * Currentのコール方式
     *
     * メソッド呼び出しの場合は "->"
     * 静的なメソッド呼び出しの場合は "::"
     * 関数呼び出しの場合は何も返されません。
     */
    public string $type = '';

    /**
     * 関数の内部の場合、関数の引数のリストとなります。
     * インクルードされたファイル内では、 読み込まれたファイルの名前となります。
     */
    public array $args = [];

    public function __construct(array $stackFrame)
    {
        foreach ($stackFrame as $key => $value) {
            if (\in_array($key, ['file', 'line'], true) && !isset($value)) {
                continue;
            }

            $this->{$key} = $value;
        }
    }

    /** スタックフレームのエラー発生箇所を整えて取得 */
    public function getErrorLocation(?\Throwable $throwable = null): string
    {
        $errorLocation = sprintf('L%-5s: ', $this->line);

        if (isset($this->class, $this->type)) {
            $errorLocation .= $this->class.$this->type.$this->function.'()';
        } else {
            $errorLocation .= sprintf('function %s()', $this->function);
        }

        if (isset($throwable)) {
            if (
                   $this->file === $throwable->getFile()
                && $this->line === $throwable->getLine()
            ) {
                // エラー発生箇所がスタックフレームのファイル・行と合致する場合
                $errorLocation .= ' [code: '.$throwable->getCode().']';
            }
        }

        return $errorLocation;
    }
}
