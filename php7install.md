Usage: ./php-config [OPTION]
Options:
  --prefix            [/usr/local/php-package/php-7.3]
  --includes          [-I/usr/local/php-package/php-7.3/include/php -I/usr/local/php-package/php-7.3/include/php/main -I/usr/local/php-package/php-7.3/include/php/TSRM -I/usr/local/php-package/php-7.3/include/php/Zend -I/usr/local/php-package/php-7.3/include/php/ext -I/usr/local/php-package/php-7.3/include/php/ext/date/lib]
  --ldflags           [ -L/usr/local/php-package/libiconv/lib -L/usr/local/php-package/libsodium/lib -L/usr/local/php-package/libzip/lib]
  --libs              [-lcrypt  -lc-client  -lzip -lzip -lz -lcrypt -lsodium -ledit -lncurses -lrt -lldap -lstdc++ -lcrypt -liconv -lpng -lz -lbz2 -lz -lrt -lm -ldl -lnsl  -lrt -lxml2 -lz -lm -lgssapi_krb5 -lkrb5 -lk5crypto -lcom_err -lssl -lcrypto -lcurl -lxml2 -lz -lm -lssl -lcrypto -lfreetype -lgssapi_krb5 -lkrb5 -lk5crypto -lcom_err -lssl -lcrypto -licui18n -licuuc -licudata -lm -licuio -lxml2 -lz -lm -lxml2 -lz -lm -lcrypt -lxml2 -lz -lm -lxml2 -lz -lm -lxml2 -lz -lm -lxml2 -lz -lm -lxml2 -lz -lm -lssl -lcrypto -lcrypt ]
  --extension-dir     [/usr/local/php-package/php-7.3/lib/php/extensions/no-debug-non-zts-20180731]
  --include-dir       [/usr/local/php-package/php-7.3/include/php]
  --man-dir           [/usr/local/php-package/php-7.3/php/man]
  --php-binary        [/usr/local/php-package/php-7.3/bin/php]
  --php-sapis         [ cli fpm phpdbg cgi]
  --configure-options [--prefix=/usr/local/php-package/php-7.3 --with-iconv=/usr/local/php-package/libiconv --with-libzip=/usr/local/php-package/libzip --enable-fpm --with-fpm-user=deploy --with-fpm-group=deploy --disable-short-tags --with-openssl --with-pcre-regex --with-pcre-jit --with-zlib --enable-bcmath --with-bz2 --enable-calendar --with-curl --enable-exif --enable-ftp --with-gd --with-freetype-dir --with-gettext --with-mhash --with-imap --enable-intl --with-ldap --enable-mbstring --with-mysqli --enable-pcntl --with-pdo-mysql --with-libedit --with-readline --enable-soap --enable-sockets --with-sodium=/usr/local/php-package/libsodium --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-wddx --with-xmlrpc --enable-zip --with-kerberos --with-imap-ssl]
  --version           [7.3.6]
  --vernum            [70306]


yum install -y libc-client-devel
ln -s /usr/lib64/libc-client.so /usr/lib/libc-client.so

# 安装 yum install -y libtool libsysfs-devel
# 使用libzip0.11 https://github.com/nih-at/libzip
# 如果提示zipconf.h找不到, 在libzip下搜索, 然后在/usr/local/include 下做个软链(一定要做这一步)
# ln -s /usr/local/lib/libzip/include/zipconf.h /usr/local/include
# 编译libzip
aclocal
autoheader
libtoolize
autoconf
automake --add-missing


# 安装 libiconv
# http://ftp.gnu.org/pub/gnu/libiconv/
