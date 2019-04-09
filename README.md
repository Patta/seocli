# SEO CLI

Scan websites against SEO criteria and/or trigger the indexing process and cache warming in deployment scripts.

## Usage

### Composer project

* Require lib ``composer require --dev lochmueller/seocli``
* Run with ``./vendor/bin/seocli ###ARGS###``

### Standalone

* Clone repo ``git clone https://github.com/lochmueller/seocli.git``
* Load lib ``cd seocli && composer install``
* Run with ``./bin/seocli ###ARGS###``

## Arguments

- ``-u uri, --uri uri`` The base URI to start the SEO CLI
- ``-d depth, --depth depth (default: 1)`` The depth of the crawler
- ``-f format, --format format (default: text)``The format of the output [text,json,xml,csv,none]
- ``-t topCount, --top-count topCount (default: 5)`` The number of items in the top lists [0=disable]

## Example

Get information from www.website.de with depth 1 as CSV format: 

``###PATH###/seocli -u https://www.website.de -d 1 -f csv``
