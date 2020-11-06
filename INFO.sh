#!/bin/bash
# Copyright (c) 2000-2016 Synology Inc. All rights reserved.

package="kodexplorer"

. "/pkgscripts-ng/include/pkg_util.sh" 
version="4.40" 
os_min_ver="7.0-40337" 
startstop_restart_services="nginx.service" 
instuninst_restart_services="nginx.service" 
install_dep_packages="WebStation>=3.0.0-0226:PHP7.3>=7.3.16-0150" 
install_provide_packages="WEBSTATION_SERVICE" 
displayname_enu="KodExplorer" 
displayname_cht="可道雲KodExplorer"
displayname_chs="可道云KodExplorer"
maintainer="KodCloud"
thirdparty="yes"
silent_upgrade="yes"
arch="noarch"
reloadui="yes"
dsmuidir="ui"
description_enu="KodExplorer is a classic , simple and lightweight cloud storage system"
description_cht="KodExplorer是可道雲經典版本，一款簡潔輕便的雲存儲系統"
description_chs="KodExplorer是可道云经典版本，一款简洁轻便的云存储系统"
maintainer_url="https://kodcloud.com/"
support_url="https://bbs.kodcloud.com/"
helpurl="https://bbs.kodcloud.com/"

[ "$(caller)" != "0 NULL" ] && return 0
pkg_dump_info
