# sag.oscf : Semi Automated Generation of Open Source Compliance Files

An *Open Source Compliance File* is a file containing all compliance artifacts which must be handed over together with a product for distributing this product compliantly and legally, that is: in accordance with the affected Open Source Licenses. Thus, if one had a procedure to create the OSCF of a product, one would only have to bundle this OSCF together with the product.

**`sag.oscf` offers a set of scripts to create such a product specific OSCF semi-automatically. Information must be gathered (semi-) manually. But the license knowledge is implemented in the knowledge engine! You need no longer to be a license expert to create a license fulfilling OSCF.**

To summarize the working steps:

1. The developers list all open source components embedded / used in the product and classify these components with respect to way the use it. For this, they use a *Normalized Bill Of Material* [ nbom.csv = a CSV file with 5 defined columns. ]
2. `sag.oscf` creates the corresponding prefilled *sbom.md* file. (In this context, sbom  does not stand for *software bill of material*, but for *specified bill of material* - because we, as German - do not want to talk about *ssbom*.)
3. The open source supervisors of the project gather the missed information into the  *sbom.md*, meaning: the fill the empty columns of the *sbom.md*. (Markdown ist a good format to aggregate even links etc.)
4. `sag.oscf` derives the corresponding sbom.csv file.
5. By using 3 sub steps, the *License-Knowledge-Engine* of `sag.oscf` derives the corresponding *task-tagged oscf.md* from the information gathered in the sbom.csv
6. The Open-Source-Supervisor and his assistants expand the *task-tagged oscf.md* in accordance with taks described in this `tt-oscf.md`.
7. They convert the created oscf.md into a pdf file which can be bundeled with the product.

The advantage of this procedure is, that the gathering people do not have to know anything about the licenses and its legal requirements. They can focus on gathering the information necessary to fulfill the license conditiond. And which information are necessary, is defined by the files `nbom.csv` and `sbom.md`.

Thus, by using `sag.oscf`you can reduce the workload of your FOSS license experts and can decouple the product development from eye of needle 'OSPO'

These are the working steps described in detail:

## 1. create a normalized BOM by using the csv format_

The normalized BOM (nbom.csv) contains information necessary to create the compliance artifacts which a developer can and must manually gather from his work. It contains 5 columns separated  by a ';':

* $1 = CNAME = component name
* $2 = RelNr = release number
* $3 = CType =
  - app,
  - dll [= dynamically linked library ]
  - sll [= statically linked library ]
  - isf [= included source file ]
* $4 = PStat =
  - rap [= required as preinstalled component],
  - unm [= unmodified foss component becomes part of the distributable package]
  - mod [= modified foss component becomes part of the distributable package]
* $5 = SPDX License ID

## 2. let the prefilled specified bom markdown file be created

Example: `./bin/nbom.csv-2-sbom.md.sh demos/tc12.nbom.csv > pre.sbom.md`


## 3. complete the prefilled specified bom markdown file

The derived file contains a partially prefilled markdown table. The empty colums must manually be filled by gathering the required information from the web.

Please name the finally filled file your `sbom.md`. The colums, it requires are these:

* NR : current number
* CNAME : component name = $1
* MpUrl : Main Project Url
* RelNr : release number = $2
* CTYPE : see above
* PSTAT : see above
* RepoUrl: Url of the repository containing the license info
* SpdxId : Spdx Identifier of the license
* LSTUrl : Url of the file containing the license / licensing statement
* LSTType :
  - stal [ stand alone license text file ]
  - embl [ license text embedded into a file header ]
  - olst [ only a licensing statement without a license text]

Example: `cp demos/tc12.sbom.md > sbom.md`

## 4. let the processable sbom.csv file be derived

Example: `./bin/sbom.md-2-sbom.csv.sh sbom.md > sbom.csv`

The knowledge engine is a php file, taking the necessary information as csv file, which is automatically be derived from the created *sbom.md*

## 5. let the OSCF core snippet and the reports be derived

by using `./bin/sbom.csv-2-oscf.snippets.php sbom.csv > oscf.core.md`

Note: The script `sbom.csv-2-oscf.snippets.php` applies the license knowledge to the gathered information. It is something like an expert system. It creates

* the `oscf.core.md`
* the `oscf.index.md` which links the index to the chapters
* the `oscf.report.sh` which collects the names of the multiply used licenses

## 6. let the final task tagged OSCF be compiled

by using the command `./bin/link.oscf.md.sh oscf.core.md`

This script uses the file from step 5 together with some files / data stored in  `./bin/snippets`, as the general oscf-header, the written offer text, and the license text in md format.

## 7. do what the *task tagged OSCF* tells you to do

before your convert your resolved *tt-oscf.md* into your PDF file.

Licensing statement:

```
(c) 2022 Karsten Reincke, Deutsche Telekom

The project 'sag.oscf' is licensed under the MIT license
```
