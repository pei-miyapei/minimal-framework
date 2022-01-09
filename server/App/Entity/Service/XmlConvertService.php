<?php

declare(strict_types=1);

namespace App\Entity\Service;

/**
 * XML変換サービス
 */
final class XmlConvertService
{
    /**
     * XML非準拠の制御文字（ASCII 32未満の文字）削除する？
     *
     * @var bool
     */
    public static $isRemoveControlCharacter = false;

    /**
     * XML非準拠の制御文字（ASCII 32未満の文字）置換する？
     * ※ 削除する機能が優先されます
     *
     * @var bool
     */
    public static $isReplaceControlCharacter = true;

    /**
     * Xmlを配列に変換
     * 属性は一切取得しない
     * （convertXmlToArrayのラッパー）
     */
    public static function convertXmlToArrayForValue(string $xml): array
    {
        return self::convertXmlToArray($xml, true, false);
    }

    /**
     * Xmlを配列に変換
     * 値の代わりに属性のみを取得する
     * （ConvertXmlToArrayのラッパー）
     */
    public static function convertXmlToArrayForAttribute(string $xml): array
    {
        return self::convertXmlToArray($xml, false, true);
    }

    /**
     * Xmlを配列に変換
     * CDATAはテキストノードとして取り込む
     *
     * @param bool $isGetValue     true を指定で値を取る
     * @param bool $isGetAttribute true を指定で属性を取る
     *                             両方指定した場合、simplexml_load_stringと同じように
     *                             子要素(タグ)が存在するか値がない要素にのみ属性を入れる
     *                             （処理置き換えなど以外ではいずれかのみの指定を推奨）
     */
    public static function convertXmlToArray(string $xml, bool $isGetValue = true, bool $isGetAttribute = true): ?array
    {
        if (self::$isRemoveControlCharacter) {
            // 制御文字削除機能
            $xml = self::removeControlCharacter($xml);
        } elseif (self::$isReplaceControlCharacter) {
            // 制御文字置換機能
            $xml = self::replaceControlCharacter($xml);
        }

        /*
            simplexml_load_stringでの変換をやめました
            バージョンによって挙動が違ったため。

            PHP5.2ではCDATAが空だったときの値が空要素のような扱い(*)になっていた。
            * [ 0 => [] ] みたいな。
            　期待したのはPHP5.4のように空（[]）

            変換後の内容がPHP5.4の素直な挙動で変換した時と全く同じ結果になるよう、
            DOMDocumentで再現するように変更しました
        */
        $domDocument = new \DOMDocument();
        $domDocument->preserveWhiteSpace = false; // false: 余分な空白を取り除く
        $domDocument->loadXML($xml);

        if ($domDocument->childNodes->length !== 1) {
            return;
        }

        return self::recursiveConvertDomDocumentToArray($domDocument, $isGetValue, $isGetAttribute);
    }

    /** 配列をXmlに変換 */
    public static function convertArrayToXml(array $array): string
    {
        $domDocument = new \DOMDocument('1.0', 'UTF-8');

        foreach ($array as $key => $value) {
            self::recursiveConvertArrayToXml($domDocument, $domDocument, $key, $value);
        }

        $domDocument->formatOutput = true;

        return $domDocument->saveXML();
    }

    /**
     * 引数（配列に変換されたノードの値）が配列でない、
     * または、数字添字を持たない配列の場合、空の配列に入れて返す。
     * そうでない場合は引数をそのまま返却する
     *
     * XMLから変換した際、
     * 要素数が1(0)～n件に変動するノード場合、
     * 単数の場合は直接値（または子ノード）になり
     * 複数の場合は数字添字配列の中に値（または子ノード）という形になる。
     *
     * 単数の場合を複数（数字添字）の形に統一するために使用する。
     */
    public static function convertArrayNodeToPlural(array $arrayNodeValue): array
    {
        if (!\is_array($arrayNodeValue) || !isset($arrayNodeValue[0])) {
            // 引数（配列に変換されたノードの値）が配列でない
            // または、数字添字を持たない配列の場合
            return [$arrayNodeValue];
        }

        return $arrayNodeValue;
    }

    /**
     * 制御文字（ASCII 32未満の文字）を削除する
     * （XML非準拠の文字で変換でエラーになるため、削除する）
     *
     * @param mixed $xml
     */
    public static function removeControlCharacter($xml)
    {
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $xml);
    }

    /**
     * 制御文字（ASCII 32未満の文字）を置換する
     * （XML非準拠の文字で変換でエラーになるため、無害化する）
     *
     * @param string $xml
     */
    public static function replaceControlCharacter($xml): string
    {
        return preg_replace_callback(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',
            fn ($mathDataCollection) => sprintf('[%s]', self::convertToHexadecimal($mathDataCollection[0])),
            $xml
        );
    }

    public static function getDomXPathByDomElement(\DOMElement $domElement): \DOMXPath
    {
        $domDocument = new \DOMDocument('1.0', 'UTF-8');
        $domDocument->preserveWhiteSpace = false; // false: 余分な空白を取り除く
        $domDocument->formatOutput = true;
        // ノードとそのすべての子をインポート
        $cloneNode = $domDocument->importNode($domElement, true);
        // rootノードに追加
        $domDocument->appendChild($cloneNode);

        return new \DOMXPath($domDocument);
    }

    /**
     * DOMDocumentのノードを再帰的に走査し、配列データを作成する
     *
     * @param bool $isGetValue     true を指定で値を取る
     * @param bool $isGetAttribute true を指定で属性を取る
     *                             両方指定した場合、simplexml_load_stringと同じように
     *                             子要素(タグ)が存在するか値がない要素にのみ属性を入れる
     *                             （処理置き換えなど以外ではいずれかのみの指定を推奨）
     *
     * @throws \LogicException
     */
    private static function recursiveConvertDomDocumentToArray(
        \DOMDocument | \DOMElement $node,
        bool $isGetValue = true,
        bool $isGetAttribute = true
    ): ?array {
        if (empty($node->childNodes->length)) {
            return;
        }

        $currentNodeResult = null;

        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof \DOMText) {
                // 子ノードはテキスト。テキストの場合複数は存在しないはず
                if ($isGetValue) {
                    $currentNodeResult = $childNode->wholeText;
                }

                if ($isGetAttribute && !isset($currentNodeResult)) {
                    // テキストノードの場合は、値は取らず属性のみを取るという場合に限り、属性を入れる
                    // ちなみにテキストが空の場合は子要素がないということになり、そもそもここに来ない
                    if (!empty($childNode->attributes->length)) {
                        $childNodeAttribute = [];

                        foreach ($childNode->attributes as $attribute) {
                            $childNodeAttribute[$attribute->name] = $attribute->value;
                        }

                        $currentNodeResult = ['@attributes' => $childNodeAttribute];
                    }
                }
            } elseif ($childNode instanceof \DOMElement) {
                // 子ノードはタグ
                if (!isset($currentNodeResult)) {
                    $currentNodeResult = [];
                }

                // 子ノードの内容
                $childNodeValue = self::recursiveConvertDomDocumentToArray($childNode, $isGetValue, $isGetAttribute);

                // 子ノードの属性
                if ($isGetAttribute && !empty($childNode->attributes->length)) {
                    $childNodeAttribute = [];

                    foreach ($childNode->attributes as $attribute) {
                        $childNodeAttribute[$attribute->name] = $attribute->value;
                    }

                    // simplexml_load_stringと同様、属性を返す場合はそれと分かるように"@attributes"をキーにする
                    $childNodeAttribute = ['@attributes' => $childNodeAttribute];

                    if (
                           !isset($childNodeValue)
                        || (\is_string($childNodeValue) && trim($childNodeValue) === '')
                    ) {
                        // 子要素の内容が無い
                        $childNodeValue = $childNodeAttribute;
                    } elseif (\is_array($childNodeValue)) {
                        // 子要素の内容はあるが、別の要素あるいは要素群
                        $childNodeValue = array_merge($childNodeAttribute, $childNodeValue);
                    }
                }

                // 配置換え。子要素が同名複数になる場合、数字添字の配列にする（simplexml_load_stringで変換したときの再現）
                if (!isset($currentNodeResult[$childNode->tagName])) {
                    // 現在のノード配列に、処理中の子ノードと同名のキーがまだ無い場合は、直接子ノード名に内容を入れる
                    $currentNodeResult[$childNode->tagName] = $childNodeValue;
                } else {
                    // 現在のノード配列に、処理中の子ノードと同名のキーがすでにある場合。
                    // 内容が数字添字の配列になっているかどうかで分岐
                    if (\is_array($currentNodeResult[$childNode->tagName]) && $currentNodeResult[$childNode->tagName] === array_values($currentNodeResult[$childNode->tagName])) {
                        // 内容が数字添字の配列の場合は、末尾に内容のみを追加
                        $currentNodeResult[$childNode->tagName][] = $childNodeValue;
                    } else {
                        // 内容が数字添字の配列でない場合、数字添字の配列にすることで同名のキーを格納する
                        $currentNodeResult[$childNode->tagName] = [
                            $currentNodeResult[$childNode->tagName],
                            $childNodeValue,
                        ];
                    }
                }
            } else {
                // 子ノードは謎
                throw new \LogicException('未対応の形式：'.\get_class($childNode));
            }
        }

        return $currentNodeResult;
    }

    /**
     * 配列要素を再帰的に判断してDOMDocumentにツリー構造を作る
     * 数値キーはスキップし、数値キーの親のキーが複数並んでいる構造に直す
     *
     * ["item" => [0 => "item1", 1 => "item2"] ]
     * → <item>item1</item><item>item2</item>
     *
     * @param \DOMDocument|\DOMElement &$parentDom
     * @param int|string               $key
     * @param array|string             $value
     */
    private static function recursiveConvertArrayToXml(\DOMDocument $domDocument, &$parentDom, $key, $value): void
    {
        $currentElement = null;

        if (!\is_array($value)) {
            // $valueが配列でない = 末端の要素
            // 1件の要素を$parentDomに追加する

            // createElementは "&" のみ置換されない（実体参照とみなされる）ので置換する。
            // XML自体がUTF-8のため固定。UTF-8以外の環境で使用する場合はデータ側を予めUTF-8にしなければならない
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $currentElement = $domDocument->createElement($key, $value);
            $parentDom->appendChild($currentElement);
        } else {
            $myName = __FUNCTION__;

            foreach ($value as $key2 => $value2) {
                if ($key2 === (int) $key2) {
                    // キーが数値
                    self::$myName($domDocument, $parentDom, $key, $value2);
                } else {
                    // キーが数値でない
                    if ($key === '@attributes') {
                        $parentDom->setAttribute($key2, $value2);
                    } else {
                        if (!isset($currentElement)) {
                            $currentElement = $domDocument->createElement($key);
                        }

                        self::$myName($domDocument, $currentElement, $key2, $value2);
                    }
                }
            }

            if (isset($currentElement)) {
                $parentDom->appendChild($currentElement);
            }
        }
    }

    /** 16進数にする */
    private static function convertToHexadecimal(string $text): string
    {
        return '0x'.bin2hex($text);
    }
}
