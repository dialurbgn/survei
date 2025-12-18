docker-compose up --build -d
docker exec -it ci_app composer install
docker exec -it ci_app fail2ban-client status
docker exec -it ci_app fail2ban-client status nginx-http-auth


docker compose up -d

Pastikan semua service berjalan:
docker ps 

docker exec -it ci_php pm2 logs


docker exec -it ci_nginx nginx -s reload
docker exec -it ci_nginx nginx -t

docker-compose build nginx
docker-compose up -d --force-recreate