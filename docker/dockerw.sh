#!/bin/bash

echo "Building docker container. This may take a while ..."
docker build -t mpoint-test . >/dev/null
docker run --rm -i mpoint-test
