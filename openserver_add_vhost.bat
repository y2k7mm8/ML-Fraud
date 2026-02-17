@echo off
setlocal

REM Usage: openserver_add_vhost.bat [domain] [path-to-OpenServer.exe]
if "%1"=="" (
  set DOMAIN=website-about.test
) else (
  set DOMAIN=%1
)

if "%2"=="" (
  set OPEN_SERVER_PATH=C:\OpenServer\OpenServer.exe
) else (
  set OPEN_SERVER_PATH=%2
)

set HOSTS=%windir%\System32\drivers\etc\hosts

echo Проверяю запись в hosts для %DOMAIN%...
findstr /I /C:"%DOMAIN%" "%HOSTS%" >nul
if %ERRORLEVEL%==0 (
  echo Запись для %DOMAIN% уже существует в hosts.
) else (
  echo Добавляю запись в hosts (требуется запуска от администратора)...
  >> "%HOSTS%" echo 127.0.0.1 %DOMAIN%
  if %ERRORLEVEL%==0 (
    echo Успешно добавлено: 127.0.0.1 %DOMAIN%
  ) else (
    echo Не удалось изменить hosts. Запустите этот скрипт от имени администратора или добавьте запись вручную.
  )
)

if exist "%OPEN_SERVER_PATH%" (
  echo Запускаю OpenServer: %OPEN_SERVER_PATH%
  start "OpenServer" "%OPEN_SERVER_PATH%"
) else (
  echo OpenServer не найден по пути %OPEN_SERVER_PATH%.
  echo Отредактируйте скрипт или запустите OpenServer вручную.
)

pause
