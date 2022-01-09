# small-framework

元々は完全に趣味開発で作ったもの  
（オレオレフレームワーク…）

実務で組織や環境要因の事実上の制約で  
フレームワークを使用せず素の PHP によるフルスクラッチが主であった際に  
特に DI やアーキテクチャの利用・学習となるべくビジネスロジックに注力できるよう  
あくまで取っ掛かりとして  
オートローダー、DI、エラー制御/ログ記録、簡易 router、画面作成などの枠組みを用意していき  
その後実際に Web システム案件、バッチ、公開 API 等々の作成で使用したり  
既存のシステム内に部分的に配置して利用したもの

### 現在の状態について

Docker 環境、PHP8/8.1 で利用できるよう一応多少調整した状態にしてありますが当面もう使用することもなく、  
PHP7/5 で使用していたため 5 系での補完用のクラスなども一部残っています。  
（fetch 時にモデルへの代入なども行えるようになっていますが  
　 Generator(yield) が使用できないバージョンで PdoStatementIterator クラスで代用していた名残、  
　従来使用できなかった引数定義の未補完や PHP8.1 の Enum ではなく自前 Enum クラスなどの使用など）

### どのように使うか

全てのアクセスをフロントコントローラに集約させ、  
共通処理などを行ったうえで URI によってルーティングを行い  
対象のコントローラへと処理を委譲する形です。

Rewrite で public/index.php にアクセスを集中させる必要があります。  
docker+nginx の場合は設定済み  
Apache 環境では .htaccess や httpd.conf などを用いて Rewrite を仕込む必要があります。  
（.htaccess.example 参照）

利用時には環境に合わせて基本的には \App 以下のみで調整や機能追加を行い、  
汎用的に使えるものは \Core 側を拡張していくといった形で  
様々な用途でのベースにしていました

なお展開後ファイルログ出力にはパーミッションの設定が必要です  
`chown -R www-data:www-data /var/www/server/storage`

Interactor(UseCase)、Repository(DB,API 等)や Presenter(View)を使用した構成を想定していますが  
特に制限になるような実装はしていないため、  
これらの枠組みは必ずしも従わなくても Controller 到達以降はどのように実装することも可能です。

### 実装サンプル

（Docker 環境で実行する場合）  
http://localhost
http://localhost/?a=otherAction

→ \App\IndexController  
→ \App\Feature\Demo\\...

#### CLI

`php /var/www/server/public/index.php /cli/cli_demo 'aaa'`

→ CliDemoController

### 設定

開発・本番など環境ごとの設定を独立して持っておき判別して使用する形  
接続は複数設定可能  
判定方法はロジック次第  
（メタルサーバーなどでの既存環境内での利用が多かったため別途環境判別用ファイルを参照したり、  
　本番や開発環境などのパスの違いで判別するなど）

### SQL について

DB 接続ごとにコネクションクラスがあり、  
それを継承して Repository クラスを定義するような形になります。

PdoConnection(n)Interface クラスを DI で取得することで、  
環境設定ごとに対象の DB に接続する PDO インスタンスを直接取得することもできます。

## 簡易 router

配置したコントローラーのパス（名前空間）をそのままルーティングに使用

App\Controller\PathTo\ExampleController  
→ `/path_to/example?a=action`

a=はアクション（メソッド）の指定  
指定がない場合は index アクションを実行する

ルーティング自体に get post などを判別する機能は無し。  
リクエスト内容(get,post や php 標準出力)自体を取得するクラスはあるため  
必要があればアクション内で判別する

```
$request->getPost('key');
$request->getServer('REQUEST_METHOD');
```

特に制限はしていないので素の PHP での参照もそのまま使用可能

### CLI による実行

`php /var/www/server/public/index.php /path_to/example?a=action 'aaa'`

通常のリクエスト同様、簡易 router のパスで実行可能  
CLI の場合は以降の引数で指定したリクエスト内容を取得可能

```
$request->getArgv(2)
```

## 簡易 View テンプレート機能

Presenter クラスに View クラスを組み込んであり、  
（View クラス直接使用でも可）  
レイアウト用のテンプレートファイルと

```
<body>
    <?php echo $this->generatePartialHtml('header'); ?>
    <?php echo $this->generatePartialHtml('main'); ?>
    <?php echo $this->generatePartialHtml('footer'); ?>
</body>
```

部分コンテンツ用のファイルを生成してページを構築する機能

Presenter クラスでレイアウトを定義し、  
Interactor（UseCase）クラスでコンテンツを定義するような使い方を想定しています

## 名前解決

public/index.php で指定した BaseServerPath を基点とするオートローダーを実装してあり  
名前空間と基点以降のファイル名を一致させたクラスは自動的に読み込む

## 自動 DI

コントローラーのメソッドの実行には自動 DI を組み込んであるため、  
引数に指定したクラスを、そのクラスの引数も含め再帰的に補完してインスタンスを渡す

public/index.php などでも行っているが  
Interface と対応するクラスを定義(bind)することで読み替え・差し替えも可能

## エラーハンドリング

エラーはエラー例外に変換してスローし、  
エラーハンドラ・シャットダウンハンドラでキャッチする

通常では最終的に例外ハンドラ（`\Config\ExceptionHandlerService`）クラスに例外を渡す  
ファイルログやメール送信、DB への書き込みなどを行う

## モデルの生成

テーブル定義からモデルを生成  
`http://localhost/scaffold/create_model?connection=1&table=throwable_logs`  
本番（Production）設定では実行不可

## その他

ファイルログ、DB への例外ログ書き込み、  
コード中に記載したデバッグ情報の保持・出力  
処理時間の計測  
などなどの処理クラスが入っている
