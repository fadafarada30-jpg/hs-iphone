$ErrorActionPreference = 'SilentlyContinue'

$root         = 'D:\2026\semester 3\webprograming\hs-iphone'
$php          = 'C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe'
$mysqld       = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe'
$myini        = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini'
$tunnelScript = "$root\scripts\start-tunnel.cmd"
$public       = 'https://hs-iphone.loca.lt/health'
$lastTunnel   = (Get-Date).AddMinutes(-5)

function Test-TcpPort {
    param([int]$Port)
    $client = New-Object Net.Sockets.TcpClient
    try { $client.Connect('127.0.0.1', $Port); return $true } catch { return $false }
    finally { $client.Close() }
}

function Test-Url {
    param([string]$Url, [int]$Timeout = 10)
    try { $r = Invoke-WebRequest $Url -UseBasicParsing -TimeoutSec $Timeout; return ([int]$r.StatusCode -eq 200) }
    catch { return $false }
}

while ($true) {
    # 1. MySQL
    if (-not (Test-TcpPort 3307)) {
        Start-Process -FilePath $mysqld -ArgumentList @("--defaults-file=$myini", '--port=3307') -WindowStyle Hidden | Out-Null
    }

    # 2. Laravel dev server
    if (-not (Test-Url 'http://127.0.0.1:8000/health' 5)) {
        Start-Process -FilePath $php -ArgumentList @('artisan', 'serve', '--host=127.0.0.1', '--port=8000') -WorkingDirectory $root -WindowStyle Hidden | Out-Null
    }

    # 3. localtunnel dengan subdomain tetap (maksimal sekali per 60 detik)
    if (-not (Test-Url $public 20) -and ((Get-Date) - $lastTunnel).TotalSeconds -gt 60) {
        Get-CimInstance Win32_Process -Filter "name='node.exe'" | Where-Object { $_.CommandLine -like '*localtunnel*' } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }
        Start-Process -FilePath 'cmd.exe' -ArgumentList @('/c', "`"$tunnelScript`"") -WindowStyle Hidden | Out-Null
        $lastTunnel = Get-Date
    }

    Start-Sleep -Seconds 20
}
