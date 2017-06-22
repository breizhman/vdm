
Project VDM REST API
===

Introduction
============

Cette API REST permet de récupérer et consulter les articles du site viedemerde.com sous format JSON.


Installation
------------

1. Téléchargez l'application
2. Mettez à jour l'application via un "composer update"

Utilisation
-----------

### Chargement des articles

Le chargement des articles se fait via la ligne de commande suivante :
```bash
php bin/console vdm:rss:load
```

Par défaut, la commande charge 200 articles. Pour changer cette limite, modifier le paramètre suivant :

```yaml
# app/config/config.yml
parameters:
...
vdm.rss.limit_posts : 200
```

### Consultation des articles

L'affichage des articles se fait via url. Les articles sont affichés sous forme de tableau JSON.

Une liste d'article s'affiche selon l'URL suivante :
- /api/posts

Il est possible de filtrer le résultat en ajoutant des paramètres à l'URL :
- "author" pour filtrer sur les auteurs des articles (ex: /api/posts?author=John)
- "from" et "to" pour filtrer les articles selon une période (ex: /api/posts?from=2017-06-01&to=2016-06-12)

Un article peut être affiché selon l'URL suivante :
- /api/posts/{id} avec {id}, l'identifiant de publication du site VDM de l'article (ex: /api/posts/22015)

