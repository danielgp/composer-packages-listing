@echo off
SETLOCAL ENABLEEXTENSIONS
SET me=%~n0
SET current_folder=%~dp0
SET parent_folder=%current_folder:\tests\=\%
echo %parent_folder%
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64+xDebug\php.ini %parent_folder%vendor\phpunit\phpunit\phpunit  --configuration %parent_folder%phpunit.xml
