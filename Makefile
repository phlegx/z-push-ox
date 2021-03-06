CP = cp
RM = rm
MV = mv
GIT = git
ALIEN = alien
BUILDPACKAGE = dpkg-buildpackage

.PHONY: deb rpm

install:

build:

deb:
	$(GIT) log -1 --format="%H" > git_revision
	$(CP) changelog debian/changelog
	$(BUILDPACKAGE)
	$(RM) debian/changelog

rpm: deb
	$(ALIEN) --keep-version --to-rpm ../z-push-ox*.deb
	$(MV) z-push-ox*.rpm ../
