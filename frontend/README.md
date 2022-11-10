Versions 1.0.0
--------------

Installation Guide:
  - clone repo
  - npm i
  - set env variables
      - LOCAL_IP=192.168.2.174
      - LOCAL_PORT=3000
      - BACKEND_URL=http://localhost:8000/api
      - SOCKET_ENDPOINT=web socket endpoint
      - Hash=you hash
  - npm run dev (dev environment)
  - npm run build && npm run start (production environment)
  - pm2 start "npm run start" --name "Nuxt"