#!/usr/bin/env sh
F=$(basename $1);
T=$(basename $2);
cp -r "catalog/view/theme/${F}" "catalog/view/theme/${T}" &&
( find "catalog/view/theme/${T}" -type f -iname '*.tpl' -print0 &&
find "catalog/view/theme/${T}" -type f -iname '*.css' -print0 ) | xargs -0 sed -e 's#theme/'${F}'#theme/'${T}'#g' -i
