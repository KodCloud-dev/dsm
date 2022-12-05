KODBOXDIR=web
KODBOX_INSTALL_DIR=$(INSTALLDIR)/$(KODBOXDIR)

all clean:

.PHONY:

install:
	[   -d  $(INSTALLDIR)   ]   ||  install -d  $(INSTALLDIR)
	[   -d  $(KODBOX_INSTALL_DIR)   ]   ||  install -d  $(KODBOX_INSTALL_DIR)
	cp  -a  $(KODBOXDIR)/*  $(KODBOX_INSTALL_DIR)

	[   -d  $(INSTALLDIR)/ui    ]   ||  install -d  $(INSTALLDIR)/ui
	[   -d  $(INSTALLDIR)/synology_added   ]   ||  install -d  $(INSTALLDIR)/synology_added
	cp  -a  ui/*    $(INSTALLDIR)/ui
	cp  -a  synology_added/*    $(INSTALLDIR)/synology_added 

	chown   -R  http:http   $(KODBOX_INSTALL_DIR)
    
INFO:   INFO.sh
	env UISTRING_PATH=$(STRING_DIR) ./INFO.sh   >   INFO
    
package:    INFO
	[   -d  $(PACKAGEDIR)   ]   ||  install -d  $(PACKAGEDIR)
	[   -d  $(PACKAGEDIR)/scripts   ]   ||  install -d  $(PACKAGEDIR)/scripts
	cp  -a  scripts/*   $(PACKAGEDIR)/scripts
	chmod   755 $(PACKAGEDIR)/scripts/*
	
	cp  -a  PACKAGE_ICON.PNG    $(PACKAGEDIR)
	cp  -a  PACKAGE_ICON_256.PNG    $(PACKAGEDIR)
	cp  -a  conf $(PACKAGEDIR)
	install -c  -m  644 INFO    $(PACKAGEDIR)
    
clean:

