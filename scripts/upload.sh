mkdir tempdir
cp . -R tempdir
cd tempdir
rm -rf `find . -name *.svn*`
VER=`cat phpwebbuilder-version`
rm -rf simpletest
F=phpwebbuilder-$VER-`date +%F`
tar cvzf ../$F.tgz *
zip -r ../$F.zip * 
cd ..
rm -rf tempdir
firefox http://sourceforge.net/project/admin/editpackages.php?group_id=153123
wput $F.zip ftp://anonymous@upload.sourceforge.net/incoming/
wput $F.tgz ftp://anonymous@upload.sourceforge.net/incoming/
rm $F.tgz
rm $F.zip
