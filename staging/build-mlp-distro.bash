#!/bin/bash

BASEDIR=/home/.jeremia/programming/git-repos/mlp_pack.git
TARGET_DIR=$BASEDIR/staging/mlp
SOURCE_DIR=$BASEDIR
ZIP_DIR=$BASEDIR/staging
VERBOSE=""

sed_handle_BOM_marker()
{
echo -e '\E[31;40m'"\033[1mByte Order Mark detected in $1\033[0m"
hexdump -C -n 16 $1
exit 1
}

sed_BOM_check()
{
#	See if the referenced file has a utf-8 Byte-Order-Mark at the start...
	od -x $1 | grep 'bbef' > /dev/null && sed_handle_BOM_marker $1;
}


sed_build_plugins()
{
until [[ $1 == "" ]]
do
	OP_DOC=$TARGET_DIR/plugins/$1.txt
	wget -q --output-document=$OP_DOC plugins.local/latest/$1.php;
	sed_BOM_check $SOURCE_DIR/$1.php
	cp -iu $VERBOSE $SOURCE_DIR/$1.php $TARGET_DIR/plugins/sources
	shift
done
}

sed_copymlpfile()
{
until [[ $1 == "" ]]
do
	sed_BOM_check $SOURCE_DIR/lib/$1
	cp -iu $VERBOSE $SOURCE_DIR/lib/$1 $TARGET_DIR/textpattern/lib/
	shift
done
}


 
echo 
echo "Cleaning MLP staging area..."
rm -rd $VERBOSE $TARGET_DIR/textpattern
rm -rd $VERBOSE $TARGET_DIR/plugins
rm     $VERBOSE $TARGET_DIR/optional/textpattern/publish.php

echo "Creating MLP staging area directories..."
mkdir     $TARGET_DIR/textpattern
mkdir     $TARGET_DIR/textpattern/lib 
mkdir     $TARGET_DIR/textpattern/txp_img
mkdir     $TARGET_DIR/plugins
mkdir     $TARGET_DIR/plugins/sources

echo "Copying textpattern/lib files..."
cd $BASEDIR
sed_copymlpfile l10n_base.php l10n_admin.php l10n_admin_classes.php l10n_default_strings.php l10n_en-gb_strings.php l10n_el-gr_strings.php l10n.css l10n_langs.php txplib_db.php

echo "Copying optional textpattern files..."
sed_BOM_check $SOURCE_DIR/publish.php
cp -iu $VERBOSE $SOURCE_DIR/publish.php $TARGET_DIR/optional/textpattern

echo "Copying MLP Images..."
cp -iu $VERBOSE $SOURCE_DIR/txp_img/*.png  $TARGET_DIR/textpattern/txp_img

echo "Building MLP Plugins & Copying source..."
sed_build_plugins l10n gbp_admin_library zem_contact_lang-mlp

echo "Creating zip archive..."
cd $TARGET_DIR
PACK_VERSION=`head -n 1 ./plugins/l10n.txt | cut -c15- | tr -d [:blank:]`
PACK_NAME=mlp-${PACK_VERSION}.zip
#echo "Pack version: $PACK_VERSION"
#echo "Pack name:    $PACK_NAME"
zip -rT -q $ZIP_DIR/$PACK_NAME .
cd $ZIP_DIR

echo -e 'The latest version is: \E[37;40m'"\033[1m$PACK_NAME\033[0m"
echo 
