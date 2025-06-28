
# Test d'implémentation Mercure - SSE

Salut. Ça fait longtemps que je voulais tester et mettre en place des notifications avec Mercure.  
C'est chose faite avec ce petit projet qui vise à tester divers évènements temps réel (SSE WebSockets).
Je n'ai pas passé de temps sur le front, mais j'ai fait en sorte que ca soit lisible à minima.  


## Prérequis

Docker installé sur votre machine.  
**N.B** : si vous utilisez docker desktop sur MacOS, ouvrez docker-desktop, rendez vous dans   
`settings->Resources->File sharing`  
et ajoutez le dossier du projet, ou son dossier parent.

## Installation  
`git clone https://github.com/frvaillant/mercure-chat folder-name`  
`cd folder-name`  
`make init`  
Cela devrait se conclure par :  
🎉 Projet initialisé avec succès ! Rendez-vous sur http://localhost

## Utilisation  
Une fois le projet installé, rendez-vous sur http://localhost  
Le projet définit 2 utilisateurs par défaut : user_1 et user_2, 
tous deux avec le mot de passe 123456.  

Connectez-vous avec le user_1. Ouvrez ensuite une fenêtre de 
navigation privée ou un autre navigateur et connectez-vous avec 
l'autre utilisateur.  
Vous pouvez tester.

## Arrêter et redémarrer  

/!\ Vous devez bien sûr être dans le dossier du projet ;-)  

Pour arrêter le projet : 
`docker compose stop`  

Pour redémarrer :
`docker compose start`

