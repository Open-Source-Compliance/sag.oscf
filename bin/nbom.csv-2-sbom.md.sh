#!/bin/bash

# (c) 2022 Karsten Reincke, Deutsche Telekom
#
# This file is licensed under the MIT license

if [ "$1" == "" -o "$1" == "-h" -o "$1" == "--help" ]; then
  echo "usage: cv2-to-pf-sbom-md.sh path-of-the-normalized-unspecified-bom-in-csv-file"; exit 0;
fi

IFILE=$1

# the input file must be a csv file (4 columns) separated  by a ';'
# each line should have the column / field structure:
# -----
# $1 = CNAME = component name
# $2 = RelNr = release number
# $3 = CType = {
# - app,
# - dll [= dynamically linked library ]
# - sll [= statically linked library ]
# - isf [= included source file ]
# }
# $4 = PStat = {
# - rap [= required as preinstalled component],
# - unm [= unmodified foss component becomes part of the distributable package]
# - mod [= modified foss component becomes part of the distributable package]
# }
# $5 = SPDX License ID
# -----------------

# The fields of the output of this converter have the following meaning:
# NR : current number
# CNAME : component name = $1
# MpUrl : Main Project Url
# RelNr : release number = $2
# CTYPE : see above
# PSTAT : see above
# RepoUrl: Url of the repository containing the license infos
# SpdxId : Spdx Identifier of the license
# LSTUrl : Url to the licens (ing statement)
# LSTType :
# - stal [ stand alone license text file ]
# - embl [ license text embedded into a file header ]
# - olst [ only a licensing statement without a license text]

echo "|NR|\[CName\](MpUrl)|RelNr|CType = {app, dll, sll,isf}|PStat = {rap, unm, mod}|\[RepoUrl\](RepoUrl)|\[SpdxId\](LSTUrl)|LSTType = {stal, embl, olst}|NFile = {irr, no, \[yes](NFUrl)}|"
echo "|---|---|---|---|---|---|---|---|---|"

awk -F ";" '{print "|"NR"|["$1"]()|"$2"|"$3"|"$4"|[]()|["$5"]()|stal|irr|"}' ${IFILE}

exit 0
