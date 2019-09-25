FROM php:7.1

MAINTAINER whr

# Version
ENV PHPREDIS_VERSION 4.0.0
ENV HIREDIS_VERSION 0.13.3
ENV SWOOLE_VERSION 4.2.5

RUN sed -i 's#http://archive.ubuntu.com/#http://mirrors.tuna.tsinghua.edu.cn/#' /etc/apt/sources.list;

# Libs
#RUN apt-get update \
#    && apt-get install -y\
#        curl \
#        wget \
#        git \
#        zip \
#        libz-dev \
#        libssl-dev \
#        libnghttp2-dev \
#        libpcre3-dev \
#    && apt-get clean \
#    && apt-get autoremove
RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libpng-dev \
	&& docker-php-ext-install -j$(nproc) iconv mcrypt \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd


#zip
RUN apt-get install libzip-dev -y
RUN pecl install zip
RUN docker-php-ext-enable zip
RUN apt-get install zip -y

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update --clean-backups

# Redis extension
RUN wget http://download.redis.io/releases/redis-4.0.9.tar.gz -O /tmp/redis.tar.tgz \
    && pecl install /tmp/redis.tar.tgz \
    && rm -rf /tmp/redis.tar.tgz \
    && docker-php-ext-enable redis

# Hiredis
RUN wget https://github.com/redis/hiredis/archive/v${HIREDIS_VERSION}.tar.gz -O hiredis.tar.gz \
    && mkdir -p hiredis \
    && tar -xf hiredis.tar.gz -C hiredis --strip-components=1 \
    && rm hiredis.tar.gz \
    && ( \
        cd hiredis \
        && make -j$(nproc) \
        && make install \
        && ldconfig \
    ) \
    && rm -r hiredis

# Swoole extension
RUN wget https://github.com/swoole/swoole-src/archive/v${SWOOLE_VERSION}.tar.gz -O swoole.tar.gz \
    && mkdir -p swoole \
    && tar -xf swoole.tar.gz -C swoole --strip-components=1 \
    && rm swoole.tar.gz \
    && ( \
        cd swoole \
        && phpize \
        && ./configure --enable-async-redis --enable-mysqlnd --enable-openssl --enable-http2 \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r swoole \
    && docker-php-ext-enable swoole

ADD . /home/dsp

WORKDIR /home/dsp

EXPOSE 80

ENTRYPOINT ["php","examples/websocket_server.php "]