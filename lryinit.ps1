function replace($file, $map)
{
    if (!(Test-Path $file -PathType Leaf))
    {
        return # 非文件
    }
    $content = Get-Content $file -Raw

    $map.GetEnumerator() | foreach {
        $content = $content.replace($_.key, $_.value)
    }

    set-content $file $content
}

function replace_env($db)
{
    if (! $db) {
        $db = 'lry'
    }

    $map = @{
        'APP_URL=http://localhost' = 'APP_URL=http://lry.my';
        'DB_DATABASE=homestead' = 'DB_DATABASE=' + $db;
        'DB_USERNAME=homestead' = 'DB_USERNAME=root';
        'DB_PASSWORD=secret' = 'DB_PASSWORD=1234';
        'CACHE_DRIVER=file' = 'CACHE_DRIVER=database';
        'SESSION_DRIVER=file' = 'SESSION_DRIVER=database';
        'QUEUE_DRIVER=sync' = 'QUEUE_DRIVER=database';
    }
    replace .env $map
    Write-Host .env replaced
}

function replace_config()
{
    $map = @{
        "'timezone' => 'UTC'" = "'timezone' => 'Asia/Shanghai'";
        "'locale' => 'en'" = "'locale' => 'zh'";
    }
    replace config/app.php $map
    Write-Host config/app.php replaced
}

function add_gitignore()
{
    add-content .gitignore @'
/.settings/
/.buildpath
/.project
TestController.php
/storage/debugbar/
'@
    Write-Host .gitignore appended
}

function add_routes()
{
    add-content routes\web.php @'

Route::get('/test/foo', [
    'as' => 'test.foo',
    'uses' => 'TestController@foo',
]);

Route::get('/test/bar', [
    'as' => 'test.bar',
    'uses' => 'TestController@bar',
]);

'@
    Write-Host routes\web.php appended
}

function create_test()
{
    $testctrl = 'app\Http\Controllers\TestController.php'
    new-item -path $testctrl -itemtype file
    set-content $testctrl @'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function foo(Request $req)
    {
        return __METHOD__;
    }

    public function bar(Request $req)
    {
        return view('test.bar');
    }

}


'@
    Write-Host $testctrl created and set content

    $viewDir = 'resources/views/test'
    if (!(test-path $viewDir)) {
        mkdir $viewDir
        Write-Host $viewDir created
    }
    $barFile = join-path $viewDir 'bar.blade.php'
    Set-Content $barFile @'
<h1>Helo</h1>
'@

    Write-Host $barFile created

}

$db = read-host "数据库名(lry)"
if (! $db) {
    $db = 'lry'
}


replace_env $db
replace_config
add_gitignore
add_routes
create_test


composer require barryvdh/laravel-ide-helper barryvdh/laravel-debugbar doctrine/dbal


Write-Output "create database $db ;" | mysql

php artisan ide-helper:model -WR
php artisan ide-helper:generate

php artisan cache:table
php artisan notifications:table
php artisan queue:failed-table
php artisan queue:table
php artisan session:table

php artisan migrate
