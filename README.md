## 構成
client - APIを実行するためのUI側のソースコード  
server - APIそのもののソースコード

### ローカルAPIサーバーを立てる
```
$ php -S localhost:8080 -t .\server\public\
```

Postmanで `http://localhost:8080/` にリクエストを送信