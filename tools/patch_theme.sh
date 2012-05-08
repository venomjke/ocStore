#!/usr/bin/env sh

cat theme.default.patch | sed -e 's#theme/default#theme/'${1}'#g' | patch -p4 -l -N --no-backup-if-mismatch -E
