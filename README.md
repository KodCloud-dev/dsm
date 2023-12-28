## 套件开发说明

### 初始化开发环境 

> 参考[synology文档](https://help.synology.com/developer-guide/getting_started/prepare_environment.html)

```bash
mkdir -p /toolkit
cd /toolkit
git clone https://github.com/SynologyOpenSource/pkgscripts-ng
cd /toolkit/pkgscripts-ng/ 
git checkout DSM7.2
./EnvDeploy -v 7.2 -p avoton
```

### 准备构建spk

```bash
git clone https://github.com/KodCloud-dev/dsm.git /toolkit/source
mkdir /toolkit/source/KodBox/web
cd /toolkit/source/KodBox/web
curl -L "https://api.kodcloud.com/?app/version&download=server.link" -o kodbox.zip
unzip kodbox.zip && rm -f kodbox.zip
sed -i "s/MyISAM/InnoDB/g" /toolkit/source/KodBox/web/app/controller/install/data/mysql.sql
cp -ar /toolkit/source/.kodbox/.web/* /toolkit/source/KodBox/web/
```

构建指定版本和spk套件名称
```bash
/toolkit/pkgscripts-ng/PkgCreate.py -v 7.2 -p avoton -c KodBox
```
