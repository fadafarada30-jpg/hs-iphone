$ErrorActionPreference = 'SilentlyContinue'

$root         = 'D:\2026\semester 3\webprograming\hs-iphone'
$php          = 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe'
$mysqld       = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe'
$myini        = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini'
$tunnelScript = "$root\scripts\start-tunnel.cmd"
$log          = "$root\scripts\watch-public.log"
$public       = 'https://doorway-ought-brink.ngrok-free.dev/health'
$lastTunnel   = (Get-Date).AddMinutes(-5)

function Write-Log {
    param([string]$Message)
    Add-Content -Path $log -Value ((Get-Date).ToString('yyyy-MM-dd HH:mm:ss') + ' ' + $Message)
}

function Test-TcpPort {
    param([int]$Port)
    $client = New-Object Net.Sockets.TcpClient
    try { $client.Connect('127.0.0.1', $Port); return $true } catch { return $false }
    finally { $client.Close() }
}

function Test-Url {
    param([string]$Url, [int]$Timeout = 10)
    try {
        $r = Invoke-WebRequest $Url -UseBasicParsing -TimeoutSec $Timeout -Headers @{ 'ngrok-skip-browser-warning' = 'true' }
        return ([int]$r.StatusCode -eq 200)
    } catch { return $false }
}

Write-Log 'WATCHDOG START (ngrok)'

while ($true) {
    if (-not (Test-TcpPort 3307)) {
        Write-Log 'MySQL down -> starting'
        Start-Process -FilePath $mysqld -ArgumentList @("--defaults-file=$myini", '--port=3307') -WindowStyle Hidden | Out-Null
    }

    if (-not (Test-Url 'http://127.0.0.1:8000/health' 5)) {
        Write-Log 'Laravel down -> starting'
        Start-Process -FilePath $php -ArgumentList @('artisan', 'serve', '--host=127.0.0.1', '--port=8000') -WorkingDirectory $root -WindowStyle Hidden | Out-Null
    }

    if (-not (Test-Url $public 20)) {
        if ((Get-Date) - $lastTunnel -gt [timespan]::FromSeconds(60)) {
            Write-Log 'Tunnel down -> restarting'
            Get-CimInstance Win32_Process -Filter "name='ngrok.exe'" | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }
            Start-Process -FilePath 'cmd.exe' -ArgumentList @('/c', "`"$tunnelScript`"") -WindowStyle Hidden | Out-Null
            $lastTunnel = Get-Date
        } else {
            Write-Log 'Tunnel down -> cooldown'
        }
    } else {
        Write-Log 'All services OK'
    }

    Start-Sleep -Seconds 20
}
