FROM centos:7.6.1810

RUN yum -y update && yum clean all
# install PHP7
RUN  yum -y install http://rpms.famillecollet.com/enterprise/remi-release-7.rpm
RUN  yum -y install --enablerepo=remi,remi-php73 php php-mbstring php-xml php-xmlrpc php-gd php-pdo php-pecl-mcrypt php-mysqlnd php-pecl-mysql

# MySQLが必要な場合は下記のコメントアウトを解除
# RUN yum -y install mysql

# php設定
COPY ./php.ini  /etc/php.ini

# httpd 起動
CMD ["/usr/sbin/httpd", "-DFOREGROUND"]