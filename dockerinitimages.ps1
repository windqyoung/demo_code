
# from https://github.com/docker-library/official-images/tree/master/library

$images = @{
	'kd93/pixeladmin' = '1.3'
    adminer = ''

    adoptopenjdk = 'latest hotspot-bionic
    8-hotspot-bionic 8-hotspot
    11-hotspot-bionic 11-hotspot
    13-hotspot-bionic 13-hotspot
    14-hotspot-bionic 14-hotspot
    '

    centos = 'latest, centos8, 8
    centos7, 7
    '

    composer = ''

    debian = 'latest
    buster
    bullseye
    '

    docker = 'latest'

    elasticsearch = '7.6.2'

    gcc = '6
    7
    8
    9
    latest'

    golang = ''

    gradle = ''

    groovy = ''

    haproxy = ''

    'hello-world' = ''

    httpd = ''

    kibana = '7.9.2'

    'kubernetesui/metrics-scraper' = 'v1.0.4'

    'kubernetesui/dashboard' = 'v2.0.1'

    logstash = '7.9.2'

    mariadb = ''

    maven = ''

    memcached = ''

    mongo = ''

    mysql = 'latest 8 5.7 5.6 5.5'

    nginx = ''

    node = ''

    openjdk = '15-jdk, 15
    15-buster
    14, jdk, latest
    11-jdk, 11
    16
    '

    php = 'latest, zts, fpm, apache
    rc-cli, rc-apache, rc-fpm, rc-zts
    7.4, 7
    7.4-apache 7-apache
    7.4-fpm, 7-fpm
    7.4-zts, 7-zts
    7.3 7.3-apache 7.3-fpm 7.3-zts
    7.2-cli, 7.2 7.2-apache 7.2-fpm 7.2-zts
    5.6-cli, 5.6 5.6-apache 5.6-fpm 5.6-zts
    5.4-cli, 5.4 5.4-apache 5.4-fpm
    '

    python = 'latest rc
    3 3.8
    3.7
    3.6
    2
    2.7
    '

    rabbitmq = ''

    redis = 'latest buster
    rc-buster
    5-buster
    6-buster
    '

    redmine = ''

    registry = ''

    ruby = ''

    thrift = ''

    tomcat = 'latest
    8
    9
    10
    '

    ubuntu = 'latest
    bionic
    focal
    '

    zookeeper = 'latest
    3.4
    3.5
    3.6
    '
}


if ($args) {
    $imagesKeys = $args
} else {
    $imagesKeys = $images.Keys
}


do {
    if ($retry) {
        Write-Host -ForegroundColor Red "RETRYING......"
        Start-Sleep 5
    }
    Write-Host -ForegroundColor Yellow "I will pull: $imagesKeys"

    $nextLoopImages = @{}

    $isRetring = $retry

    $retry = $False

    foreach ($name in $imagesKeys) {
        $tagStr = $images[$name]

        if (-not $tagStr) {
            $tagStr = 'latest'
        }

        $tags = $tagStr -split '\s*,\s*|\s+'

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
                    $nextLoopImages[$name] += ',' + $t
                    Write-Host -ForegroundColor Red "run $cmd error, will retry..."
                }
            }
        }
    }

    $images = $nextLoopImages;
    $imagesKeys = $images.Keys

} while ($retry)
