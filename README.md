# Ninox API Wrapper

[Ninox's REST API](https://ninoxdb.de/de/manual/ninox-api/rest-api) currently only supports API-Token-Auth where your token grants full access to all your Ninox-Teams and Ninox-Databases. So usually you don't wont to expose your API-Token. Which makes the API not suitable for calls from a Frontend or an App. This API wrapper makes it possible to limit the access to your Ninox API using the same REST API without ever exposing your Ninox API-Token. 

## Quick Start

Install via Composer
```
composer install
```

Serve the API locally
```
php -S localhost:8000 -t public
```

Fill the `.env` file with your Ninox Informations
```
NINOX_API_URL=https://api.ninoxdb.de/v1/
NINOX_API_KEY=your top secret ninox api key
NINOX_TEAM_ID=some team id
NINOX_DATABASE_ID=the database id
```

## Endpoints

The endpoints are orientaded on the original Ninox URI Layout - just without the now unnessesary parts.

## Database

### Get Database Information

```
GET /
```
_(Analogous to ```api.ninoxdb.de/v1/teams/:team/databases/:database/```)_


## Tables

### Get List of all Tables

```
GET /tables
```
_(Analogous to ```api.ninoxdb.de/v1/teams/:team/databases/:database/tables```)_

### Get Table Information by Table-ID

```
GET /tables/:tableid
```
_(Analogous to ```api.ninoxdb.de/v1/teams/:team/databases/:database/tables/:tableid```)_

## Records

### Get Records of a Table

```
GET /tables/:tableid/records
```
_(Analogous to ```api.ninoxdb.de/v1/teams/:team/databases/:database/tables/:tableid/records```)_

### Get Record by ID of a Table

```
GET /tables/:tableid/records/:recordid
```
_(Analogous to ```api.ninoxdb.de/v1/teams/:team/databases/:database/tables/:tableid/records/recordid```)_

## Environment Variables & Customization

Don't want that all tables of your Ninox Database are public queryable? Define a Whitelist trough the `PUBLIC_TABLES` env variable.
```
PUBLIC_TABLES=A,B,C2
```

If you want to, you can rename the URL parts ```/tables/``` and ```/records/``` via the .env variables.

```
URL_PART_TABLES=table
URL_PART_RECORDS=records
```
