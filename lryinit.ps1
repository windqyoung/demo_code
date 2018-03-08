function replace($file, $map)
{
    if (!(Test-Path $file -PathType Leaf))
    {
        return # ·ÇÎÄ¼þ
    }
    $content = Get-Content $file -Raw

    $map.GetEnumerator() | foreach {
        $content = $content.replace($_.key, $_.value)
    }

    set-content $file $content

}

function replaceFiles()
{
    $map = @{
        'APP_URL=http://localhost' = 'APP_URL=http://lry.my';
        'DB_DATABASE=homestead' = 'DB_DATABASE=lry';
        'DB_USERNAME=homestead' = 'DB_USERNAME=root';
        'DB_PASSWORD=secret' = 'DB_PASSWORD=1234';
        'CACHE_DRIVER=file' = 'CACHE_DRIVER=database';
        'SESSION_DRIVER=file' = 'SESSION_DRIVER=database';
        'QUEUE_DRIVER=sync' = 'QUEUE_DRIVER=database';
    }
    replace .env $map
    Write-Host .env replaced

    $map = @{
        "'timezone' => 'UTC'" = "'timezone' => 'Asia/Shanghai'";
        "'locale' => 'en'" = "'locale' => 'zh'";
    }
    replace config/app.php $map
    Write-Host config/app.php replaced

    add-content .gitignore @'
/.settings/
/.buildpath
/.project
TestController.php
/storage/debugbar/
'@
    Write-Host .gitignore appended

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
        return __METHOD__;
    }

}


'@
    Write-Host $testctrl created and set content
}


composer require barryvdh/laravel-ide-helper barryvdh/laravel-debugbar doctrine/dbal -vvv

replaceFiles

echo "create database lry;" | mysql

php artisan ide-helper:model -WR
php artisan ide-helper:generate

php artisan cache:table
php artisan notifications:table
php artisan queue:failed-table
php artisan queue:table
php artisan session:table

php artisan migrate
