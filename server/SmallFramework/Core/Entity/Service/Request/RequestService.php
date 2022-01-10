<?php

declare(strict_types=1);

namespace SmallFramework\Core\Entity\Service\Request;

use Dependency\Dependency;

class RequestService implements RequestServiceInterface
{
    /** $_GET */
    private array $get = [];

    /** $_POST */
    private array $post = [];

    /** $_FILES */
    private array $files = [];

    /** php://input */
    private string $standardInput = '';

    public function __construct()
    {
        Dependency::singleton($this);

        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->standardInput = file_get_contents('php://input');
    }

    /**
     * $_GETの値を取得
     *
     * {@inheritDoc}
     *
     * @see RequestServiceInterface::get()
     */
    public function get(?string $key = null)
    {
        if (!isset($key)) {
            return $this->get;
        }

        return !isset($this->get[$key]) ? null : $this->get[$key];
    }

    /**
     * $_POSTの値を取得
     *
     * {@inheritDoc}
     *
     * @see RequestServiceInterface::getPost()
     */
    public function getPost(?string $key = null)
    {
        if (!isset($key)) {
            if (!\in_array($this->getContentType(), ['application/json', 'application/xml'], true)) {
                return $this->post;
            }

            return $this->standardInput;
        }

        return !isset($this->post[$key]) ? null : $this->post[$key];
    }

    /**
     * $_SERVERの値を取得
     *
     * {@inheritDoc}
     *
     * @see RequestServiceInterface::getServer()
     */
    public function getServer(?string $key = null)
    {
        if (!isset($key)) {
            return $_SERVER;
        }

        return !isset($_SERVER[$key]) ? null : $_SERVER[$key];
    }

    /**
     * $_REQUEST の値を取得
     *
     * {@inheritDoc}
     *
     * @see RequestServiceInterface::getRequest()
     */
    public function getRequest(?string $key = null)
    {
        if (!isset($key)) {
            return $_REQUEST;
        }

        return !isset($_REQUEST[$key]) ? null : $_REQUEST[$key];
    }

    /**
     * $_FILES の値を取得
     *
     * @return mixed
     */
    public function getFiles(?string $key = null, ?string $key2 = null)
    {
        if (!isset($key)) {
            return $this->files;
        }

        if (!isset($key2)) {
            return !isset($this->files[$key]) ? null : $this->files[$key];
        }

        return !isset($this->files[$key][$key2]) ? null : $this->files[$key][$key2];
    }

    /**
     * PUTリクエスト（標準入力の内容）を取得
     *
     * {@inheritDoc}
     *
     * @see RequestServiceInterface::getPut()
     */
    public function getPut(): string
    {
        return $this->standardInput;
    }

    /**
     * $_SERVER["CONTENT_TYPE"] から
     * Content-type の最初のセミコロンの前まで（application/json等）を取得して
     * 小文字で返す
     */
    public function getContentType(): string
    {
        $contentTypeCollection = explode(';', $this->getServer('CONTENT_TYPE'));

        return trim(strtolower($contentTypeCollection[0]));
    }
}
