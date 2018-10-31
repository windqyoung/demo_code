./configure  --prefix=/apps/product/php/php-7.2.11 --with-iconv=/usr/local/libiconv --with-libzip=/apps/product/php/libzip  --enable-fpm  --with-fpm-user=deploy --with-fpm-group=deploy  --disable-short-tags --with-openssl --with-pcre-regex --with-pcre-jit --with-zlib --enable-bcmath --with-bz2 --enable-calendar --with-curl --enable-exif --enable-ftp --with-gd --with-freetype-dir --with-gettext --with-mhash --with-imap --enable-intl --with-ldap --enable-mbstring --with-mysqli --enable-pcntl --with-pdo-mysql --with-libedit --with-readline --enable-soap --enable-sockets --with-sodium=/apps/product/php/libsodium --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-wddx --with-xmlrpc --enable-zip   --with-kerberos --with-imap-ssl

yum install -y libc-client-devel
ln -s /usr/lib64/libc-client.so /usr/lib/libc-client.so

# 使用libzip0.11 https://github.com/nih-at/libzip
# 如果提示zipconf.h找不到, 在libzip下搜索, 然后在/usr/local/include 下做个软链
# ln -s /usr/local/lib/libzip/include/zipconf.h /usr/local/include

# 安装 libiconv
# http://ftp.gnu.org/pub/gnu/libiconv/
