@echo off
setlocal

:: Fecha y hora para nombrar el archivo de backup
for /f "tokens=1-4 delims=/: " %%a in ("%date%") do set today=%%d-%%b-%%c
for /f "tokens=1-2 delims=: " %%a in ("%time%") do set time=%%a-%%b

:: Nombre de la base de datos, usuario y contraseña de MySQL
set db_name=inventario1
set user=root
set password=

:: Ruta al directorio de backups dentro de XAMPP
set backup_dir=C:\xampp\backup

:: Ruta a mysqldump (viene con XAMPP)
set mysqldump_path=C:\xampp\mysql\bin\mysqldump.exe

:: Archivo de salida
set backup_file=%backup_dir%\%db_name%_%today%_%time%.sql

:: Crear la carpeta de backups si no existe
if not exist "%backup_dir%" (
    mkdir "%backup_dir%"
)

:: Ejecutar mysqldump para hacer el backup sin solicitar contraseña
echo %password% | "%mysqldump_path%" -u %user% -p --password=%password% %db_name% > "%backup_file%" 2>> "%backup_dir%\backup_log.txt"

:: Confirmación y cierre
if errorlevel 1 (
    echo Error al ejecutar mysqldump >> "%backup_dir%\backup_log.txt"
) else (
    exit /b 0
)

endlocal
