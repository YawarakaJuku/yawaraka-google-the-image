# やわらか画像検索くん
## 概要
- 画像検索を行うLINE公式アカウントです。
- 画像を送信すると、その画像に似た画像を返信します。

# 開発環境
## 必要なもの
### アプリケーション
- Docker Desktop
  + [Docker Desktop for Windows](https://docs.docker.com/docker-for-windows/install/)
  + [Docker Desktop for Mac](https://docs.docker.com/docker-for-mac/install/)
  + [Docker Desktop for Linux](https://docs.docker.com/desktop/install/linux-install/)
- Visual Studio Code
  + [Visual Studio Code](https://code.visualstudio.com/)
- Git
  + [Git](https://git-scm.com/downloads)

### サービス
- ngrok
  + [ngrok](https://ngrok.com/)
    - 無料アカウントで問題ないです。


## 環境構築
### 1. Dockerイメージをビルドして起動
- 以下のコマンドを実行
  ```shell
  $ docker compose up -d --build
  ```

### 2. 設定ファイルを作成
- 以下のコマンドを実行
  ```shell
  $ docker compose exec ngrok cp .env.example .env
  ```

### 3. ngrok のアクセストークンを .env ファイルに設定
- NGROK_AUTHTOKEN にアクセストークンを設定する
  + `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` は、ngrok のアカウントページから取得したトークンを入力してください。
    * [ngrok アカウントページ](https://dashboard.ngrok.com/get-started/your-authtoken)

### 4. LINE Messaging API のチャネルアクセストークンを .env に設定
- CHANNEL_ACCESS_TOKEN にアクセストークンを設定する
  + `xxxx .... xxxx` は、LINE Developers から取得できます。
    * [LINE Developers](https://developers.line.biz/console/)

### 5. 公開ページへのアクセスを確認
- [http://localhost:4040](http://localhost:4040)
  + 上記にアクセスして公開ページを確認
    * https://xxxx-000-000-000-000.ngrok-free.app のようなアドレスになる

### 6. LINE Developers ページの Webhook を設定して疎通確認を行う
- 省略

### 7. Docker コンテナを停止
- 以下のコマンドを実行
  ```shell
  $ docker compose down
  ```
    + 以降は、開発開始時に行う手順を実行してください。


## 開発の手順
### 開発開始時に行う手順
- 以下のコマンドを実行
  ```shell
  $ docker compose up -d
  $ docker compose exec php php .htngrok.php
  ```
  + ngrok が毎回生成するランダムなURLを LINE Messaging API の Webhook URL に自動設定する

### 開発終了時に行う手順
- 以下のコマンドを実行
  ```shell
  $ docker compose down
  ```


