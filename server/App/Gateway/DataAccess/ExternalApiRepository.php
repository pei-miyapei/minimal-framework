<?php

declare(strict_types=1);

namespace App\Gateway\DataAccess;

use App\Entity\Model\ApiResponseModel;

/** 外部API */
abstract class ExternalApiRepository
{
    /**
     * APIリクエスト時のコールバック
     *
     * 起動タイミングはAPIレスポンス受け取り直後
     *
     * @param string           $url
     * @param array            $httpHeader
     * @param ?string          $postData
     * @param ApiResponseModel $responseModel
     *
     * @var ?callable
     */
    public $callbackForRequest;

    protected string $apiBaseUrl = '';

    /** 秒間実行制限用 */
    protected int $lastRequestTime = 0;

    public function __construct(string $apiBaseUrl)
    {
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /** 秒間実行制限用 */
    protected function sleepToLimitRequest(int $interval = 1): void
    {
        if (empty($this->lastRequestTime)) {
            return;
        }

        // floatの比較はうまくいかないが文字列で比較するのもなんか嫌なので秒で
        // 最後の実行時間 + $interval 秒から現在時刻を引く（ $interval 秒以上経過していれば0以下になる）
        $tempTime = $this->lastRequestTime + $interval - microtime(true);

        if ($tempTime > 0) {
            // 残り時間分sleepする
            usleep($tempTime * 1000000);
        }
    }

    /**
     * API リクエスト
     *
     * @param resource $curlResource
     *
     * @throws \Exception
     */
    protected function apiRequest($curlResource, array $httpHeader, string $data = ''): ApiResponseModel
    {
        $responseModel = new ApiResponseModel();
        $throwable = null;
        $url = null;

        try {
            $url = curl_getinfo($curlResource, CURLINFO_EFFECTIVE_URL);
            //var_dump($url);
            //var_dump($data);

            // curl
            curl_setopt_array(
                $curlResource,
                [
                    CURLOPT_RETURNTRANSFER => true,        // 結果を文字列として受け取る
                    CURLOPT_HTTPHEADER => $httpHeader,
                    CURLOPT_HEADER => true,        // ヘッダの内容も出力（ログ・調査用）
                ]
            );

            $responseModel->body = curl_exec($curlResource);
            $curlInfo = curl_getinfo($curlResource);

            //var_dump($httpHeader);
            //var_dump($curlInfo);
            //var_dump($responseModel->body);

            $responseModel->httpCode = $curlInfo['http_code'];

            // レスポンスヘッダと内容を分解
            $position = $curlInfo['header_size'];
            $responseModel->header = preg_split("/\r\n|\r|\n/", substr($responseModel->body, 0, $position));
            $responseModel->body = substr($responseModel->body, $position);

            //var_dump($responseModel);
            //var_dump(curl_errno($curlResource));

            if (curl_errno($curlResource) > 0) {
                throw new \Exception(curl_error($curlResource));
            }

            // 最後の実行時間を記録（秒間実行制限用）
            $this->lastRequestTime = microtime(true);

            curl_close($curlResource);
        } catch (\Throwable $throwable) {
            // 何か起きてもとりあえずコールバックにはたどり着けるようにキャッチして継続させる
            // （致命的なエラーのときは無理）
        }

        // 応答受け取り直後のコールバック
        if (\is_callable($this->callbackForRequest)) {
            \call_user_func_array(
                $this->callbackForRequest,
                [$url, $httpHeader, $data, $responseModel]
            );
        }

        if (!empty($throwable)) {
            throw $throwable;
        }

        return $responseModel;
    }

    /**
     * For safe multipart POST request for PHP5.3 ~ PHP 5.4.
     *
     * @param array $requestData  "name => value"
     * @param array $requestFiles "name => path"
     *
     * @return list($boundary, $messageBody)
     */
    protected function getCustomMessageBody(array $requestData = [], array $requestFiles = [])
    {
        // "name" と "filename" で無効な文字
        static $InvalidCharacters = ["\0", '"', "\r", "\n"];

        // 通常のパラメーターをビルド
        $messageBody = $this->buildNormalParameters($InvalidCharacters, $requestData);

        // ファイルのパラメーターをビルド
        foreach ($requestFiles as $key => $filePath) {
            switch (true) {
                case false === $filePath = realpath((string) $filePath):
                case !is_file($filePath):
                case !is_readable($filePath):
                    throw new \InvalidArgumentException('無効なファイル');
            }

            $data = file_get_contents($filePath);
            $fileName = \call_user_func('end', explode(\DIRECTORY_SEPARATOR, $filePath));

            $messageBody[] = implode("\r\n", [
                sprintf(
                    'Content-Disposition: form-data; name="%s"; filename="%s"',
                    str_replace($InvalidCharacters, '_', $key),
                    str_replace($InvalidCharacters, '_', $fileName)
                ),
                'Content-Type: application/octet-stream',
                '',
                $data,
            ]);
        }

        // 安全な境界線を生成する
        // boundaryと同じ文字が含まれている場合、生成しなおす
        do {
            $boundary = '---------------------'.md5(mt_rand().microtime());
        } while (preg_grep("/{$boundary}/", $messageBody));

        // 各パラメータに境界線を追加
        foreach ($messageBody as $key => $part) {
            $messageBody[$key] = sprintf("--%s\r\n%s", $boundary, $part);
        }

        // add final boundary
        $messageBody[] = "--{$boundary}--";
        $messageBody[] = '';

        return [$boundary, implode("\r\n", $messageBody)];
    }

    private function buildNormalParameters(array $InvalidCharacters, array $requestData, array $messageBody = [], string $parent = ''): string
    {
        foreach ($requestData as $key => $value) {
            $key = str_replace($InvalidCharacters, '_', $key);
            $key = empty($parent) ? $key : sprintf('%s[%s]', $parent, $key);

            if (\is_array($value)) {
                $messageBody = $this->buildNormalParameters($value, $messageBody, $key);
            } else {
                $messageBody[] = implode("\r\n", [
                    sprintf('Content-Disposition: form-data; name="%s"', $key),
                    '',
                    (string) $value,
                ]);
            }
        }

        return $messageBody;
    }
}
