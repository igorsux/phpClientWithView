#!/usr/bin/env bash
docker-compose -f setup.yaml run --rm composer $*
