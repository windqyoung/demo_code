param(
    $path, [switch]$verbose
)


# ���1.jpg��Ŀ¼����ڲ�����
function check1jpg
{
    process {
        $dir = $_
        if (! $dir.PSIsContainer)
        {
            Write-Verbose ($dir.fullname + ' ����Ŀ¼, ����')
            return
        }

        $hasJpg = dir $dir.fullname -Filter *.jpg # ��jpg ?
        if (! $hasJpg)
        {
            Write-Verbose ($dir.fullname + ' ����û��.jpg�ļ�, ����')
            return
        }

        $has1jpg = dir $dir.fullname -filter 1.jpg # ��1.jpg ?
        if (! $has1jpg) {
            Write-Host $dir.fullname δ�ҵ�1.jpg -ForeGroundColor Green
        }
    }
}

# ת���ļ����Ĵ�Сд
function convertFileName($lower = $true)
{
    process {
        $file = $_

        $toName = if ($lower) { $file.name.toLower() } else { $file.name.toUpper() }

        if ($file.name -ceq $toName)
        {
            Write-Verbose ($file.fullname + ' �������, ����')
            return
        }

        if (! $file.PsIsContainer)
        {
            Rename-Item $file.fullname $toName
        }
        # ����Ŀ¼
        else
        {
            $tempName = $file.name + (New-Guid)
            Rename-Item $file.fullname $tempName # �ĳ���ʱĿ¼��
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
    $path = Read-Host ������Ŀ¼
}

$action = @(
    @{
        title = '�˳�(q)';
        cmd = {
            exit
        }
    },
    @{
        title = '���1.jpg����';
        cmd = {
            dir $path -r -Directory | check1jpg
        }
    },
    @{
        title = 'ת��1.jpgΪСд';
        cmd = {
            dir $path -r -File -Filter 1.jpg | convertFileName
        }
    },
    @{
        title = 'ת��1.jpgΪ��д';
        cmd = {
            dir $path -r -File -Filter 1.jpg | convertFileName $false
        }
    },
    @{
        title = 'ת��ȫ��Ŀ¼ΪСд';
        cmd = {
            dir $path -r -Directory | convertFileName
        }
    },
    @{
        title = 'ת��ȫ��Ŀ¼Ϊ��д';
        cmd = {
            dir $path -r -Directory | convertFileName $false
        }
    },
    @{
        title = '����ͼƬ�ļ�';
        cmd = {
            dir $path -Directory | foreach {
                dir $_.FullName -r -File -Exclude *.jpg, *.png, *.jpeg | foreach {
                    Write-Host $_.fullname �ҵ� -ForegroundColor Red
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

    $sel = Read-Host ѡ�����

    if ($sel -eq 'q')
    {
        exit
    }

    if ($action[$sel]) {
        & $action[$sel].cmd
    }
    else
    {
        '��Ч����'
    }
}