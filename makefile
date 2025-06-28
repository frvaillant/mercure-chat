# Nom du fichier : Makefile (pas d'extension)

# Variables
ENV_FILE=.env.local

# Cible par défaut
.DEFAULT_GOAL := init

check-docker:
	@command -v docker > /dev/null || { echo "❌ Docker n'est pas installé ou pas dans le PATH."; exit 1; }
	@docker info > /dev/null 2>&1 || { echo "❌ Docker ne fonctionne pas (le daemon est-il lancé ?)"; exit 1; }
	@echo "✅ Docker est disponible et fonctionne."

# Commande : make init
init: check-docker
	@echo "🔧 Initialisation du projet..."
	@test -f $(ENV_FILE) || cp .env $(ENV_FILE)
	@echo "Build container"
	docker compose build
	@echo "Start container"
	docker compose --env-file $(ENV_FILE) up -d
	@echo "Composer install"
	docker compose exec php composer install --no-interaction --no-progress --quiet
	@echo "Migrations"
	docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
	@echo "Yarn install"
	docker compose exec php yarn install --silent
	@echo "Build assets"
	docker compose exec php yarn encore dev > /dev/null 2>&1
	@echo "Add users in database"
	docker compose exec php bin/console app:add:user

	@echo "\033[32m🎉 Projet initialisé avec succès ! Rendez-vous sur http://localhost \033[0m"

# Commande : make down
down:
	docker compose down -v

# Commande : make reset
reset:
	docker system prune -af --volumes

# Commande : make logs
logs:
	docker compose logs -f

# Commande : make sh
sh:
	docker compose exec php sh

