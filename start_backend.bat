@ECHO OFF


@REM @REM for backend
@cd backend
@set PATH=php;%PATH%
start php artisan serve