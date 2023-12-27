#!/bin/bash
# Copyright (c) 2015-2022 KodCloud Inc. All rights reserved.

package="KodBox"

. "/pkgscripts-ng/include/pkg_util.sh" 
version="1.49-702312" 
os_min_ver="7.0-40000"
startstop_restart_services="nginx.service" 
instuninst_restart_services="nginx.service" 
install_dep_packages="WebStation>=3.0.0-0323:MariaDB10:PHP8.2>=8.2.5-0102:Apache2.4>=2.4.46-0122"
install_provide_packages="WEBSTATION_SERVICE"
displayname="KodBox"
displayname_enu="KodBox" 
displayname_cht="可道雲KodBox"
displayname_chs="可道云KodBox"
maintainer="KodCloud"
thirdparty="yes"
silent_upgrade="yes"
arch="noarch"
reloadui="yes"
adminprotocol="http"
adminport="80"
adminurl="kodbox"
dsmuidir="ui"
description="KodBox 是一款强大的私有云盘/企业网盘管理系统，采用的技术为 PHP 及 MySQL。此套件可让您在 Synology NAS 上架设 私有云盘。"
description_enu="KodBox is a powerful private cloud disk/enterprise network disk management system, which uses PHP and MySQL technologies. This suite allows you to set up private cloud disks on the Synology NAS."
description_cht="KodBox是一款强大的私有雲盤/企業網盤管理系統, 採用的科技為PHP及MySQL。 此套件可讓您在Synology NAS上架設私有雲盤。"
description_chs="KodBox 是一款强大的私有云盘/企业网盘管理系统, 采用的技术为 PHP 及 MySQL。此套件可让您在 Synology NAS 上架设 私有云盘。"
maintainer_url="https://kodcloud.com/"
support_url="https://bbs.kodcloud.com/"
helpurl="https://bbs.kodcloud.com/"

[ "$(caller)" != "0 NULL" ] && return 0
pkg_dump_info
