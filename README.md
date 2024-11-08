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

      - uses: kurozumi/eccube-plugin-test
        with:
          plugin-code: ${{ env.PLUGIN_CODE }}
          plugin-package-name: ${{ env.PLUGIN_PACKAGE_NAME }}
          eccube-versions: ${{ matrix.eccube-versions }}
          php-versions: ${{ matrix.php-versions }}
```