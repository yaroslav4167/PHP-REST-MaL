## Install
- Create MySQL database and import structure.sql
- Confugure config/config.php

## API Documentation
### POST /author/add
| Field       | Type    | Description          | Required |
|  ---        | ----    |      ----------      |  ------  |
|  name       | string  |   Author name        | **Yes**  |
|  patronymic | string  |   Author middle name | No       |

### POST /author/delete
| Field       | Type    | Description          | Required |
|  ---        | ----    |      ----------      |  ------  |
|  id         | int     |Author ID in Database | **Yes**  |

### GET /author/list
| Field       | Type    | Description          | Required |
|  ---        | ----    |      ----------      |  ------  |
|  page       | int     |   Start page number  |    No    |
|  perPage    | int     |   Number of elements |    No    |

### POST /author/update
| Field       | Type    | Description          | Required |
|  ---        | ----    |      ----------      |  ------  |
|  id         | int     |Author ID in Database | **Yes**  |
|  name       | string  |   Author name        |  No      |
|  patronymic | string  |   Author middle name |  No      |

### POST /magazine/add
| Field       | Type    | Description          | Required |
|  ---        | ----    |      ----------      |  ------  |
|  name       | string  | Magazine name        | **Yes**  |
|  date       | date    | Magazine release date| No       |
|  description| string  | Magazine description | No       |
|  image      | file    | Magazine image       | No       |
|  authors    | array   | Array of authors ID  | **Yes**  |

### POST /magazine/delete
| Field       | Type    | Description           | Required |
|  ---        | ----    |      ----------       |  ------  |
|  id         | int     |Magazine ID in Database| **Yes**  |

### GET /magazine/list
| Field       | Type    | Description          | Required |
|  ---        | ----    |      ----------      |  ------  |
|  page       | int     |   Start page number  |    No    |
|  perPage    | int     |   Number of elements |    No    |

### POST /magazine/update
| Field       | Type    | Description           | Required |
|  ---        | ----    |      ----------       |  ------  |
|  id         | int     |Magazine ID in Database| **Yes**  |
|  name       | string  | Magazine name         | No       |
|  date       | date    | Magazine release date | No       |
|  description| string  | Magazine description  | No       |
|  image      | file    | Magazine image        | No       |
|  authors    | array   | Array of authors ID   | Yes      |


## Curl test commands
### Add:
```
curl -X POST http://restma.ru/magazine/add -F "data={\"name\": \"WOW\", \"authors\": 1}" -F image=@"C:/Users/Yaroslavik/Downloads/images.jpg"
```

```
curl -X POST http://restma.ru/author/add -d "{\"name\": 234, \"patronymic\": 345}"
```

### Update:
```
curl -X POST http://restma.ru/magazine/update -F "data={\"id\": \"1\", \"authors\": [1,2]} -F image=@"C:/Users/Yaroslavik/Downloads/images.jpg"
```

```
curl -X POST http://restma.ru/author/update -d "{\"id\": \"2\", \"patronymic\": \"TEST\"}"

error case (empty name):
curl -X POST http://restma.ru/author/update -d "{\"id\": \"2\", \"patronymic\": \"TEST\", \"name\": \"\"}"
```

### Delete
```
curl -X POST http://restma.ru/magazine/delete -d "{\"id\": \"2\"}"
```

```
curl -X POST http://restma.ru/author/delete -d "{\"id\": \"4\"}"
```

### List
```
curl -X GET http://restma.ru/magazine/list
curl -X GET http://restma.ru/magazine/list -d "{\"page\": 1, \"perPage\":2}"
```

```
curl -X GET http://restma.ru/author/list
curl -X GET http://restma.ru/author/list -d "{\"page\": \"1\", \"perPage\":2}"
```
