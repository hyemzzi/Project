FROM php:8.2-fpm

# MariaDB 연동을 위한 확장 설치
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 빌드 타임 ARG
ARG DB_HOST
ARG DB_USER
ARG DB_PASSWORD
ARG DB_NAME

# 런타임 ENV로 전달
ENV DB_HOST=$DB_HOST \
    DB_USER=$DB_USER \
    DB_PASSWORD=$DB_PASSWORD \
    DB_NAME=$DB_NAME
    
WORKDIR /var/www/html

# PHP 소스코드 복사
COPY ./src /var/www/html

# 권한 설정 (필요시)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# PHP-FPM 포트 확인 (기본 9000)
EXPOSE 9000

