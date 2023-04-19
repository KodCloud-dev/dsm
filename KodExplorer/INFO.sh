#!/bin/bash
# Copyright (c) 2015-2022 KodCloud Inc. All rights reserved.

package="KodExplorer"

. "/pkgscripts-ng/include/pkg_util.sh" 
version="4.51-702304" 
os_min_ver="7.0-40000"
startstop_restart_services="nginx.service" 
instuninst_restart_services="nginx.service" 
install_dep_packages="WebStation>=3.0.0-0323:PHP7.4>=7.4.28-0117:Apache2.4>=2.4.46-0122"
install_provide_packages="WEBSTATION_SERVICE"
displayname="KodExplorer"
displayname_enu="KodExplorer" 
displayname_cht="可道雲KodExplorer"
displayname_chs="可道云KodExplorer"
maintainer="KodCloud"
dsmappname="SYNO.SDS.KodExplorer"
thirdparty="yes"
silent_upgrade="yes"
arch="noarch"
reloadui="yes"
adminprotocol="http"
adminport="80"
adminurl="kodexplorer"
dsmuidir="ui"
description="KodExplorer是可道云经典版本, 是一个开源的基于Web的在线文件管理、代码编辑器。此套件可让您在 DiskStation 上快速完成私有云/私有网盘/在线文档管理系统的部署和搭建。"
description_enu="KodExplorer is an open source web-based file management, code editor. The package enables you to quickly complete the deployment and setup of a private cloud / private cloud disk / online document management system on your DiskStation."
description_cht="KodExplorer是可道雲經典版本, 是一個開源的基於Web的線上文件管理、程式碼編輯器。此套件可讓您在DiskStation上快速完成私有雲/私有網盤/線上檔案管理系統的部署和搭建。"
description_chs="KodExplorer是可道云经典版本, 是一个开源的基于Web的在线文件管理、代码编辑器。此套件可让您在 DiskStation 上快速完成私有云/私有网盘/在线文档管理系统的部署和搭建。"
maintainer_url="https://kodcloud.com/"
support_url="https://bbs.kodcloud.com/"
helpurl="https://bbs.kodcloud.com/"

[ "$(caller)" != "0 NULL" ] && return 0
pkg_dump_info
