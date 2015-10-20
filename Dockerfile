FROM debian:8.1

RUN \
  apt-get update && \
  apt-get install -y vim && \
  apt-get install -y build-essential apache2 apache2-doc php5 libapache2-mod-php5

WORKDIR /src

CMD ["/bin/bash"]
