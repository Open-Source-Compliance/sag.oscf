#!/bin/php

<?php

/* (c) 2022 Karsten Reincke, Deutsche Telekom
*
* This file is licensed under the MIT license
*/

/* $CompNumInd=0; value not used in this context */
$CompNameInd=1;
$CompName="";
/* $CompHomepageUrlInd=2; value not used in this context */
$CompReleaseNumInd=3;
$CompReleaseNum="";
/* $CompTypeInd=4; value not used in this context */
$CompModifiedStatusInd=5; /* if == rap required as preinstalled ignore entry */
$ignoreValue='rap';
$CompRepositoryUrlInd=6;
$CompRepositoryUrl="";
$CompSpdxIdInd=7;
$CompSpdxId="";
$CompLicenseFileUrlInd=8;
$CompLicenseFileUrl="";
$CompLicensingTypeInd=9;
$CompLicensingType=""; /* expecting stal | embl | olst */
$CompNoticeFileMarkerInd=10;
$CompNoticeFileMarker=""; /* expecting irr | no | yes */
$CompNoticeFileUrlInd=11;
$CompNoticeFileUrl=""; /* filled if CompNoticeFileMarker = yes */

$generallyUsableLicenses=array();
$writtenOfferNecessary="no";

$oscfRfile="oscf.report.sh";
$oscfIfile="oscf.index.md";

function prepDerivation() {
  global $oscfRfile,$oscfIfile;
  if (file_exists($oscfRfile)) unlink($oscfRfile);
  if (file_exists($oscfIfile)) unlink($oscfIfile);
}

function evalArguments() {
  global $argv;
  global $argc;
  if (($argc <= 1)|| (($argv[1]=="-h")||($argv[1]=="--help"))) {
    print "usage: cv4-to-tt-oscf-md.php path-to-completely-filled-qualified-bom-csv-file\n";
    exit(-1);
  }
  return $argv[1];
}

function toInternalLinkName($compName) {
  return strtolower(preg_replace('/\s+/','_',$compName));
}

function todoInsertFileTextLiterally($url) {
  echo "\n**TODO:** Insert the complete content of the (respective) file (from)\n";
  echo "[${url}](${url})\n";
  echo "```\njust here within this three tick-marks-section!\n```\n";
}

function todoInsertCopyrightLines($repourl) {
  echo "\n**TODO:** Grep all Copyright-Lines from all files from the repository\n";
  echo "[${repourl}](${repourl})\n";
  echo "and insert the result\n";
  echo "```\njust here within this three tick-marks-section!\n```\n";
}

function todoInsertLicenseTextLiterally($licTextUrl,$licTextType) {
  switch($licTextType) {
    case 'olst':
      echo "    Note: The repository only contains a licensing statement!\n";
      echo "\n**TODO:** Insert the complete licensing statement from file\n";
      echo "[${licTextUrl}](${licTextUrl})\n";
      echo "```\njust here within this three tick-marks-section!\n```\n";
      break;
    case 'embl':
      echo "\n**TODO:** Extract the embedded license text from file\n";
      echo "[${licTextUrl}](${licTextUrl})\n";
      echo "(including all possibly associated copyright lines)\n";
      echo "and insert it\n";
      echo "```\njust here within this three tick-marks-section!\n```\n";
      break;
    case 'stal':
    default:
      echo "\n**TODO:** Insert the complete content of the license file\n";
      echo "[${licTextUrl}](${licTextUrl})\n";
      echo "(including all possibly associated copyright lines)\n";
      echo "```\njust here within this three tick-marks-section!\n```\n";
  }
}

function noteUsedLicense($licID,$woRequired){
  global $generallyUsableLicenses;
  global $writtenOfferNecessary;
  $generallyUsableLicenses[$licID]= strtolower($licID).".md";
  if ($woRequired==true)
    $writtenOfferNecessary="yes";
}

function writeOscfEntry(
  $chapterNumber,
  $entryNumber,
  $component,
  $releaseNumber,
  $repositoryUrl,
  $spdxID,
  $licenseTextUrl,
  $licensingType,
  $noticeTextMarker,
  $noticeFileUrl
) {

  printf("<a name=\"%s\"></a>\n",toInternalLinkName($component));
  echo "### ${chapterNumber}.${entryNumber} Package: ${component}\n";
  echo "- Release: ${releaseNumber}\n";
  echo "- Repository: [${repositoryUrl}](${repositoryUrl})\n";
  echo "- Scope: Default\n";
  echo "  - LicenseID: ${spdxID}\n";
  switch ($spdxID) {
    case 'BSD-2-Clause':
    case 'BSD-3-Clause':
    case 'MIT':
    case 'ISC':
    case 'PSF-2.0':
    case 'Zlib':
    case 'curl':
    case 'bzip2-1.0.6':
      echo "  - Licensetext:\n";
      todoInsertLicenseTextLiterally($licenseTextUrl,$licensingType);
      break;
  case 'Apache-2.0':
      echo "  - LicenseText: see [the 'once for all' license text at the end of this compliance file](#Apache-2.0)\n";
      noteUsedLicense($spdxID,false);
      if (($noticeTextMarker == "irr")||($noticeTextMarker == "no")) {
        echo "  - NoticeFile: does not exist in the repository\n\n";
      }
      else {
        echo "  - NoticeFile:\n";
        todoInsertFileTextLiterally($noticeFileUrl);
      }
      break;
    case 'BSL-1.0':
      echo "  - LicenseText: see [the 'once for all' license text at the end of this compliance file](#$spdxID)\n\n";
      noteUsedLicense($spdxID,true);
      todoInsertCopyrightLines($repositoryUrl);
      break;
    case 'MPL-1.1':
    case 'MPL-2.0':
      echo "  - LicenseText: see [the 'once for all' license text at the end of this compliance file](#$spdxID)\n\n";
      noteUsedLicense($spdxID,true);
      break;
    case 'LGPL-2.1-only':
    case 'LGPL-2.1-or-later':
      echo "  - LicenseText: see [the 'once for all' license text at the end of this compliance file](#LGPL-2.1)\n\n";
      noteUsedLicense("LGPL-2.1",true);
      todoInsertCopyrightLines($repositoryUrl);
      break;
    case 'LGPL-3.0-only':
    case 'LGPL-3.0-or-later':
      echo "  - LicenseText: see [the 'once for all' license text at the end of this compliance file](#LGPL-3.0)\n\n";
      noteUsedLicense("LGPL-3.0",true);
      todoInsertCopyrightLines($repositoryUrl);
      break;
    case 'GPL-2.0-only':
    case 'GPL-2.0-or-later':
      echo "  - LicenseText: see [the 'once for all' license text at the end of this compliance file](#GPL-2.0)\n\n";
      noteUsedLicense("GPL-2.0",true);
      todoInsertCopyrightLines($repositoryUrl);
      break;
    case 'GPL-3.0-only':
    case 'GPL-3.0-or-later':
      echo "  - LicenseText: see [the 'once for all' license text at the end of this compliance file](#GPL-3.0)\n\n";
      noteUsedLicense("GPL-3.0",true);
      todoInsertCopyrightLines($repositoryUrl);
      break;
    case 'Public Domain':
    case 'PDD':
      echo "  - LicenseText: not necessary, dedicated to Public Domain\n\n";
      break;
    default:
      echo "  - Licensetext: unknown\n\n";
      // code...
      break;
  }

}

$infilePath=evalArguments();

$compIndex=array();
$compRow=array();

prepDerivation();

if(file_exists($infilePath) && ($csvFile = fopen($infilePath,"r")) !== false) {

  $i = 0;
  while(($row = fgetcsv($csvFile, 0,";")) !== false) {
    if ($row[$CompModifiedStatusInd]!=$ignoreValue) {
      $CompName=$row[$CompNameInd];
      $CompReleaseNum=$row[$CompReleaseNumInd];
      $CompSatus=$row[$CompModifiedStatusInd];
      $CompRepositoryUrl=$row[$CompRepositoryUrlInd];
      $CompSpdxId=$row[$CompSpdxIdInd];
      $CompLicenseFileUrl=$row[$CompLicenseFileUrlInd];
      $CompLicensingType=$row[$CompLicensingTypeInd];
      $CompNoticeFileMarker=$row[$CompNoticeFileMarkerInd];
      if ($CompNoticeFileMarker=="yes") {
        $CompNoticeFileUrl=$row[$CompNoticeFileUrlInd];
      }
      writeOscfEntry(
        3,
        ($i+1),
        $CompName,
        $CompReleaseNum,
        $CompRepositoryUrl,
        $CompSpdxId,
        $CompLicenseFileUrl,
        $CompLicensingType,
        $CompNoticeFileMarker,
        $CompNoticeFileUrl
      );
      $compRow=array('pkg'=>$CompName,'rel'=>$CompReleaseNum,'lic'=>$CompSpdxId);
      $lines=array_push($compIndex,$compRow);
      $i++;
    }
  }
  fclose($csvFile);
}


if (($indexFile = fopen(($oscfIfile),"w")) !== false) {
  fprintf($indexFile,"\n## 2. Index of the included FOSS packages\n\n");

  foreach ($compIndex as $compRow) {
    fprintf($indexFile,
      "- [%s](#%s) %s %s\n",
      $compRow['pkg'],
      toInternalLinkName($compRow['pkg']),
      $compRow['rel'],
      $compRow['lic']);
  }
  fclose($indexFile);
} else {
  echo "can't write indexfile ${oscfIfile}\n";
  exit;
}

if (($reportFile = fopen($oscfRfile,"w")) !== false) {
  fprintf($reportFile,"#!/bin/bash\n\n");
  fprintf($reportFile,"USELICSNIPPETS=\"");
  foreach($generallyUsableLicenses as $spdxID => $fileName) {
    fprintf($reportFile,"$fileName ");
  }
  fprintf($reportFile,"\"\n");
  fprintf($reportFile,"USEWRITTENOFFER=%s\n",$writtenOfferNecessary);
  fclose($reportFile);
}
exit;

?>
