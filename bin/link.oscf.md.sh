#!/bin/bash

# (c) 2022 Karsten Reincke, Deutsche Telekom
#
# This file is licensed under the MIT license

if [ "$1" == "" -o "$1" == "-h" -o "$1" == "--help" ]; then
  echo "usage: link-oscf-md.sh path-of-oscf-snippet"; exit 0;
fi


IFILE=$1

TOOLDIR="bin"
SNIPDIR=${TOOLDIR}/snippets

OSCFHEADER=${SNIPDIR}/oscf.header.md
OSCFWOFFER=${SNIPDIR}/oscf.writtenoffer.md
OSCFC3HL=${SNIPDIR}/oscf.c3hl.md
OSCFC4HL=${SNIPDIR}/oscf.c4hl.md


oscfIfile="oscf.index.md"
oscfRfile="oscf.report.sh"
WOF=""

source ${oscfRfile}

if [ "$USEWRITTENOFFER" == "yes" ]; then WOF=${OSCFWOFFER}; fi

CLF=""

for f in ${USELICSNIPPETS}
do
  CLF="$CLF ${SNIPDIR}/$f"
done

CATFILES="$OSCFHEADER $WOF ./$oscfIfile $OSCFC3HL $IFILE $OSCFC4HL $CLF"


cat $CATFILES > tt.oscf.md

exit 0
