@echo off
SETLOCAL ENABLEEXTENSIONS
SET me=%~n0
SET current_folder=%~dp0
SET parent_folder=%current_folder:\tests\=\%
echo %parent_folder%
IF NOT EXIST "php-code-sniffer" (
    MD php-code-sniffer
)
echo Checking compatibility with PHP 5.5
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PHPCompatibility --runtime-set testVersion 5.5 %parent_folder% --report-file=%current_folder%php-code-sniffer\php_5.5.xml --ignore=*/tests/*,*/vendor/*
echo Checking compatibility with PHP 5.6
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PHPCompatibility --runtime-set testVersion 5.6 %parent_folder% --report-file=%current_folder%php-code-sniffer\php_5.6.xml --ignore=*/tests/*,*/vendor/*
echo Checking compatibility with PHP 7.0
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PHPCompatibility --runtime-set testVersion 7.0 %parent_folder% --report-file=%current_folder%php-code-sniffer\php_7.0.xml --ignore=*/tests/*,*/vendor/*
echo Checking compatibility with PHP 7.1
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PHPCompatibility --runtime-set testVersion 7.1 %parent_folder% --report-file=%current_folder%php-code-sniffer\php_7.1.xml --ignore=*/tests/*,*/vendor/*
echo Checking compatibility with PHP 7.2
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PHPCompatibility --runtime-set testVersion 7.2 %parent_folder% --report-file=%current_folder%php-code-sniffer\php_7.2.xml --ignore=*/tests/*,*/vendor/*
echo Checking PHP code for PSR1 compliance
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PSR1 %parent_folder% --report-file=%current_folder%php-code-sniffer\PSR1.xml --ignore=*/tests/*,*/vendor/*
echo Checking PHP code for PSR2 compliance
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PSR2 %parent_folder% --report-file=%current_folder%php-code-sniffer\PSR2.xml --ignore=*/tests/*,*/vendor/*
echo Checking PHP code for PSR12 compliance
C:\www\App\PHP\7.2.x64\php.exe -c C:\www\Config\PHP\7.2.x64\php.ini %parent_folder%vendor\squizlabs\php_codesniffer\bin\phpcs -p -v --extensions=php -d date.timezone="Europe/Bucharest" --encoding=utf-8 --report=xml --standard=PSR12 %parent_folder% --report-file=%current_folder%php-code-sniffer\PSR12.xml --ignore=*/tests/*,*/vendor/*
