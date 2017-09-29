

# 初始化依赖
yum install -y curl-devel expat-devel gettext-devel openssl-devel zlib-devel asciidoc xmlto docbook2x perl-devel

# 如果没有docbook2x-texi的话, 因为这软件改名了
ln -s /usr/bin/db2x_docbook2texi /usr/bin/docbook2x-texi

# 安装libiconv
wget https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.15.tar.gz
tar zxvf libiconv-1.15.tar.gz
cd libiconv-1.15
./configure --prefix=/usr/local
make
make install
cd ..

# 连接动态库libiconv
echo /usr/local/lib > /etc/ld.so.conf.d/libiconv.conf
ldconfig

# 安装
git clone git://git.kernel.org/pub/scm/git/git.git

cd git
git checkout v2.14.2 -b gitlatesttag

make configure
./configure --prefix=/usr
make all doc info
make install install-doc install-html install-info

git --version

