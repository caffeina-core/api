# Core Extension : API

Il modulo API permette di esporre rapidamente dei dati tramite RESTful API.

## Classi Implementate

### API
Questa classe gestisce il setup base delle routes e l'installazione degli endpoints.

### Resource
Classe astratta per la rappresentazione dei dati da esporre.


## Esporre una risorsa

Per prima cosa è necessario creare una risorsa estendendo con `Resource ` una nuova classe o una già esistente (ad esempio un modello).

```php
class Category extends Resource {}
```

Questa classe esporrà direttamente tutti i dati passati, ed è equivalente alla seguente forma :

```php
class Category extends Resource {
  public function expose($fields, $mode) {
    return $fields;
  }
}
```

Tramite il modulo `API` possiamo ora esporre un endpoint per la restituzione dei dati :

```php
API::resource("/categories", [
  "class"    => "Category",
  "sql"      => [
    "table"       => "categories",
  ],
]);
```

La chiamata `API::resource` ha interfaccia :

```php
API::resource($path, array $options)
```
`$path` è la base della URL sulla quale esporre la risorsa.

I parametri di `$options` sono :

| Nome | Descrizione | Obbligatorio | Default |
|------|-------------|---------|-----|
| `class` | La classe estesa da `Resource` della risorsa associata. | SI | `null` |
| `sql.table` | La tabella dalla quale leggere i dati | NO | `null` |
| `sql.raw` | La query custom per leggere i dati | NO | `null` |
| `sql.primary_key` | il nome della colonna che rappresenta la chiave primaria | SI | `id` |

È necessario che almeno uno tra `sql.raw` o `sql.table` sia presente. Ricordarsi che `sql.raw` ha priorità su `sql.table`.


L'output sarà quindi :

```json
{
  "data": [
    {
      "id": "lifestyle",
      "name": "Lifestyle",
      "thumbnail": "/media/batband2.jpg"
    },
    {
      "id": "generale",
      "name": "Generale",
      "thumbnail": null
    },
    {
      "id": "viaggi",
      "name": "Viaggi",
      "thumbnail": "/media/kayak12.jpg"
    },
    {
      "id": "cultura",
      "name": "Cultura",
      "thumbnail": "/media/272.jpg"
    },
    {
      "id": "benessere",
      "name": "Benessere",
      "thumbnail": "/media/maratoneti2.jpg"
    },
    {
      "id": "musica",
      "name": "Musica",
      "thumbnail": "/media/leonard2.jpg"
    },
    {
      "id": "news",
      "name": "News",
      "thumbnail": "/media/vin-diesel2.jpg"
    },
    {
      "id": "scienza",
      "name": "Scienza",
      "thumbnail": "/media/462.jpg"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "count": 8
  }
}
```
I parametri GET supportati dalla paginazione sono :

| Nome | Descrizione |
|------|-------------|
| `page` | Indice a base 1 della pagina corrente. |
| `limit` | Numero di elementi per pagina |

## Variare l'esposizione dei dati della risorsa

È possibile cambiare la natura dei dati presentati da parte della risorsa (Marshalling).

È sufficiente restituire un nuovo schema dei dati nella funzione `expose` della risorsa.

```php
class Article extends Resource {
  public function expose($fields, $mode) {
    return [
        "id"        => $fields->slug,
        "title"     => $fields->title,
        "thumbnail" => $fields->thumbnail,
        "content"   => $fields->content,
        "created"   => date("Y-m-d H:i:s", $fields->created),
        "lang"      => $fields->lang,
        "tags"      => explode(',', $fields->tags),
        "seo"       => [
          "title"       => $fields->seo_title,
          "keywords"    => $fields->seo_keywords,
          "description" => $fields->seo_description,
        ],
    ];
```

Se viene usata la funzione `API::resource` verrà installata una modalità differente per l'esposizione dei dati nella vista di collezione paginata rispetto a quella a singola risorsa.

Nella vista di collezione paginata il parametro `$mode` della funzione `expose` avrà valore `list`.

In questo modo è possibile esporre modelli differenti di dati a seconda della modalità selezionata per la Risorsa.

È possibile selezionare manualmente la modalità di esposizione tramite la funzione : `Resource::setExposure("my-custom-mode");`

```php
class Article extends Resource {
  public function expose($fields, $mode) {
    switch ($mode) {
      case "list": return [
        "id"        => $fields->slug,
        "title"     => $fields->title,
        "thumbnail" => $fields->thumbnail,
      ];
      default: return [
        "id"        => $fields->slug,
        "title"     => $fields->title,
        "thumbnail" => $fields->thumbnail,
        "content"   => $fields->content,
        "created"   => date("Y-m-d H:i:s",$fields->created),
        "lang"      => $fields->lang,
        "tags"      => explode(',', $fields->tags),
        "seo"       => [
          "title"       => $fields->seo_title,
          "keywords"    => $fields->seo_keywords,
          "description" => $fields->seo_description,
        ],
      ];
    }
  }
}
```

L'output della lista sarà :

```json
// http://api.helloworld.caffeina.be/article?limit=3

{
  "data": [
    {
      "id": "social-banking",
      "title": "Social Banking",
      "thumbnail": "alfa12.jpg"
    },
    {
      "id": "hello-marco",
      "title": "Hello Marco!",
      "thumbnail": "marco12.png"
    },
    {
      "id": "gli-smartphone-degli-italiani",
      "title": "Gli smartphone degli italiani",
      "thumbnail": "130726-infografica_header12.png"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 3,
    "count": 919
  }
}
```

Mentre nella visualizzazione del dettaglio l'esposizione sarà :

```json
// http://api.helloworld.caffeina.be/article/hello-marco
{
  "data": {
      "id": "hello-marco",
      "title": "Hello Marco!",
      "thumbnail": "marco12.png",
      "content": "...",
      "created": "2013-07-19 08:06:33",
      "lang": "IT",
      "tags": [
        "team",
        "bank",
        "hello"
      ],
      "seo": {
        "title": "...",
        "keywords": "...",
        "description": "..."
      }
    }
}
```

## Proiezione

È possibile eseguire una proiezione dei dati tramite il parametro  GET `fields`.

La lista dei campi da mostrare esclusivamente è passabile come lista separata da virgola dei nomi dei campi.

```
?fields=<field1>,<field2>,<field3>,...
```

Esempio :

```json
// http://api.helloworld.caffeina.be/article/hello-marco?fields=title,thumbnail

{
  "data": {
    "id": "hello-marco",
    "title": "Hello Marco!",
    "thumbnail": "marco12.png"
  }
}
```

Se viene usata la funzione `API::resource` è possibile definire come shorthand della seguente richiesta :

```
/article/hello-marco?fields=title,thumbnail
```

la seguente URL :

```
/article/hello-marco/title,thumbnail
```

### Usare una query custom per esporre una risorsa

È possibile usare una query sql custom per il recupero dei dati da gestire tramite la risorsa assegnata.

Esempio :

```php
API::resource("/article", [
  "class"    => "Article",
  "sql"      => [
    "raw"         => "SELECT a.*, c.name AS category_name FROM articles_view a JOIN categories c on c.id = a.id_category",
    "primary_key" => "slug",
  ],
]);
```

In questo modo sarà possibile esporre il campo ottenuto tramite la join `category_name` :

```php
class Article extends Resource {
  public function expose($fields, $mode) {
    switch ($mode) {
      case "list": return [
        "id"        => $fields->slug,
        "title"     => $fields->title,
        "thumbnail" => $fields->thumbnail,
        "category"  => $fields->category_name,
      ];
      default: return [
        "id"        => $fields->slug,
        "title"     => $fields->title,
        "thumbnail" => $fields->thumbnail,
        "content"   => $fields->content,
        "created"   => date("Y-m-d H:i:s", $fields->created),
        "lang"      => $fields->lang,
        "category"  => $fields->category_name,
        "tags"      => explode(',', $fields->tags),
        "seo"       => [
          "title"       => $fields->seo_title,
          "keywords"    => $fields->seo_keywords,
          "description" => $fields->seo_description,
        ],
      ];
    }
  }
}
```
