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
# 下载最新kodbox安装包到web下
sed -i "s/MyISAM/InnoDB/g" /toolkit/source/KodBox/web/app/controller/install/data/mysql.sql
cp -ar .kodbox/.web/* /toolkit/source/KodBox/web/*
```

构建指定版本和spk套件名称
```bash
/toolkit/pkgscripts-ng/PkgCreate.py -v 7.2 -p avoton -c KodBox
```
