#!/bin/bash
for i in `ls src/*.php`; do
  cat $i | grep -v "<?php" | grep -v "?>" | lgrind -lphp -i - > $i.tex;
done
pdflatex presentation;
bibtex presentation;
pdflatex presentation;
