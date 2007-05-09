#!/bin/bash
#for i in `ls src/*.php`; do
#  cat $i | grep -v "<?php" | grep -v "?>" | lgrind -lphp -i - > $i.tex;
#done
#cat src/consulta.sql | lgrind -lsql -i - > src/consulta.sql.tex;

pdflatex presentation;
bibtex presentation;
pdflatex presentation;
