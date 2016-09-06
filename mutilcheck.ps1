param(
    $path, [switch]$verbose
)


# 检查1.jpg在目录里存在不存在
function check1jpg
{
    process {
        $dir = $_
        if (! $dir.PSIsContainer)
        {
            Write-Verbose ($dir.fullname + ' 不是目录, 跳过')
            return
        }

        $hasJpg = dir $dir.fullname -Filter *.jpg # 有jpg ?
        if (! $hasJpg)
        {
            Write-Verbose ($dir.fullname + ' 里面没有.jpg文件, 跳过')
            return
        }

        $has1jpg = dir $dir.fullname -filter 1.jpg # 有1.jpg ?
        if (! $has1jpg) {
            Write-Host $dir.fullname 未找到1.jpg -ForeGroundColor Green
        }
    }
}

# 转换文件名的大小写
function convertFileName($lower = $true)
{
    process {
        $file = $_

        $toName = if ($lower) { $file.name.toLower() } else { $file.name.toUpper() }

        if ($file.name -ceq $toName)
        {
            Write-Verbose ($file.fullname + ' 无需改名, 跳过')
            return
        }

        if (! $file.PsIsContainer)
        {
            Rename-Item $file.fullname $toName
        }
        # 改名目录
        else
        {
            $tempName = $file.name + (New-Guid)
            Rename-Item $file.fullname $tempName # 改成临时目录名
            Rename-Item (Join-Path $file.Parent.FullName $tempName) $toName
        }

        Write-Host $file.fullname renamed -ForeGroundColor Yellow
    }

}

if ($verbose)
{
    $VerbosePreference = 'Continue'
}

while (! $path)
{
    $path = Read-Host 请输入目录
}

$action = @(
    @{
        title = '退出(q)';
        cmd = {
            exit
        }
    },
    @{
        title = '检查1.jpg存在';
        cmd = {
            dir $path -r -Directory | check1jpg
        }
    },
    @{
        title = '转换1.jpg为小写';
        cmd = {
            dir $path -r -File -Filter 1.jpg | convertFileName
        }
    },
    @{
        title = '转换1.jpg为大写';
        cmd = {
            dir $path -r -File -Filter 1.jpg | convertFileName $false
        }
    },
    @{
        title = '转换全部目录为小写';
        cmd = {
            dir $path -r -Directory | convertFileName
        }
    },
    @{
        title = '转换全部目录为大写';
        cmd = {
            dir $path -r -Directory | convertFileName $false
        }
    },
    @{
        title = '检查非图片文件';
        cmd = {
            dir $path -Directory | foreach {
                dir $_.FullName -r -File -Exclude *.jpg, *.png, *.jpeg | foreach {
                    Write-Host $_.fullname 找到 -ForegroundColor Red
                }
            }
        }
    }
)

while ($true)
{
    for ($i = 0; $i -lt $action.Length; $i ++)
    {
        Write-Host "`t" "${i}:" $action[$i].title -ForegroundColor Cyan
    }

    $sel = Read-Host 选择操作

    if ($sel -eq 'q')
    {
        exit
    }

    if ($action[$sel]) {
        & $action[$sel].cmd
    }
    else
    {
        '无效操作'
    }
}