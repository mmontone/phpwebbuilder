#!/bin/bash
for i in `ls src/*.php`; do
  lgrind -i -lphp $i > $i.tex;
done
pdflatex presentation;
bibtex presentation;
pdflatex presentation;
