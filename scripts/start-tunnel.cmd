@echo off
"C:\laragon\bin\ngrok\ngrok.exe" http 8000 --request-header-add ngrok-skip-browser-warning:true --log=stdout --log-level=info
