mkdir tempdir
cp . -R tempdir
cd tempdir
rm -rf `find . -name *.svn*`
VER=`cat phpwebbuilder-version`
F=phpwebbuilder-$VER-`date +%F`
tar cvzf ../$F.tgz *
zip -r ../$F.zip * 
cd ..
rm -rf tempdir
gftp anonymous:upload.sourceforge.net /incoming
firefox http://sourceforge.net/project/admin/editpackages.php?group_id=153123
rm $F.tgz
rm $F.zip
