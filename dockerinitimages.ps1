
$images = @{
    adminer = ''

    adoptopenjdk = '12.0.2_10-jdk-hotspot-bionic, 12-jdk-hotspot-bionic, 12-hotspot-bionic, hotspot-bionic 
12.0.2_10-jre-hotspot-bionic, 12-jre-hotspot-bionic'

    centos = ''

    composer = ''

    docker = ''

    gcc = '6.5.0, 6.5, 6
7.4.0, 7.4, 7
8.3.0, 8.3, 8
9.2.0, 9.2, 9, latest'

    golang = ''

    gradle = ''

    groovy = ''

    'hello-world' = ''

    httpd = ''

    mariadb = ''

    maven = ''

    memcached = ''

    mongo = ''

    mysql = ''

    nginx = ''

    node = ''

    openjdk = '14-ea-14-jdk-oraclelinux7, 14-ea-14-oraclelinux7, 14-ea-jdk-oraclelinux7, 14-ea-oraclelinux7, 14-jdk-oraclelinux7, 14-oraclelinux7, 14-ea-14-jdk-oracle, 14-ea-14-oracle, 14-ea-jdk-oracle, 14-ea-oracle, 14-jdk-oracle, 14-oracle
14-ea-12-jdk-alpine3.10, 14-ea-12-alpine3.10, 14-ea-jdk-alpine3.10, 14-ea-alpine3.10, 14-jdk-alpine3.10, 14-alpine3.10, 14-ea-12-jdk-alpine, 14-ea-12-alpine, 14-ea-jdk-alpine, 14-ea-alpine, 14-jdk-alpine, 14-alpine
13-jdk-oraclelinux7, 13-oraclelinux7, 13-jdk-oracle, 13-oracle
12.0.2-jdk-oraclelinux7, 12.0.2-oraclelinux7, 12.0-jdk-oraclelinux7, 12.0-oraclelinux7, 12-jdk-oraclelinux7, 12-oraclelinux7, jdk-oraclelinux7, oraclelinux7, 12.0.2-jdk-oracle, 12.0.2-oracle, 12.0-jdk-oracle, 12.0-oracle, 12-jdk-oracle, 12-oracle, jdk-oracle, oracle
11.0.4-jdk-stretch, 11.0.4-stretch, 11.0-jdk-stretch, 11.0-stretch, 11-jdk-stretch, 11-stretch
11.0.4-jdk-slim-buster, 11.0.4-slim-buster, 11.0-jdk-slim-buster, 11.0-slim-buster, 11-jdk-slim-buster, 11-slim-buster, 11.0.4-jdk-slim, 11.0.4-slim, 11.0-jdk-slim, 11.0-slim, 11-jdk-slim, 11-slim
11.0.4-jre-stretch, 11.0-jre-stretch, 11-jre-stretch
11.0.4-jre-slim-buster, 11.0-jre-slim-buster, 11-jre-slim-buster, 11.0.4-jre-slim, 11.0-jre-slim, 11-jre-slim
8u222-jdk-stretch, 8u222-stretch, 8-jdk-stretch, 8-stretch
8u222-jdk-slim-buster, 8u222-slim-buster, 8-jdk-slim-buster, 8-slim-buster, 8u222-jdk-slim, 8u222-slim, 8-jdk-slim, 8-slim
8u222-jre-stretch, 8-jre-stretch
8u222-jre-slim-buster, 8-jre-slim-buster, 8u222-jre-slim, 8-jre-slim'

    php = '7.4.0RC1-cli-buster, 7.4-rc-cli-buster, rc-cli-buster, 7.4.0RC1-buster, 7.4-rc-buster, rc-buster, 7.4.0RC1-cli, 7.4-rc-cli, rc-cli, 7.4.0RC1, 7.4-rc, rc
7.4.0RC1-apache-buster, 7.4-rc-apache-buster, rc-apache-buster, 7.4.0RC1-apache, 7.4-rc-apache, rc-apache
7.4.0RC1-fpm-buster, 7.4-rc-fpm-buster, rc-fpm-buster, 7.4.0RC1-fpm, 7.4-rc-fpm, rc-fpm
7.4.0RC1-zts-buster, 7.4-rc-zts-buster, rc-zts-buster, 7.4.0RC1-zts, 7.4-rc-zts, rc-zts
7.4.0RC1-cli-alpine3.10, 7.4-rc-cli-alpine3.10, rc-cli-alpine3.10, 7.4.0RC1-alpine3.10, 7.4-rc-alpine3.10, rc-alpine3.10, 7.4.0RC1-cli-alpine, 7.4-rc-cli-alpine, rc-cli-alpine, 7.4.0RC1-alpine, 7.4-rc-alpine, rc-alpine
7.4.0RC1-fpm-alpine3.10, 7.4-rc-fpm-alpine3.10, rc-fpm-alpine3.10, 7.4.0RC1-fpm-alpine, 7.4-rc-fpm-alpine, rc-fpm-alpine
7.4.0RC1-zts-alpine3.10, 7.4-rc-zts-alpine3.10, rc-zts-alpine3.10, 7.4.0RC1-zts-alpine, 7.4-rc-zts-alpine, rc-zts-alpine
7.3.9-cli-buster, 7.3-cli-buster, 7-cli-buster, cli-buster, 7.3.9-buster, 7.3-buster, 7-buster, buster, 7.3.9-cli, 7.3-cli, 7-cli, cli, 7.3.9, 7.3, 7, latest
7.3.9-apache-buster, 7.3-apache-buster, 7-apache-buster, apache-buster, 7.3.9-apache, 7.3-apache, 7-apache, apache
7.3.9-fpm-buster, 7.3-fpm-buster, 7-fpm-buster, fpm-buster, 7.3.9-fpm, 7.3-fpm, 7-fpm, fpm
7.3.9-zts-buster, 7.3-zts-buster, 7-zts-buster, zts-buster, 7.3.9-zts, 7.3-zts, 7-zts, zts
7.3.9-cli-stretch, 7.3-cli-stretch, 7-cli-stretch, cli-stretch, 7.3.9-stretch, 7.3-stretch, 7-stretch, stretch
7.3.9-apache-stretch, 7.3-apache-stretch, 7-apache-stretch, apache-stretch
7.3.9-fpm-stretch, 7.3-fpm-stretch, 7-fpm-stretch, fpm-stretch
7.3.9-zts-stretch, 7.3-zts-stretch, 7-zts-stretch, zts-stretch
7.3.9-cli-alpine3.10, 7.3-cli-alpine3.10, 7-cli-alpine3.10, cli-alpine3.10, 7.3.9-alpine3.10, 7.3-alpine3.10, 7-alpine3.10, alpine3.10, 7.3.9-cli-alpine, 7.3-cli-alpine, 7-cli-alpine, cli-alpine, 7.3.9-alpine, 7.3-alpine, 7-alpine, alpine
7.3.9-fpm-alpine3.10, 7.3-fpm-alpine3.10, 7-fpm-alpine3.10, fpm-alpine3.10, 7.3.9-fpm-alpine, 7.3-fpm-alpine, 7-fpm-alpine, fpm-alpine
7.3.9-zts-alpine3.10, 7.3-zts-alpine3.10, 7-zts-alpine3.10, zts-alpine3.10, 7.3.9-zts-alpine, 7.3-zts-alpine, 7-zts-alpine, zts-alpine
7.3.9-cli-alpine3.9, 7.3-cli-alpine3.9, 7-cli-alpine3.9, cli-alpine3.9, 7.3.9-alpine3.9, 7.3-alpine3.9, 7-alpine3.9, alpine3.9
7.3.9-fpm-alpine3.9, 7.3-fpm-alpine3.9, 7-fpm-alpine3.9, fpm-alpine3.9
7.3.9-zts-alpine3.9, 7.3-zts-alpine3.9, 7-zts-alpine3.9, zts-alpine3.9
7.2.22-cli-buster, 7.2-cli-buster, 7.2.22-buster, 7.2-buster, 7.2.22-cli, 7.2-cli, 7.2.22, 7.2
7.2.22-apache-buster, 7.2-apache-buster, 7.2.22-apache, 7.2-apache
7.2.22-fpm-buster, 7.2-fpm-buster, 7.2.22-fpm, 7.2-fpm
7.2.22-zts-buster, 7.2-zts-buster, 7.2.22-zts, 7.2-zts
7.2.22-cli-stretch, 7.2-cli-stretch, 7.2.22-stretch, 7.2-stretch
7.2.22-apache-stretch, 7.2-apache-stretch
7.2.22-fpm-stretch, 7.2-fpm-stretch
7.2.22-zts-stretch, 7.2-zts-stretch
7.2.22-cli-alpine3.10, 7.2-cli-alpine3.10, 7.2.22-alpine3.10, 7.2-alpine3.10, 7.2.22-cli-alpine, 7.2-cli-alpine, 7.2.22-alpine, 7.2-alpine
7.2.22-fpm-alpine3.10, 7.2-fpm-alpine3.10, 7.2.22-fpm-alpine, 7.2-fpm-alpine
7.2.22-zts-alpine3.10, 7.2-zts-alpine3.10, 7.2.22-zts-alpine, 7.2-zts-alpine
7.2.22-cli-alpine3.9, 7.2-cli-alpine3.9, 7.2.22-alpine3.9, 7.2-alpine3.9
7.2.22-fpm-alpine3.9, 7.2-fpm-alpine3.9
7.2.22-zts-alpine3.9, 7.2-zts-alpine3.9
7.1.32-cli-buster, 7.1-cli-buster, 7.1.32-buster, 7.1-buster, 7.1.32-cli, 7.1-cli, 7.1.32, 7.1
7.1.32-apache-buster, 7.1-apache-buster, 7.1.32-apache, 7.1-apache
7.1.32-fpm-buster, 7.1-fpm-buster, 7.1.32-fpm, 7.1-fpm
7.1.32-zts-buster, 7.1-zts-buster, 7.1.32-zts, 7.1-zts
7.1.32-cli-stretch, 7.1-cli-stretch, 7.1.32-stretch, 7.1-stretch
7.1.32-apache-stretch, 7.1-apache-stretch
7.1.32-fpm-stretch, 7.1-fpm-stretch
7.1.32-zts-stretch, 7.1-zts-stretch
7.1.32-cli-alpine3.10, 7.1-cli-alpine3.10, 7.1.32-alpine3.10, 7.1-alpine3.10, 7.1.32-cli-alpine, 7.1-cli-alpine, 7.1.32-alpine, 7.1-alpine
7.1.32-fpm-alpine3.10, 7.1-fpm-alpine3.10, 7.1.32-fpm-alpine, 7.1-fpm-alpine
7.1.32-zts-alpine3.10, 7.1-zts-alpine3.10, 7.1.32-zts-alpine, 7.1-zts-alpine
7.1.32-cli-alpine3.9, 7.1-cli-alpine3.9, 7.1.32-alpine3.9, 7.1-alpine3.9
7.1.32-fpm-alpine3.9, 7.1-fpm-alpine3.9
7.1.32-zts-alpine3.9, 7.1-zts-alpine3.9'

    python = '3.8.0b4-buster, 3.8-rc-buster, rc-buster
3.8.0b4-slim-buster, 3.8-rc-slim-buster, rc-slim-buster, 3.8.0b4-slim, 3.8-rc-slim, rc-slim
3.8.0b4-alpine3.10, 3.8-rc-alpine3.10, rc-alpine3.10, 3.8.0b4-alpine, 3.8-rc-alpine, rc-alpine
3.7.4-buster, 3.7-buster, 3-buster, buster
3.7.4-slim-buster, 3.7-slim-buster, 3-slim-buster, slim-buster, 3.7.4-slim, 3.7-slim, 3-slim, slim
3.7.4-stretch, 3.7-stretch, 3-stretch, stretch
3.7.4-slim-stretch, 3.7-slim-stretch, 3-slim-stretch, slim-stretch
3.7.4-alpine3.10, 3.7-alpine3.10, 3-alpine3.10, alpine3.10, 3.7.4-alpine, 3.7-alpine, 3-alpine, alpine
3.7.4-alpine3.9, 3.7-alpine3.9, 3-alpine3.9, alpine3.9
3.6.9-buster, 3.6-buster
3.6.9-slim-buster, 3.6-slim-buster, 3.6.9-slim, 3.6-slim
3.6.9-stretch, 3.6-stretch
3.6.9-slim-stretch, 3.6-slim-stretch
3.6.9-alpine3.10, 3.6-alpine3.10, 3.6.9-alpine, 3.6-alpine
3.6.9-alpine3.9, 3.6-alpine3.9
3.5.7-buster, 3.5-buster
3.5.7-slim-buster, 3.5-slim-buster, 3.5.7-slim, 3.5-slim
3.5.7-stretch, 3.5-stretch
3.5.7-slim-stretch, 3.5-slim-stretch
3.5.7-alpine3.10, 3.5-alpine3.10, 3.5.7-alpine, 3.5-alpine
3.5.7-alpine3.9, 3.5-alpine3.9
2.7.16-buster, 2.7-buster, 2-buster
2.7.16-slim-buster, 2.7-slim-buster, 2-slim-buster, 2.7.16-slim, 2.7-slim, 2-slim
2.7.16-stretch, 2.7-stretch, 2-stretch
2.7.16-slim-stretch, 2.7-slim-stretch, 2-slim-stretch
2.7.16-alpine3.10, 2.7-alpine3.10, 2-alpine3.10, 2.7.16-alpine, 2.7-alpine, 2-alpine
2.7.16-alpine3.9, 2.7-alpine3.9, 2-alpine3.9
'

    rabbitmq = ''

    redis = ''

    redmine = ''

    ruby = ''

    thrift = ''

    ubuntu = ''

    wordpress = ''



}


if ($args) {
    $imagesKeys = $args
} else {
    $imagesKeys = $images.Keys
}

Write-Host -ForegroundColor Yellow "I will pull: $imagesKeys"

do {

    if ($retry) {
        Write-Host -ForegroundColor Red "RETRYING ......................."
        sleep 5
    }

    $isRetring = $retry

    $retry = $False

    foreach ($name in $imagesKeys) {
        $tagStr = $images[$name]

        if (-not $tagStr) {
            $tagStr = 'latest'
        }

        $tags = $tagStr -split ',|\s+'

        foreach ($t in $tags) {
            if ($t) {
                $cmd = "docker pull ${name}:${t}"
                Write-Host -ForegroundColor Blue -NoNewLine $cmd 
                if ($isRetring) { 
                    Write-Host -ForegroundColor Red ' # retry' 
                } else {
                    Write-Host
                }
                docker pull ${name}:${t}

                if (-not $?) {
                    $retry = $True
                    Write-Host -ForegroundColor Red "run $cmd error, will retry..."
                }
            }
        }
    }

} while ($retry)
