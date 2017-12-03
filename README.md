# eveseat-mining-ledger
An extension for Eve SeAT which is providing Mining Ledger over ESI for 2.x version

[![Latest Stable Version](https://img.shields.io/packagist/v/warlof/eveseat-mining-ledger.svg?style=flat-square)](https://packagist.org/packages/warlof/eveseat-mining-ledger)
[![Build Status](https://img.shields.io/travis/warlof/eveseat-mining-ledger.svg?style=flat-square)](https://travis-ci.org/warlof/eveseat-mining-ledger)
[![Code Climate](https://img.shields.io/codeclimate/github/warlof/eveseat-mining-ledger.svg?style=flat-square)](https://codeclimate.com/github/warlof/eveseat-mining-ledger)
[![Coverage Status](https://img.shields.io/coveralls/warlof/eveseat-mining-ledger.svg?style=flat-square)](https://coveralls.io/github/warlof/eveseat-mining-ledger?branch=master)
[![License](https://img.shields.io/badge/license-GPLv3-blue.svg?style=flat-square)](https://raw.githubusercontent.com/warlof/eveseat-mining-ledger/master/LICENSE)

# Setup

## Create application

- go to the following url in order to create credentials
-- for live server : https://developers.eveonline.com
-- for test server : https://developers.testeveonline.com

- check `Authentication & API Access` in `Connection Type`

- search for `esi-industry.read_character_mining.v1` scope in `Available Scopes List` and move it to `Requested Scopes List` by clicking on the two arrows button.

- set the following value into callback URL `{seat-public-url}/auth/mining-ledger/callback`
> **NOTE**
> 
> for example, if you have SeAT available on `seat.example.com`, the callback will be `https://seat.example.com/auth/mining-ledger/callback`
> but, if you're accessing to SeAT with `example.com/seat`, therefore, the callback will be `https://example.com/seat/auth/mining-ledger/callback`

> **IMPORTANT**
> 
> Application are not cross compatible.
> If you want to use `singularity` as server source, you have to create an application on `testeveonline.com`.
> You'll need to create another application on `eveonline.com` in order to make call to `tranquility`

## Install package

- download package using `composer require warlof/eveseat-mining-ledger`
- add package into project by appending `Warlof\Seat\MiningLedger\MiningLedgerProvider::class,` in `providers` array from `/config/app.php`
- append following attributes into `.env` file

| Variable               | Description                                                                              |
|------------------------|------------------------------------------------------------------------------------------|
| WEML_EVE_CLIENT_ID     | A valid CCP client_id                                                                    |
| WEML_EVE_CLIENT_SECRET | The client associated secret                                                             |
| WEML_SSO_BASE          | `https://login.eveonline.com/oauth` for live                                             |
|                        | `https://sisi.testeveonline.com/oauth` for test                                          |
| WEML_ESI_SERVER        | `tranquility` for live ~ `singularity` for test                                          |

# About

This package is providing ESI support for mining ledger only to SeAT 2.x which is still using the deprecated xAPI. Prefer to install SeAT 3.x if it's available since provided informations will be available in core.

## Commands

This package will add few commands into your SeAT installation. You'll their name and purpose into the table bellow.

| Command name | Description | Scheduled |
|---------------------|--------------|--------|
| esi:market-prices:update | This command will update item average and adjuster prices | twice a day |
| esi:mining-ledger:update | This command will update mining ledger from every character which have access granted to the package | every 10 minutes |

## Permissions

This package will add two new permission for respectively character and corporation :
 - corporation.warlof_mining
 - character.warlof_mining

They will grant access to mining ledger on each entity type.

# Usage

Every user must go on each of their character in order to active the synchronization. On character views, a new item menu should appeared called `Mining`.
If user clic on this menu item and the current character has not already been binded, there will be an `Activate` link on table header, on the right corner.

As CEO or officer, you'll be able to see mined amount on corporation sheet where a new item menu should appeared, called `Mining`.
The first sub view `Ledger` will show you a list of average mined quantity, volume and amount foreach character per year and month.

The second sub view called `Tracking` will show you the list of all character and display the status if or not the character is coupled to ESI.