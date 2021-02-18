#!/bin/bash
# Copyright (c) 2000-2016 Synology Inc. All rights reserved.

package="KodExplorer"

. "/pkgscripts-ng/include/pkg_util.sh" 
version="4.40-2019" 
os_min_ver="7.0-40337" 
startstop_restart_services="nginx.service" 
instuninst_restart_services="nginx.service" 
install_dep_packages="WebStation>=3.0.0-0226:PHP7.3>=7.3.16-0150:Apache2.2>=2.2.34-0104" 
install_provide_packages="WEBSTATION_SERVICE"
displayname="KodExplorer"
displayname_enu="KodExplorer" 
displayname_cht="可道雲KodExplorer"
displayname_chs="可道云KodExplorer"
maintainer="KodCloud"
thirdparty="yes"
silent_upgrade="yes"
arch="noarch"
reloadui="yes"
dsmuidir="web"
beta="yes"
dsmappname="SYNO.SDS.KodExplorer"
description="KodExplorer是可道云经典版本，是一个公开源码的基于Web的在线文件管理、代码编辑器。此套件可让您在 DiskStation 上快速完成私有云/私有网盘/在线文档管理系统的部署和搭建。"
description_enu="KodExplorer is an open source web-based file management, code editor. The package enables you to quickly complete the deployment and setup of a private cloud / private cloud disk / online document management system on your DiskStation."
description_cht="KodExplorer是可道雲經典版本，是一個公開源碼的基於Web的線上文件管理、程式碼編輯器。此套件可讓您在DiskStation上快速完成私有雲/私有網盤/線上檔案管理系統的部署和搭建。"
description_chs="KodExplorer是可道云经典版本，是一个公开源码的基于Web的在线文件管理、代码编辑器。此套件可让您在 DiskStation 上快速完成私有云/私有网盘/在线文档管理系统的部署和搭建。"
maintainer_url="https://kodcloud.com/"
support_url="https://bbs.kodcloud.com/"
helpurl="https://bbs.kodcloud.com/"

[ "$(caller)" != "0 NULL" ] && return 0
pkg_dump_info
