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
### 1. リポジトリをクローン
- 任意のディレクトリで以下のコマンドを実行
  ```shell
  $ git clone git@github.com:YawarakaJuku/yawaraka-google-the-image.git
  ```

### 2. Dockerイメージをビルドして起動
- 以下のコマンドを実行
  ```shell
  $ cd yawaraka-google-the-image
  $ docker compose up -d --build
  ```

### ngrok を起動
- 以下のコマンドを実行
  ```shell
  $ docker compose exec ngrok ngrok authtoken xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx --config /etc/ngrok.yml
  $ docker compose exec ngrok ngrok http httpd:80 --config /etc/ngrok.yml
  ```
  + `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` は、ngrok のアカウントページから取得したトークンを入力してください。
    * [ngrok アカウントページ](https://dashboard.ngrok.com/get-started/your-authtoken)


## 開発の手順
### 開発開始時に行う手順
- 以下のコマンドを実行
  ```shell
  $ docker compose up -d
  $ docker compose exec ngrok ngrok http httpd:80 --config /etc/ngrok.yml
  ```

### 開発終了時に行う手順
- 以下のコマンドを実行
  ```shell
  $ docker compose down
  ```



