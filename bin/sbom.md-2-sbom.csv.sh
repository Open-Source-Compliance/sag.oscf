#!/bin/bash

# (c) 2022 Karsten Reincke, Deutsche Telekom
#
# This file is licensed under the MIT license

if [ "$1" == "" -o "$1" == "-h" -o "$1" == "--help" ]; then
  echo "usage: cv3-to-cf-sbom-2-csv.sh path-of-the-completely-filled-specified-bom-in-markdown-table-format"; exit 0;
fi

IFILE=$1

# After having converted the md tablet by the command
# sed -f conv.cmds $IFILE
# each line should have the column / field structure:
# -----
# $1 = current line nummber
# $2 = copmponent name
# $3 = coponenent homepage url
# $4 = release number
# $5 = component type (dll =dynamically-linked-lib | sll =statically-linked-lib | app)
# $6 = component modification status (yes | no)
# $7 = repository url
# $8 = repeated repository url (=> ignore it)
# $9 = SPDX License ID
# $10 = License file in Repository
# $11 = LicensingType
#       stal [=StandaloneLicTxt] |
#       embl [=embeddedLicTxt.] |
#       olst [=onlyLicensingStatement])
# $12 = Noticefile status (irr|no|yes)
# $13 = Noticefile path if $b/$a == yes
# -----------------
#
# ignoring the unwished columns is done by the awk command
# -> | awk -F ";" '{print $1";"$2";"$3";"$4";"$5";"$6";"$7";"$9";"$10";"$11";"$12}'

# we expect a md table = including the header, hence we use tail to overread it

tail -n +3 ${IFILE} | \
  sed -e "s/^|//" \
    -e "s/|/;/g" \
    -e "s/](/;/g" \
    -e "s/;\[/;/g" \
    -e "s/);/;/g" | \
  awk -F ";" \
  '{print $1";"$2";"$3";"$4";"$5";"$6";"$7";"$9";"$10";"$11";"$12";"$13}'

exit 0
