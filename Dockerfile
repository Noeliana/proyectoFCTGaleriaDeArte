FROM ubuntu:latest
LABEL authors="pigdata"

ENTRYPOINT ["top", "-b"]