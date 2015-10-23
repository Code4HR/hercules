FROM debian:8.1

###################
# Package Manager Dependencies
RUN \
  apt-get update && \
  apt-get install -y \
  vim \
  curl \
  ant \
  build-essential \
  apache2 \
  apache2-doc \
  php5 \
  libapache2-mod-php5 \
  sudo \
  make \
  net-tools \
  amavisd-new \
  libcurl4-gnutls-dev

##################
# ElasticSearch install
RUN \
  curl -L -O https://download.elastic.co/elasticsearch/elasticsearch/elasticsearch-1.7.1.tar.gz && \
  tar -xzvf ./elasticsearch-1.7.1.tar.gz && \
  rm ./elasticsearch-1.7.1.tar.gz && \
  mv ./elasticsearch-1.7.1 /usr/local/share/elasticsearch && \
  /usr/local/share/elasticsearch/bin/plugin -install mobz/elasticsearch-head

###################
# Work Directory
WORKDIR /src

###################
# Container Command
CMD ["/bin/bash"]
