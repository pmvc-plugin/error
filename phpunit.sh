#!/bin/bash

docker-compose run --rm phpunit phpunit --no-configuration --bootstrap ./tests-legacy/include.php ./tests-legacy/ErrorTest.php 
