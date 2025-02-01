# Vending Machine

## DESCRIPTION
### This application simulates a vending machine behaviour using an API

## REQUIREMENTS
### Docker and docker-compose installation is needed
<br>

## INSTALLATION
### Inside the project rott directory execute:

`docker-compose build`

`docker-compose up`

### Once the docker container are running execute the following command to get inside the container

`docker exec -it vending-php bash`

## Now inside the container,execute the migrations

`php bin/console doctrine:migrations:migrate`

## USAGE

### The aplication has four actions service, insert money, return money and get product
<br> 

### 1. SERVICE

### In this action is set the product and the money available to return change

#### URL

`http://localhost/vending/service`

#### METHOD

`POST`

#### PAYLOAD EXAMPLE
```json
{
  "change": [
    { "value": "100", "quantity": 10 },
    { "value": "5", "quantity": 10 },
    { "value": "10", "quantity": 10 },
    { "value": "25", "quantity": 10 }
  ],
  "items":[
    { "name": "soda","price":150, "quantity": 7 },
    { "name": "water","price":65, "quantity": 10 },
    { "name": "juice","price":100, "quantity": 4 }
  ]
}
```
### RESPONSE
```json
{
    "message": "Service action completed"
}
```
<br>

### 2. INSERT MONEY

### In this action a coin is inserted on the vending machine, one coin is inserted per request with no limit of requests

#### URL

`http://localhost/vending/insert`

#### METHOD

`POST`

#### PAYLOAD EXAMPLE
```json
{
  "value":0.25
}

```
### RESPONSE
```json
{
    "message": "Insert action completed"
}
```
<br>

### 3. RETURN MONEY

### In this action all the inserted coins are returned

#### URL

`http://localhost/vending/return-money`

#### METHOD

`GET`

```
### RESPONSE
```json
[
    "0.10",
    "0.10"
]
```
<br>

### 4. GET PRODUCT

### In this action the selected product is delivered and the change retuned, a query param with product has to be added

#### URL

`http://localhost/vending/select?selection=GET-WATER`

#### METHOD

`GET`

```
### RESPONSE
```json
[
    {
        "message": [
            "WATER",
            "0.25",
            "0.10"
        ]
    }
]
```
<br>

## TESTING

### Inside the container execute

`php bin/phpunit`





