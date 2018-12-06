#Usage: /usr/local/bin/php-config7 [OPTION]
#Options:
#  --prefix            [/usr/local/php-7.2.12-pack/php-7.2.12]
#  --includes          [-I/usr/local/php-7.2.12-pack/php-7.2.12/include/php -I/usr/local/php-7.2.12-pack/php-7.2.12/include/php/main -I/usr/local/php-7.2.12-pack/php-7.2.12/include/php/TSRM -I/usr/local/php-7.2.12-pack/php-7.2.12/include/php/Zend -I/usr/local/php-7.2.12-pack/php-7.2.12/include/php/ext -I/usr/local/php-7.2.12-pack/php-7.2.12/include/php/ext/date/lib]
#  --ldflags           [ -L/usr/local/php-7.2.12-pack/libiconv-1.15/lib -L/usr/local/php-7.2.12-pack/libsodium/lib -L/usr/local/php-7.2.12-pack/libzip/lib]
#  --libs              [-lcrypt  -lc-client  -lzip -lz -lcrypt -lsodium -ledit -lncurses -lrt -lldap -lstdc++ -lcrypt -liconv -lpng -lz -lbz2 -lz -lrt -lm -ldl -lnsl  -lrt -lxml2 -lz -lm -lgssapi_krb5 -lkrb5 -lk5crypto -lcom_err -lssl -lcrypto -lcurl -lxml2 -lz -lm -lssl -lcrypto -lfreetype -lgssapi_krb5 -lkrb5 -lk5crypto -lcom_err -lssl -lcrypto -licui18n -licuuc -licudata -lm -licuio -lxml2 -lz -lm -lxml2 -lz -lm -lcrypt -lxml2 -lz -lm -lxml2 -lz -lm -lxml2 -lz -lm -lxml2 -lz -lm -lxml2 -lz -lm -lssl -lcrypto -lcrypt ]
#  --extension-dir     [/usr/local/php-7.2.12-pack/php-7.2.12/lib/php/extensions/no-debug-non-zts-20170718]
#  --include-dir       [/usr/local/php-7.2.12-pack/php-7.2.12/include/php]
#  --man-dir           [/usr/local/php-7.2.12-pack/php-7.2.12/php/man]
#  --php-binary        [/usr/local/php-7.2.12-pack/php-7.2.12/bin/php]
#  --php-sapis         [ cli fpm phpdbg cgi]
#  --configure-options [--prefix=/usr/local/php-7.2.12-pack/php-7.2.12 --with-iconv=/usr/local/php-7.2.12-pack/libiconv-1.15 --with-libzip=/usr/local/php-7.2.12-pack/libzip --enable-fpm --with-fpm-user=deploy --with-fpm-group=deploy --disable-short-tags --with-openssl --with-pcre-regex --with-pcre-jit --with-zlib --enable-bcmath --with-bz2 --enable-calendar --with-curl --enable-exif --enable-ftp --with-gd --with-freetype-dir --with-gettext --with-mhash --with-imap --enable-intl --with-ldap --enable-mbstring --with-mysqli --enable-pcntl --with-pdo-mysql --with-libedit --with-readline --enable-soap --enable-sockets --with-sodium=/usr/local/php-7.2.12-pack/libsodium --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-wddx --with-xmlrpc --enable-zip --with-kerberos --with-imap-ssl]
#  --version           [7.2.12]
#  --vernum            [70212]

yum install -y libc-client-devel
ln -s /usr/lib64/libc-client.so /usr/lib/libc-client.so

# 使用libzip0.11 https://github.com/nih-at/libzip
# 如果提示zipconf.h找不到, 在libzip下搜索, 然后在/usr/local/include 下做个软链
# ln -s /usr/local/lib/libzip/include/zipconf.h /usr/local/include

# 安装 libiconv
# http://ftp.gnu.org/pub/gnu/libiconv/
