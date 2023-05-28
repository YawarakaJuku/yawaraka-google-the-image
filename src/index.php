<?php

// LIME Messaging API へ送られてきた情報を取得する
$json = file_get_contents("php://input");

// LIME Messaging API へ送られてきた情報をデコードする
$data = json_decode($json, true);
if (count($data["events"]) === 0) {
    exit(); // 何も入ってなければとりあえずそこで試合終了
}

$access_token = getenv("CHANNEL_ACCESS_TOKEN");

// Event は複数飛んでくることがあるようなのでループで処理
// https://developers.line.biz/ja/reference/messaging-api/#webhook-event-objects
foreach ($data["events"] as $event) {

    // LINE が固有で持っている userId
    // 友だち追加などで使用する LINE ID とは別物
    // こっちの LINE ID は "Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" と言う33文字で構成される
    $line_id = $event["source"]["userId"];

    // LINEメッセージの場合は $event["message"]["type"]、そうでなければ $event["type"] に種別が入っている
    // 種別は、エンドユーザーが文字を送ったら text、画像を送ったら image が入っていると言うような感じ
    $type = $event["message"]["type"] ?? $event["type"];

    // エンドユーザーにリプライメッセージを返すために必要になるトークン
    // 例えば unfollow（ブロックされた）場合のようにリプライメッセージを送れない場合は replyToken は入ってない
    $reply_token = $event["replyToken"] ?? "";

    // メッセージ種別によって処理を振り分ける
    switch ($type) {
        case "text":
            // テキストが送信された際に飛んでくるメッセージ
            // https://developers.line.biz/ja/reference/messaging-api/#wh-text
            $text = $event["message"]["text"];

            // テキストをキーワードにして Google 検索をする場合
            $keyword = urlencode($text); // まずはURLエンコードを行う
            $url = "https://www.google.com/search?q=$keyword&tbm=isch"; // Google 検索アドレスへキーワードを付与する
            // 上記の $url を LINE で送信すると、LINE が自動的にリンクを生成してくれる

            // リプライするメッセージを作成する
            // 今回扱うのは LINE Messaging API の用語で言えば「テキストメッセージ」
            // https://developers.line.biz/ja/reference/messaging-api/#text-message
            $message_01 = [
                "type" => "text",
                "text" => $text, // オウム返しする
            ];

            // メッセージの配列（1個～5個まで）を作成する
            // LINE Messaging API の用語で言えば「メッセージオブジェクト」
            // https://developers.line.biz/ja/reference/messaging-api/#message-objects
            $messages = [
                $message_01, // ここではメッセージはひとつだけ
                // メッセージを２つ以上返してみたければこ配列に追加する感じになる
                // LINE Messaging API の仕様上、メッセージは最大で５つまで
            ];

            // 作成したメッセージをリプライするためのリクエストボディを作成する
            $data = [
                "replyToken" => $reply_token,
                "messages" => $messages,
            ];

            // 以下はある程度お決まりの処理
            // https://developers.line.biz/ja/reference/messaging-api/#send-reply-message
            $curl = curl_init("https://api.line.me/v2/bot/message/reply"); // LINE Messaging API のリプライ用のURL
            curl_setopt($curl, CURLOPT_POST, true); // POST することを明示
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); // POST することを明示
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 結果を受け取る設定
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // POST するデータをセット
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json; charset=UTF-8", // POST する内容は JSON であることを指定
                "Authorization: Bearer $access_token", // LINE Messaging API のアクセストークン
            ]);
            $result = curl_exec($curl); // HTTP リクエストの実行と結果の取得
            curl_close($curl); // 終了処理

            // 結果をログに残してみる
            $log = "log/" . date("Y-m-d_H-i-s") . ".log";
            file_put_contents($log, $result);

            break;
        case "image":
            // 画像が送信された際に飛んでくるメッセージ
            // この時点では画像は取得できず、画像を取得するための id のみ取得できる
            $content_id = $event["message"]["id"];



            // コンテンツIDから画像を取得する
            // file_get_contents で取得する場合は、HTTPヘッダーにアクセストークンを付与する必要がある
            $header = [
                "Authorization: Bearer $access_token",
            ];
            // HTTPヘッダーを指定して file_get_contents を実行する
            $context = [
                "http" => [
                    "method" => "GET",
                    "header" => implode("\\r\\n", $header),
                    "ignore_errors" => true,
                ],
            ];
            // file_get_contents で画像を取得する
            $image_file = file_get_contents(
                "https://api-data.line.me/v2/bot/message/$content_id/content",
                false,
                stream_context_create($context));
            // $image_file に画像ファイルの内容が入るので、そのままファイル保存すれば画像ファイルとして扱える



            // 画像を保存する
            // 保存先のファイル名は、LINE ID と送信された日時を組み合わせて作成する
            // LINEの場合は、画像形式は送信元がどうであれ必ず JPEG になる
            $filename = "img/$line_id-" . date("Y-m-d_H-i-s") . ".jpg";
            file_put_contents($filename, $image_file);



            // リプライするメッセージを作成する
            // 画像のURLを指定して Google 画像検索をする URL を作成する
            $domain = $_SERVER["HTTP_HOST"]; // ngrok で作成したドメインや、レンタルサーバーのドメインなどを指定する
            $image_url = "https://$domain/$filename"; // LINE Messaging API は https しか扱えないので http は考えない

            // Google 画像検索を使用する
            $url_01 = "https://www.google.com/searchbyimage?sbisrc=4chanx&image_url=$image_url&safe=off";

            // リプライするメッセージを作成する
            $message_01 = [
                "type" => "text",
                "text" => $url_01, // 生成した URL を文字列として返す
            ];

            // メッセージオブジェクトを作成する
            $messages = [
                $message_01,
            ];

            // 作成したメッセージをリプライするためのリクエストボディを作成する
            $data = [
                "replyToken" => $reply_token,
                "messages" => $messages,
            ];

            // 以下はある程度お決まりの処理
            // テキストメッセージで行っているものを同じことを行う
            $curl = curl_init("https://api.line.me/v2/bot/message/reply"); // LINE Messaging API のリプライ用のURL
            curl_setopt($curl, CURLOPT_POST, true); // POST することを明示
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); // POST することを明示
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 結果を受け取る設定
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // POST するデータをセット
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json; charset=UTF-8", // POST する内容は JSON であることを指定
                "Authorization: Bearer $access_token", // LINE Messaging API のアクセストークン
            ]);
            $result = curl_exec($curl); // HTTP リクエストの実行と結果の取得
            curl_close($curl); // 終了処理

            // 結果をログに残してみる
            $log = "log/" . date("Y-m-d_H-i-s") . ".log";
            file_put_contents($log, $result);

            break;
        case "video":
        case "audio":
        case "file":
        case "location":
        case "sticker":
        case "unsend":
        case "follow":
        case "unfollow":
        case "join":
        case "leave":
        case "memberJoined":
        case "memberLeft":
        case "postback":
        case "beacon":
            // 今回は、テキストと画像以外は何もしない
            break;
    }
}
