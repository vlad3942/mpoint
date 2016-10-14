
docker build -t mpoint-test .
docker run --rm -i --dns 8.8.8.8 --dns 8.8.4.4 mpoint-test
