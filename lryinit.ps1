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
        'DB_DATABASE=homestead' = 'DB_DATABASE=lry';
        'DB_USERNAME=homestead' = 'DB_USERNAME=root';
        'DB_PASSWORD=secret' = 'DB_PASSWORD=1234';
    }
    replace .env $map
    Write-Host .env replaced

    $map = @{
        "'timezone' => 'UTC'" = "'timezone' => 'Asia/Shanghai'";
        "'locale' => 'en'" = "'locale' => 'zh'";
        'App\Providers\RouteServiceProvider::class,' = @'
App\Providers\RouteServiceProvider::class,

        Barryvdh\Debugbar\ServiceProvider::class,
        Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
'@
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
    $nulll = new-item -path $testctrl -itemtype file
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

php artisan ide-helper:model -WR
php artisan ide-helper:generate
