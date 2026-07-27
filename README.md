# eccube-plugin-test

## Usage

```yaml
name: Plugin test for EC-CUBE

on:
  pull_request:
  workflow_dispatch:

env:
  PLUGIN_CODE: plugin-code
  PLUGIN_PACKAGE_NAME: 'eccube/plugin-package-name'

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        eccube-versions: [ '4.2', '4.3' ]
        php-versions: [ '7.4', '8.0', '8.1', '8.2', '8.3' ]
        database: [ 'mysql', 'mysql8', 'pgsql' ]
        include:
          - database: mysql
            database_url: mysql://root:password@127.0.0.1:3306/eccube_db
            database_server_version: 5.7
            database_charset: utf8mb4
          - database: mysql8
            database_url: mysql://root:password@127.0.0.1:3308/eccube_db
            database_server_version: 8
            database_charset: utf8mb4
          - database: pgsql
            database_url: postgres://postgres:password@127.0.0.1:5432/eccube_db
            database_server_version: 14
            database_charset: utf8
        exclude:
          - eccube-versions: 4.2
            php-versions: 8.2
          - eccube-versions: 4.2
            php-versions: 8.3
          - eccube-versions: 4.3
            php-versions: 7.4
          - eccube-versions: 4.3
            php-versions: 8.0            

    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: password
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
      mysql8:
        image: mysql:8
        env:
          MYSQL_ROOT_PASSWORD: password
        ports:
          - 3308:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
      postgres:
        image: postgres:11
        env:
          POSTGRES_USER: postgres
          POSTGRES_PASSWORD: password
        ports:
          - 5432:5432
        # needed because the postgres container does not provide a healthcheck
        options: --health-cmd pg_isready --health-interval 10s --health-timeout 5s --health-retries 5

    steps:
      - name: Checkout
        uses: actions/checkout@v4
        with:
          ref: ${{ github.event.pull_request.head.sha }}

      - uses: kurozumi/eccube-plugin-test@main
        with:
          plugin-code: ${{ env.PLUGIN_CODE }}
          plugin-package-name: ${{ env.PLUGIN_PACKAGE_NAME }}
```
## 入力

| 入力 | 既定 | 説明 |
|------|------|------|
| `plugin-code` | （必須） | プラグインコード |
| `plugin-package-name` | （必須） | composer のパッケージ名 |
| `working-directory` | `ec-cube` | EC-CUBE を配置するディレクトリ |
| `plugin-directory` | `./` | プラグインのディレクトリ |
| `plugin-archive` | `true` | プラグインを tgz にして mock-package-api へ置く |
| `pre-composer-install` | | `composer install` の前に実行するコマンド |
| `dependency-plugins-enable` | | 依存プラグインを有効化するコマンド |
| `dependency-plugins-disable` | | 依存プラグインを無効化するコマンド |
| `composer-version` | `v2` | composer のメジャーバージョン |
| `app-env` | `test` | `APP_ENV` |
| `app-debug` | `0` | `APP_DEBUG` |
| `core-tests` | | プラグインを有効にしたまま実行する本体テスト |

### core-tests

プラグインを有効にした状態で、EC-CUBE 本体のテストを実行します。プラグインが
本体の挙動を壊していないかを見るためのものです。

```yaml
      - uses: kurozumi/eccube-plugin-test@main
        with:
          plugin-code: ${{ env.PLUGIN_CODE }}
          plugin-package-name: ${{ env.PLUGIN_PACKAGE_NAME }}
          core-tests: >-
            tests/Eccube/Tests/Web/ShoppingControllerTest.php
            tests/Eccube/Tests/Web/CartValidationTest.php
```

EC-CUBE 4.3 までは、本体のテストクラスを継承した空のクラスをプラグイン側に置く
ことで同じことができました。4.4 で本体のテストクラスがすべて `final` になり
継承できなくなったため、この入力で代替します。
