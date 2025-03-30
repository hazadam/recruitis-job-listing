# How to run
### Stack setup
 - make up / docker compose up -d
### Env variables backend setup
 - cp app/be/.env.dist app/be/.env
 - fill following variables
 - RECRUITIS_AUTH_TOKEN=
 - RECRUITIS_CACHE_LIFETIME=
 - RECRUITIS_BASE_URI=
### Env variables frontend setup
 - cp app/fe/.env.dist app/fe/.env
 - specify where is the back going to be at
 - VITE_API_BASE_URI=
### Run BE + FE  
 - run _composer dev_ from app/be
 - run _npm run dev_ from app/fe
