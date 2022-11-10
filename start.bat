@ECHO OFF


@REM @REM for backend
@cd backend
@set PATH=php;%PATH%
start php artisan serve

@cd ..

@REM for frontend   
@cd frontend
@set PATH=nodejs;%PATH%

start npm run dev

@cd ..

@REM start http://192.168.2.174:3000

code backend/. && code frontend/.
